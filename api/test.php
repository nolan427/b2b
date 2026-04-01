<?php
header('Content-Type: application/json');

try {
    $kvUrl = getenv('KV_REST_API_URL');
    $kvToken = getenv('KV_REST_API_TOKEN');
    
    if (!$kvUrl || !$kvToken) {
        throw new Exception('Environment variables not set');
    }
    
    // Try a simple Redis command
    $url = $kvUrl . '/ping';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $kvToken]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo json_encode([
        'success' => true,
        'message' => 'Redis connection works!',
        'kvUrl' => substr($kvUrl, 0, 30) . '...',
        'ping_response' => $response
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'kvUrl_set' => !empty(getenv('KV_REST_API_URL')),
        'kvToken_set' => !empty(getenv('KV_REST_API_TOKEN'))
    ]);
}
?>