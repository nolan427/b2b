<?php
// log.php

date_default_timezone_set('UTC');
$logFile = __DIR__ . '/logs.txt';

/**
 * Sanitize value so logs stay one-line and colon-safe
 */
function clean($value) {
    $value = (string)$value;
    $value = str_replace(["\r", "\n"], ' ', $value);
    $value = preg_replace('/:+/', '_', $value);
    return trim($value);
}

/**
 * Get visitor IP (supports proxies / CDNs)
 */
function getClientIP() {
    $keys = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR'
    ];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            return explode(',', $_SERVER[$key])[0];
        }
    }
    return 'unknown';
}

$ip = getClientIP();
$userAgent = clean($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

// Data from JS
$deviceName = clean($_POST['device'] ?? 'unknown');
$resolution = clean($_POST['resolution'] ?? 'unknown');
$email = clean($_POST['email'] ?? 'unknown');

/**
 * ASN lookup (free, no key)
 * ipapi.co
 */
$asn = '';
$org = '';

$ipApiUrl = "https://ipapi.co/{$ip}/json/";
$context = stream_context_create([
    'http' => [
        'timeout' => 3,
        'header' => "User-Agent: visit-logger/1.0\r\n"
    ]
]);

$response = @file_get_contents($ipApiUrl, false, $context);
if ($response !== false) {
    $json = json_decode($response, true);
    if (is_array($json)) {
        $asn = clean($json['asn'] ?? '');
        $org = clean($json['org'] ?? $json['organization'] ?? '');
    }
}

$timestamp = gmdate('c');

// Colon-separated log entry
$line = implode(':', [$email,
    $timestamp,
    clean($ip),
    $deviceName,
    $asn,
    $org,
    $resolution,
    $userAgent
]) . PHP_EOL;

// Append to logs.txt
file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);

// Optional response
http_response_code(204);
exit;
