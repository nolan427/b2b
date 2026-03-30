<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../vendor/autoload.php';
use Predis\Client;

// Connect to Redis
$redis = new Client(getenv('REDIS_URL'));

$action = $_GET['action'] ?? '';
$key = $_GET['key'] ?? '';

if ($action === 'get' && $key) {
    // Fetch all fields for this key
    $value = $redis->hgetall($key);
    if ($value && count($value)) {
        echo json_encode(['success' => true, 'data' => $value]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Key not found or empty']);
    }
} elseif ($action === 'set' && $key) {
    // Set the hash fields for this key
    $sender = $_REQUEST['sender'] ?? '';
    $title = $_REQUEST['title'] ?? '';
    $url = $_REQUEST['url'] ?? '';
    $fields = [];
    if ($sender !== '') $fields['sender'] = $sender;
    if ($title !== '') $fields['title'] = $title;
    if ($url !== '') $fields['url'] = $url;
    if (count($fields)) {
        $redis->hmset($key, $fields);
        echo json_encode(['success' => true, 'message' => 'Data stored']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No data provided']);
    }
} elseif ($action === 'delete' && $key) {
    $count = $redis->del($key);
    echo json_encode(['success' => $count > 0, 'deleted' => $count]);
} elseif ($action === 'list') {
    $pattern = $_GET['pattern'] ?? '*';
    $keys = $redis->keys($pattern);
    $docs = [];
    foreach ($keys as $k) {
        $hash = $redis->hgetall($k);
        // Only add keys if result is a non-empty array (i.e., a Redis hash)
        if (is_array($hash) && count($hash)) {
            $docs[$k] = $hash;
        }
    }
    echo json_encode(['success' => true, 'docs' => $docs]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
}
?>