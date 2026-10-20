<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$action = $_GET['action'] ?? '';
$key = $_GET['key'] ?? '';

try {
    $campaigns = loadData('campaigns');
    
    if ($action === 'get' && $key) {
        // Fetch campaign by key
        if (isset($campaigns[$key])) {
            echo json_encode(['success' => true, 'data' => $campaigns[$key]]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Key not found']);
        }
        
    } elseif ($action === 'set' && $key) {
        // Set campaign data
        $sender = trim($_REQUEST['sender'] ?? '');
        $title = trim($_REQUEST['title'] ?? '');
        $url = trim($_REQUEST['url'] ?? '');
        
        if (empty($sender) || empty($title) || empty($url)) {
            echo json_encode([
                'success' => false, 
                'error' => 'Missing required fields',
                'debug' => [
                    'sender' => !empty($sender),
                    'title' => !empty($title),
                    'url' => !empty($url)
                ]
            ]);
            exit;
        }
        
        // Save campaign
        $campaigns[$key] = [
            'sender' => $sender,
            'title' => $title,
            'url' => $url,
            'created_at' => $campaigns[$key]['created_at'] ?? date('c'),
            'updated_at' => date('c')
        ];
        
        saveData('campaigns', $campaigns);
        echo json_encode(['success' => true, 'message' => 'Campaign created successfully', 'key' => $key]);
        
    } elseif ($action === 'delete' && $key) {
        // Delete campaign
        if (isset($campaigns[$key])) {
            unset($campaigns[$key]);
            saveData('campaigns', $campaigns);
            echo json_encode(['success' => true, 'deleted' => 1]);
        } else {
            echo json_encode(['success' => true, 'deleted' => 0]);
        }
        
    } elseif ($action === 'list') {
        // List all campaigns
        $pattern = $_GET['pattern'] ?? '*';
        
        if ($pattern === '*') {
            $docs = $campaigns;
        } else {
            // Simple pattern matching
            $docs = [];
            foreach ($campaigns as $k => $v) {
                if (fnmatch($pattern, $k)) {
                    $docs[$k] = $v;
                }
            }
        }
        
        // Remove timestamps from output
        $output = [];
        foreach ($docs as $k => $v) {
            $output[$k] = [
                'sender' => $v['sender'],
                'title' => $v['title'],
                'url' => $v['url']
            ];
        }
        
        echo json_encode(['success' => true, 'docs' => $output]);
        
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    }
    
} catch (Exception $e) {
    error_log('Storage error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Storage operation failed',
        'details' => $e->getMessage()
    ]);
}
?>
