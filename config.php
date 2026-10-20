<?php
/**
 * Configuration file for B2B Phishing Simulator
 * Uses JSON file storage - No database required!
 */

// ========== APPLICATION SETTINGS ==========
define('APP_NAME', 'Adobe Document Cloud');
define('TIMEZONE', 'America/New_York');

// Data directory (must be writable) - works in any subdirectory
define('DATA_DIR', __DIR__ . '/data');

// Base URL detection - auto-detects subdirectory
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $base = dirname($script);
    return $protocol . '://' . $host . $base;
}

function getBasePath() {
    $script = $_SERVER['SCRIPT_NAME'];
    $base = dirname($script);
    return ($base === '/' || $base === '\\') ? '' : $base;
}

define('BASE_URL', getBaseUrl());
define('BASE_PATH', getBasePath());

// Set timezone
date_default_timezone_set(TIMEZONE);

// Error reporting (set to 0 for production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// ========== JSON FILE FUNCTIONS ==========

function getDataFile($type) {
    $files = [
        'campaigns' => DATA_DIR . '/campaigns.json',
        'visits' => DATA_DIR . '/visits.json',
        'settings' => DATA_DIR . '/settings.json'
    ];
    
    if (!isset($files[$type])) {
        throw new Exception("Invalid data type: $type");
    }
    
    return $files[$type];
}

function loadData($type) {
    $file = getDataFile($type);
    
    // Create data directory if it doesn't exist
    if (!file_exists(DATA_DIR)) {
        mkdir(DATA_DIR, 0755, true);
    }
    
    // Create file if it doesn't exist
    if (!file_exists($file)) {
        $initialData = ($type === 'visits') ? [
            'visits' => [],
            'stats' => [
                'total' => 0,
                'by_email' => [],
                'by_device' => [],
                'by_mx_type' => []
            ],
            'by_key' => []
        ] : [];
        
        saveData($type, $initialData);
        return $initialData;
    }
    
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    
    if ($data === null) {
        error_log("Failed to parse JSON from $file");
        return ($type === 'visits') ? [
            'visits' => [],
            'stats' => ['total' => 0, 'by_email' => [], 'by_device' => [], 'by_mx_type' => []],
            'by_key' => []
        ] : [];
    }
    
    return $data;
}

function saveData($type, $data) {
    $file = getDataFile($type);
    
    // Ensure directory exists
    if (!file_exists(DATA_DIR)) {
        mkdir(DATA_DIR, 0755, true);
    }
    
    // Atomic write with lock
    $tempFile = $file . '.tmp';
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    if (file_put_contents($tempFile, $json, LOCK_EX) === false) {
        throw new Exception("Failed to write to temporary file");
    }
    
    if (!rename($tempFile, $file)) {
        unlink($tempFile);
        throw new Exception("Failed to save data file");
    }
    
    return true;
}

function initializeData() {
    // Ensure data directory exists with proper permissions
    if (!file_exists(DATA_DIR)) {
        if (!mkdir(DATA_DIR, 0755, true)) {
            throw new Exception("Failed to create data directory");
        }
    }
    
    // Create .htaccess to protect data directory
    $htaccess = DATA_DIR . '/.htaccess';
    if (!file_exists($htaccess)) {
        file_put_contents($htaccess, "Deny from all\n");
    }
    
    // Initialize files
    loadData('campaigns');
    loadData('visits');
    loadData('settings'); // Initialize settings file
    
    return true;
}
?>
