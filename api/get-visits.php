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
    // Load data
    $visits = loadData('visits');
    
    // Get query parameters
    $key = $_GET['key'] ?? null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

    // If specific key requested
    if ($key) {
        if (!isset($visits['by_key'][$key])) {
            echo json_encode([
                'success' => true,
                'document_key' => $key,
                'total_visits' => 0,
                'unique_visitors' => 0,
                'visits' => []
            ]);
            exit;
        }

        $keyData = $visits['by_key'][$key];
        $visitIds = array_slice($keyData['visit_ids'], -$limit);
        
        $keyVisits = [];
        foreach ($visitIds as $visitId) {
            if (isset($visits['visits'][$visitId])) {
                $keyVisits[] = $visits['visits'][$visitId];
            }
        }

        echo json_encode([
            'success' => true,
            'document_key' => $key,
            'total_visits' => $keyData['total_visits'],
            'unique_visitors' => count($keyData['unique_emails']),
            'visits' => array_reverse($keyVisits) // Most recent first
        ]);
        exit;
    }

    // Get all visits grouped by key
    $visitsByKey = [];

    foreach ($visits['by_key'] as $docKey => $keyData) {
        $visitIds = array_slice($keyData['visit_ids'], -100); // Last 100 visits per key
        
        $keyVisits = [];
        foreach ($visitIds as $visitId) {
            if (isset($visits['visits'][$visitId])) {
                $keyVisits[] = $visits['visits'][$visitId];
            }
        }

        $visitsByKey[$docKey] = [
            'total_visits' => $keyData['total_visits'],
            'unique_visitors' => count($keyData['unique_emails']),
            'visits' => array_reverse($keyVisits) // Most recent first
        ];
    }

    // Get global stats
    $stats = $visits['stats'] ?? [
        'total' => 0,
        'by_device' => [],
        'by_mx_type' => []
    ];

    echo json_encode([
        'success' => true,
        'total_visits' => $stats['total'],
        'total_keys' => count($visitsByKey),
        'device_breakdown' => $stats['by_device'] ?? [],
        'mx_type_breakdown' => $stats['by_mx_type'] ?? [],
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
