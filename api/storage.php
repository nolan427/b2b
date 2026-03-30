<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../vendor/autoload.php';

use Predis\Client;

try {
    // Connect to Vercel Redis
    $redis = new Client(getenv('REDIS_URL'));
    
    $action = $_GET['action'] ?? '';
    $key = $_GET['key'] ?? '';
    $value = $_GET['value'] ?? '';
    
    if ($action === 'get' && $key) {
        $result = $redis->get($key);
        echo json_encode(['success' => true, 'value' => $result]);
    } elseif ($action === 'set' && $key && $value) {
        $redis->set($key, $value);
        echo json_encode(['success' => true, 'message' => 'Data stored']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>