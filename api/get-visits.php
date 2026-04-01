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
    // Connect to Redis
    $redis = connectRedis();

    // Get query parameters
    $key = $_GET['key'] ?? null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

    // If specific key requested
    if ($key) {
        $visitIds = $redis->zrevrange("visits:key:$key", 0, $limit - 1);
        
        $visits = [];
        foreach ($visitIds as $visitId) {
            $data = $redis->get($visitId);
            if ($data) {
                $visits[] = json_decode($data, true);
            }
        }

        $uniqueEmails = $redis->scard("visits:key:$key:emails");
        $totalVisits = $redis->hget('visits:count_by_key', $key);

        $redis->close();

        echo json_encode([
            'success' => true,
            'document_key' => $key,
            'total_visits' => intval($totalVisits),
            'unique_visitors' => intval($uniqueEmails),
            'visits' => $visits
        ]);
        exit;
    }

    // Get all visits grouped by key
    $keyCounts = $redis->hgetall('visits:count_by_key');
    
    $visitsByKey = [];

    // For each key, get its visits
    if ($keyCounts && is_array($keyCounts)) {
        foreach ($keyCounts as $docKey => $count) {
            $visitIds = $redis->zrevrange("visits:key:$docKey", 0, 99);
            
            $visits = [];
            foreach ($visitIds as $visitId) {
                $data = $redis->get($visitId);
                if ($data) {
                    $visits[] = json_decode($data, true);
                }
            }

            $uniqueEmails = $redis->scard("visits:key:$docKey:emails");

            $visitsByKey[$docKey] = [
                'total_visits' => intval($count),
                'unique_visitors' => intval($uniqueEmails),
                'visits' => $visits
            ];
        }
    }

    // Get global stats
    $totalVisits = $redis->get('visits:stats:total') ?? 0;
    $deviceCounts = $redis->hgetall('visits:stats:by_device') ?? [];
    $mxTypeCounts = $redis->hgetall('visits:stats:by_mx_type') ?? [];

    $redis->close();

    echo json_encode([
        'success' => true,
        'total_visits' => intval($totalVisits),
        'total_keys' => count($visitsByKey),
        'device_breakdown' => $deviceCounts,
        'mx_type_breakdown' => $mxTypeCounts,
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

function connectRedis() {
    $redisUrl = getenv('REDIS_URL');
    
    if (!$redisUrl) {
        throw new Exception('REDIS_URL not configured');
    }

    // Parse Redis URL: redis://user:pass@host:port
    $parsed = parse_url($redisUrl);
    
    $host = $parsed['host'] ?? 'localhost';
    $port = $parsed['port'] ?? 6379;
    $password = $parsed['pass'] ?? null;

    $redis = new Redis();
    
    if (!$redis->connect($host, $port, 5)) {
        throw new Exception('Could not connect to Redis');
    }

    if ($password) {
        if (!$redis->auth($password)) {
            throw new Exception('Redis authentication failed');
        }
    }

    return $redis;
}
?>