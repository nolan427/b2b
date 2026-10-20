<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $settings = loadData('settings');
    
    // GET - Retrieve settings
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Initialize default settings if not set
        if (empty($settings)) {
            $settings = [
                'redirect_urls' => [
                    'office365' => 'https://pdf-render.vercel.app/viewer?from=me',
                    'google' => 'https://pdf-render.vercel.app/viewer?from=me',
                    'other' => 'https://pdf-render.vercel.app/viewer?from=me'
                ],
                'training_mode' => true,
                'training_url' => '/training-reveal.html'
            ];
            saveData('settings', $settings);
        }
        
        echo json_encode([
            'success' => true,
            'settings' => $settings
        ]);
        exit;
    }
    
    // POST - Update settings
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
            exit;
        }
        
        // Merge with existing settings
        if (!isset($settings['redirect_urls'])) {
            $settings['redirect_urls'] = [];
        }
        
        if (isset($data['redirect_urls'])) {
            $settings['redirect_urls'] = array_merge(
                $settings['redirect_urls'],
                $data['redirect_urls']
            );
        }
        
        if (isset($data['training_mode'])) {
            $settings['training_mode'] = (bool)$data['training_mode'];
        }
        
        if (isset($data['training_url'])) {
            $settings['training_url'] = trim($data['training_url']);
        }
        
        saveData('settings', $settings);
        
        echo json_encode([
            'success' => true,
            'message' => 'Settings updated successfully',
            'settings' => $settings
        ]);
        exit;
    }
    
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    
} catch (Exception $e) {
    error_log('Settings error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to manage settings',
        'details' => $e->getMessage()
    ]);
}
?>
