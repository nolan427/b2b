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
    // Get Redis credentials
    $kvUrl = getenv('KV_REST_API_URL');
    $kvToken = getenv('KV_REST_API_TOKEN');

    if (!$kvUrl || !$kvToken) {
        throw new Exception('Redis credentials not configured');
    }

    // Get query parameters
    $key = $_GET['key'] ?? null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

    // If specific key requested
    if ($key) {
        $visitIds = kvZRevRange("visits:key:$key", 0, $limit - 1, $kvUrl, $kvToken);
        
        $visits = [];
        foreach ($visitIds as $visitId) {
            $data = kvGet($visitId, $kvUrl, $kvToken);
            if ($data) {
                $visits[] = json_decode($data, true);
            }
        }

        $uniqueEmails = kvSCard("visits:key:$key:emails", $kvUrl, $kvToken);
        $totalVisits = kvHGet('visits:count_by_key', $key, $kvUrl, $kvToken);

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
    $keyCountsData = kvHGetAll('visits:count_by_key', $kvUrl, $kvToken);
    
    // Convert array to associative array
    $keyCounts = [];
    if ($keyCountsData && is_array($keyCountsData)) {
        for ($i = 0; $i < count($keyCountsData); $i += 2) {
            if (isset($keyCountsData[$i + 1])) {
                $keyCounts[$keyCountsData[$i]] = $keyCountsData[$i + 1];
            }
        }
    }

    $visitsByKey = [];

    // For each key, get its visits
    foreach ($keyCounts as $docKey => $count) {
        $visitIds = kvZRevRange("visits:key:$docKey", 0, 99, $kvUrl, $kvToken);
        
        $visits = [];
        foreach ($visitIds as $visitId) {
            $data = kvGet($visitId, $kvUrl, $kvToken);
            if ($data) {
                $visits[] = json_decode($data, true);
            }
        }

        $uniqueEmails = kvSCard("visits:key:$docKey:emails", $kvUrl, $kvToken);

        $visitsByKey[$docKey] = [
            'total_visits' => intval($count),
            'unique_visitors' => intval($uniqueEmails),
            'visits' => $visits
        ];
    }

    // Get global stats
    $totalVisits = kvGet('visits:stats:total', $kvUrl, $kvToken) ?? 0;
    
    $deviceCountsData = kvHGetAll('visits:stats:by_device', $kvUrl, $kvToken);
    $deviceCounts = [];
    if ($deviceCountsData && is_array($deviceCountsData)) {
        for ($i = 0; $i < count($deviceCountsData); $i += 2) {
            if (isset($deviceCountsData[$i + 1])) {
                $deviceCounts[$deviceCountsData[$i]] = intval($deviceCountsData[$i + 1]);
            }
        }
    }

    $mxTypeCountsData = kvHGetAll('visits:stats:by_mx_type', $kvUrl, $kvToken);
    $mxTypeCounts = [];
    if ($mxTypeCountsData && is_array($mxTypeCountsData)) {
        for ($i = 0; $i < count($mxTypeCountsData); $i += 2) {
            if (isset($mxTypeCountsData[$i + 1])) {
                $mxTypeCounts[$mxTypeCountsData[$i]] = intval($mxTypeCountsData[$i + 1]);
            }
        }
    }

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

// ========== HELPER FUNCTIONS ==========

function kvHGetAll($key, $kvUrl, $kvToken) {
    $url = $kvUrl . '/hgetall/' . urlencode($key);
    return kvRequest($url, $kvToken);
}

function kvZRevRange($key, $start, $stop, $kvUrl, $kvToken) {
    $url = $kvUrl . '/zrevrange/' . urlencode($key) . '/' . $start . '/' . $stop;
    return kvRequest($url, $kvToken);
}

function kvGet($key, $kvUrl, $kvToken) {
    $url = $kvUrl . '/get/' . urlencode($key);
    return kvRequest($url, $kvToken);
}

function kvSCard($key, $kvUrl, $kvToken) {
    $url = $kvUrl . '/scard/' . urlencode($key);
    return kvRequest($url, $kvToken);
}

function kvHGet($key, $field, $kvUrl, $kvToken) {
    $url = $kvUrl . '/hget/' . urlencode($key) . '/' . urlencode($field);
    return kvRequest($url, $kvToken);
}

function kvRequest($url, $token) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("Redis request failed with code $httpCode");
    }

    $data = json_decode($response, true);
    if (isset($data['error'])) {
        throw new Exception('Redis error: ' . $data['error']);
    }

    return $data['result'] ?? null;
}
?>