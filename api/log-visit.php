<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit;
    }

    // Validate required fields
    if (empty($data['email']) || empty($data['device']) || empty($data['mx_type'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing required fields: email, device, mx_type'
        ]);
        exit;
    }

    // Validate MX type
    $validMxTypes = ['office365', 'google', 'other'];
    $mxType = strtolower($data['mx_type']);
    if (!in_array($mxType, $validMxTypes)) {
        $mxType = 'other';
    }

    // Get IP address
    $ip = getClientIP();

    // Get Redis credentials
    $kvUrl = getenv('KV_REST_API_URL');
    $kvToken = getenv('KV_REST_API_TOKEN');

    if (!$kvUrl || !$kvToken) {
        throw new Exception('Redis credentials not configured');
    }

    // Create visit record
    $timestamp = round(microtime(true) * 1000);
    $visitId = 'visit:' . $timestamp . ':' . bin2hex(random_bytes(4));
    $visitData = [
        'ip' => $ip,
        'email' => $data['email'],
        'device' => $data['device'],
        'mx_type' => $mxType,
        'document_key' => $data['document_key'] ?? 'unknown',
        'timestamp' => date('c')
    ];

    // Store in Redis
    kvSetEx($visitId, 7776000, json_encode($visitData), $kvUrl, $kvToken); // 90 days

    // Add to global timeline
    kvZAdd('visits:timeline', $timestamp, $visitId, $kvUrl, $kvToken);

    // Group by document key
    if (!empty($data['document_key'])) {
        $docKey = $data['document_key'];
        kvZAdd("visits:key:$docKey", $timestamp, $visitId, $kvUrl, $kvToken);
        kvHIncrBy('visits:count_by_key', $docKey, 1, $kvUrl, $kvToken);
        kvSAdd("visits:key:$docKey:emails", $data['email'], $kvUrl, $kvToken);
    }

    // Global stats
    kvHIncrBy('visits:stats:by_email', $data['email'], 1, $kvUrl, $kvToken);
    kvHIncrBy('visits:stats:by_device', $data['device'], 1, $kvUrl, $kvToken);
    kvHIncrBy('visits:stats:by_mx_type', $mxType, 1, $kvUrl, $kvToken);
    kvIncr('visits:stats:total', $kvUrl, $kvToken);

    echo json_encode([
        'success' => true,
        'visit_id' => $visitId,
        'ip' => $ip,
        'mx_type' => $mxType,
        'document_key' => $data['document_key'] ?? 'unknown',
        'timestamp' => $visitData['timestamp']
    ]);

} catch (Exception $e) {
    error_log('Visit logging error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to log visit',
        'details' => $e->getMessage()
    ]);
}

// ========== HELPER FUNCTIONS ==========

function getClientIP() {
    $headers = [
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = $_SERVER[$header];
            if (strpos($ip, ',') !== false) {
                $ips = explode(',', $ip);
                $ip = trim($ips[0]);
            }
            return $ip;
        }
    }

    return 'unknown';
}

function kvSetEx($key, $seconds, $value, $kvUrl, $kvToken) {
    $url = $kvUrl . '/setex/' . urlencode($key) . '/' . $seconds . '/' . urlencode($value);
    return kvRequest($url, $kvToken);
}

function kvZAdd($key, $score, $member, $kvUrl, $kvToken) {
    $url = $kvUrl . '/zadd/' . urlencode($key) . '/' . $score . '/' . urlencode($member);
    return kvRequest($url, $kvToken);
}

function kvHIncrBy($key, $field, $increment, $kvUrl, $kvToken) {
    $url = $kvUrl . '/hincrby/' . urlencode($key) . '/' . urlencode($field) . '/' . $increment;
    return kvRequest($url, $kvToken);
}

function kvSAdd($key, $member, $kvUrl, $kvToken) {
    $url = $kvUrl . '/sadd/' . urlencode($key) . '/' . urlencode($member);
    return kvRequest($url, $kvToken);
}

function kvIncr($key, $kvUrl, $kvToken) {
    $url = $kvUrl . '/incr/' . urlencode($key);
    return kvRequest($url, $kvToken);
}

function kvRequest($url, $token) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("Redis request failed with code $httpCode");
    }

    $data = json_decode($response, true);
    if (isset($data['error'])) {
        throw new Exception('Redis error: ' . $data['error']);
    }

    return $data['result'] ?? null;
}
?>