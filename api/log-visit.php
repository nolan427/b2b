<?php
require_once __DIR__ . '/../config.php';

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
    
    // Load visit data
    $visits = loadData('visits');
    
    // Create visit record
    $visitId = 'visit_' . time() . '_' . bin2hex(random_bytes(4));
    $visitData = [
        'ip' => $ip,
        'email' => $data['email'],
        'device' => $data['device'],
        'mx_type' => $mxType,
        'document_key' => $data['document_key'] ?? 'unknown',
        'timestamp' => date('c')
    ];
    
    // Add to visits array
    $visits['visits'][$visitId] = $visitData;
    
    // Update stats
    if (!isset($visits['stats'])) {
        $visits['stats'] = [
            'total' => 0,
            'by_email' => [],
            'by_device' => [],
            'by_mx_type' => []
        ];
    }
    
    $visits['stats']['total']++;
    
    // Count by email
    if (!isset($visits['stats']['by_email'][$data['email']])) {
        $visits['stats']['by_email'][$data['email']] = 0;
    }
    $visits['stats']['by_email'][$data['email']]++;
    
    // Count by device
    if (!isset($visits['stats']['by_device'][$data['device']])) {
        $visits['stats']['by_device'][$data['device']] = 0;
    }
    $visits['stats']['by_device'][$data['device']]++;
    
    // Count by MX type
    if (!isset($visits['stats']['by_mx_type'][$mxType])) {
        $visits['stats']['by_mx_type'][$mxType] = 0;
    }
    $visits['stats']['by_mx_type'][$mxType]++;
    
    // Group by document key
    $docKey = $data['document_key'] ?? 'unknown';
    if (!isset($visits['by_key'][$docKey])) {
        $visits['by_key'][$docKey] = [
            'total_visits' => 0,
            'unique_emails' => [],
            'visit_ids' => []
        ];
    }
    $visits['by_key'][$docKey]['total_visits']++;
    $visits['by_key'][$docKey]['unique_emails'][$data['email']] = true;
    $visits['by_key'][$docKey]['visit_ids'][] = $visitId;
    
    // Save data
    saveData('visits', $visits);

    echo json_encode([
        'success' => true,
        'visit_id' => $visitId,
        'ip' => $ip,
        'mx_type' => $mxType,
        'document_key' => $docKey,
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

// ========== HELPER FUNCTION ==========

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
?>
