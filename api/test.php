<?php
header('Content-Type: application/json');

try {
    $redisUrl = getenv('REDIS_URL');
    
    if (!$redisUrl) {
        throw new Exception('REDIS_URL environment variable not set');
    }
    
    // Parse Redis URL
    $parsed = parse_url($redisUrl);
    $host = $parsed['host'] ?? 'localhost';
    $port = $parsed['port'] ?? 6379;
    $password = $parsed['pass'] ?? null;
    
    // Try to connect
    $redis = new Redis();
    
    if (!$redis->connect($host, $port, 5)) {
        throw new Exception('Could not connect to Redis at ' . $host . ':' . $port);
    }
    
    if ($password) {
        if (!$redis->auth($password)) {
            throw new Exception('Redis authentication failed');
        }
    }
    
    // Test ping
    $pong = $redis->ping();
    
    // Test set/get
    $redis->set('test_key', 'test_value', 10);
    $value = $redis->get('test_key');
    
    $redis->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Redis connection works!',
        'host' => $host,
        'port' => $port,
        'ping' => $pong,
        'test_set_get' => $value === 'test_value' ? 'OK' : 'FAILED'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'redis_url_set' => !empty(getenv('REDIS_URL'))
    ]);
}
?>