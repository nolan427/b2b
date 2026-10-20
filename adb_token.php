<?php
/**
 * Safe obfuscated version - reads from clean backup
 */

// Try to read from the original backup first
$possibleFiles = [
    __DIR__ . '/adb_token_original.html',
    __DIR__ . '/adb_token_original_backup.html',
];

$html = null;
foreach ($possibleFiles as $file) {
    if (file_exists($file) && filesize($file) > 10000) {
        $html = file_get_contents($file);
        if ($html && strpos($html, '<!DOCTYPE html>') !== false) {
            break;
        }
    }
}

// If all else fails, serve without obfuscation
if (!$html) {
    header('Location: adb_token_original.html' . (isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_FRAGMENT) : ''));
    exit;
}

// Add ONLY safe random comments (no modifications to content)
$safeComment1 = "\n<!-- " . bin2hex(random_bytes(12)) . " -->\n";
$safeComment2 = "\n<!-- gen:" . time() . "-" . mt_rand(1000, 9999) . " -->\n";

$html = str_replace('<head>', '<head>' . $safeComment1, $html);
$html = str_replace('</head>', $safeComment2 . '</head>', $html);

// Output clean HTML
header('Content-Type: text/html; charset=UTF-8');
echo $html;
?>
