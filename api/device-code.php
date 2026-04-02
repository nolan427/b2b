<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

// Azure AD Configuration
$CLIENT_ID = 'd2301a85-917a-484c-8c6b-d3273eebd8dd';
$TENANT_ID = 'common';
$SCOPE = 'openid profile email User.Read offline_access';

// Read JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$action = $data['action'] ?? 'request';

try {
    if ($action === 'request') {
        // Request device code from Microsoft
        $url = "https://login.microsoftonline.com/{$TENANT_ID}/oauth2/v2.0/devicecode";
        
        $postData = http_build_query([
            'client_id' => $CLIENT_ID,
            'scope' => $SCOPE
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Failed to request device code: HTTP ' . $httpCode);
        }

        $result = json_decode($response, true);
        
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);

    } elseif ($action === 'poll') {
        // Poll for token
        $deviceCode = $data['device_code'] ?? null;
        
        if (!$deviceCode) {
            throw new Exception('device_code is required');
        }

        $url = "https://login.microsoftonline.com/{$TENANT_ID}/oauth2/v2.0/token";
        
        $postData = http_build_query([
            'client_id' => $CLIENT_ID,
            'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
            'device_code' => $deviceCode
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);
        
        // Return result with HTTP code
        echo json_encode([
            'success' => true,
            'http_code' => $httpCode,
            'data' => $result
        ]);

    } else {
        throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>