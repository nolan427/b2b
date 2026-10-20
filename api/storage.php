<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$db = getDB();

$action = $_GET['action'] ?? '';
$key = $_GET['key'] ?? '';

try {
    if ($action === 'get' && $key) {
        // Fetch campaign by key
        $stmt = $db->prepare("SELECT sender, title, url FROM campaigns WHERE id = ?");
        $stmt->execute([$key]);
        $campaign = $stmt->fetch();
        
        if ($campaign) {
            echo json_encode(['success' => true, 'data' => $campaign]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Key not found']);
        }
        
    } elseif ($action === 'set' && $key) {
        // Set campaign data
        $sender = $_REQUEST['sender'] ?? '';
        $title = $_REQUEST['title'] ?? '';
        $url = $_REQUEST['url'] ?? '';
        
        if (empty($sender) || empty($title) || empty($url)) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        // Insert or update
        $stmt = $db->prepare("
            INSERT INTO campaigns (id, sender, title, url) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                sender = VALUES(sender),
                title = VALUES(title),
                url = VALUES(url)
        ");
        
        $stmt->execute([$key, $sender, $title, $url]);
        echo json_encode(['success' => true, 'message' => 'Data stored']);
        
    } elseif ($action === 'delete' && $key) {
        // Delete campaign
        $stmt = $db->prepare("DELETE FROM campaigns WHERE id = ?");
        $stmt->execute([$key]);
        echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);
        
    } elseif ($action === 'list') {
        // List all campaigns
        $pattern = $_GET['pattern'] ?? '*';
        
        if ($pattern === '*') {
            $stmt = $db->query("SELECT id, sender, title, url FROM campaigns ORDER BY created_at DESC");
        } else {
            $likePattern = str_replace('*', '%', $pattern);
            $stmt = $db->prepare("SELECT id, sender, title, url FROM campaigns WHERE id LIKE ? ORDER BY created_at DESC");
            $stmt->execute([$likePattern]);
        }
        
        $docs = [];
        while ($row = $stmt->fetch()) {
            $docs[$row['id']] = [
                'sender' => $row['sender'],
                'title' => $row['title'],
                'url' => $row['url']
            ];
        }
        
        echo json_encode(['success' => true, 'docs' => $docs]);
        
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    }
    
} catch (Exception $e) {
    error_log('Storage error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database operation failed',
        'details' => $e->getMessage()
    ]);
}
?>
