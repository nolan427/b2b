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
    // Import Vercel KV using Composer or direct HTTP calls
    // Since we're using Vercel KV, we need to use REST API
    
    $kvUrl = getenv('KV_REST_API_URL');
    $kvToken = getenv('KV_REST_API_TOKEN');
    
    if (!$kvUrl || !$kvToken) {
        throw new Exception('Redis credentials not configured');
    }
    
    // Helper function to call Redis REST API
    function kvRequest($endpoint, $kvUrl, $kvToken) {
        $url = $kvUrl . $endpoint;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $kvToken
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        return $data['result'] ?? null;
    }
    
    // Get all document keys that have visits
    $keyCountsData = kvRequest('/hgetall/visits:count_by_key', $kvUrl, $kvToken);
    
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
    foreach ($keyCounts as $key => $count) {
        // Get visit IDs for this key (latest 100)
        $visitIds = kvRequest('/zrevrange/' . urlencode("visits:key:$key") . '/0/99', $kvUrl, $kvToken);
        
        $visits = [];
        if ($visitIds && is_array($visitIds)) {
            foreach ($visitIds as $visitId) {
                $data = kvRequest('/get/' . urlencode($visitId), $kvUrl, $kvToken);
                if ($data) {
                    $visits[] = json_decode($data, true);
                }
            }
        }
        
        // Get unique email count
        $uniqueEmails = kvRequest('/scard/' . urlencode("visits:key:$key:emails"), $kvUrl, $kvToken) ?? 0;
        
        $visitsByKey[$key] = [
            'total_visits' => intval($count),
            'unique_visitors' => intval($uniqueEmails),
            'visits' => $visits
        ];
    }
    
    // Get global stats
    $totalVisits = kvRequest('/get/visits:stats:total', $kvUrl, $kvToken) ?? 0;
    
    $deviceCountsData = kvRequest('/hgetall/visits:stats:by_device', $kvUrl, $kvToken);
    $deviceCounts = [];
    if ($deviceCountsData && is_array($deviceCountsData)) {
        for ($i = 0; $i < count($deviceCountsData); $i += 2) {
            if (isset($deviceCountsData[$i + 1])) {
                $deviceCounts[$deviceCountsData[$i]] = $deviceCountsData[$i + 1];
            }
        }
    }
    
    $mxTypeCountsData = kvRequest('/hgetall/visits:stats:by_mx_type', $kvUrl, $kvToken);
    $mxTypeCounts = [];
    if ($mxTypeCountsData && is_array($mxTypeCountsData)) {
        for ($i = 0; $i < count($mxTypeCountsData); $i += 2) {
            if (isset($mxTypeCountsData[$i + 1])) {
                $mxTypeCounts[$mxTypeCountsData[$i]] = $mxTypeCountsData[$i + 1];
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
?>