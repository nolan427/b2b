<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $db = getDB();
    
    // Get query parameters
    $key = $_GET['key'] ?? null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

    // If specific key requested
    if ($key) {
        $stmt = $db->prepare("
            SELECT ip, email, device, mx_type, document_key, timestamp
            FROM visits
            WHERE document_key = ?
            ORDER BY timestamp DESC
            LIMIT ?
        ");
        $stmt->execute([$key, $limit]);
        
        $visits = [];
        while ($row = $stmt->fetch()) {
            $visits[] = [
                'ip' => $row['ip'],
                'email' => $row['email'],
                'device' => $row['device'],
                'mx_type' => $row['mx_type'],
                'document_key' => $row['document_key'],
                'timestamp' => $row['timestamp']
            ];
        }
        
        // Get total count and unique visitors
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM visits WHERE document_key = ?");
        $stmt->execute([$key]);
        $totalVisits = $stmt->fetch()['total'];
        
        $stmt = $db->prepare("SELECT COUNT(DISTINCT email) as unique_count FROM visits WHERE document_key = ?");
        $stmt->execute([$key]);
        $uniqueVisitors = $stmt->fetch()['unique_count'];

        echo json_encode([
            'success' => true,
            'document_key' => $key,
            'total_visits' => (int)$totalVisits,
            'unique_visitors' => (int)$uniqueVisitors,
            'visits' => $visits
        ]);
        exit;
    }

    // Get all visits grouped by key
    $stmt = $db->query("
        SELECT 
            document_key,
            COUNT(*) as total_visits,
            COUNT(DISTINCT email) as unique_visitors
        FROM visits
        GROUP BY document_key
    ");
    
    $visitsByKey = [];
    $totalVisits = 0;
    $deviceBreakdown = [];
    $mxTypeBreakdown = [];

    while ($row = $stmt->fetch()) {
        $docKey = $row['document_key'];
        
        // Get recent visits for this key
        $vstmt = $db->prepare("
            SELECT ip, email, device, mx_type, document_key, timestamp
            FROM visits
            WHERE document_key = ?
            ORDER BY timestamp DESC
            LIMIT 100
        ");
        $vstmt->execute([$docKey]);
        
        $keyVisits = [];
        while ($visit = $vstmt->fetch()) {
            $keyVisits[] = [
                'ip' => $visit['ip'],
                'email' => $visit['email'],
                'device' => $visit['device'],
                'mx_type' => $visit['mx_type'],
                'document_key' => $visit['document_key'],
                'timestamp' => $visit['timestamp']
            ];
        }
        
        $visitsByKey[$docKey] = [
            'total_visits' => (int)$row['total_visits'],
            'unique_visitors' => (int)$row['unique_visitors'],
            'visits' => $keyVisits
        ];
        
        $totalVisits += $row['total_visits'];
    }
    
    // Get device breakdown
    $stmt = $db->query("
        SELECT device, COUNT(*) as count
        FROM visits
        GROUP BY device
    ");
    while ($row = $stmt->fetch()) {
        $deviceBreakdown[$row['device']] = (int)$row['count'];
    }
    
    // Get MX type breakdown
    $stmt = $db->query("
        SELECT mx_type, COUNT(*) as count
        FROM visits
        GROUP BY mx_type
    ");
    while ($row = $stmt->fetch()) {
        $mxTypeBreakdown[$row['mx_type']] = (int)$row['count'];
    }

    echo json_encode([
        'success' => true,
        'total_visits' => $totalVisits,
        'total_keys' => count($visitsByKey),
        'device_breakdown' => $deviceBreakdown,
        'mx_type_breakdown' => $mxTypeBreakdown,
        'visits_by_key' => $visitsByKey
    ]);

} catch (Exception $e) {
    error_log('Error fetching visits: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch visits',
        'details' => $e->getMessage()
    ]);
}
?>
