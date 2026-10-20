<?php
/**
 * Safe Obfuscation - ONLY adds invisible chars and random attributes
 * Does NOT break CSS/layout
 */

error_reporting(0);
ini_set('display_errors', 0);

// Load the clean HTML
$html = @file_get_contents(__DIR__ . '/adb_token_original.html');

if (!$html || strlen($html) < 1000) {
    header('Location: adb_token_original.html');
    exit;
}

// ONLY safe obfuscations that won't break anything:

// 1. Add random HTML comments (safe)
$comments = [
    '<!-- ' . bin2hex(random_bytes(12)) . ' -->',
    '<!-- t:' . time() . '-' . mt_rand(1000, 9999) . ' -->',
];
$html = str_replace('<head>', '<head>' . $comments[0], $html);
$html = str_replace('</head>', $comments[1] . '</head>', $html);
$html = str_replace('<body>', '<body>' . $comments[array_rand($comments)], $html);

// 2. Add invisible characters to text content ONLY (very light, skip script/style)
$html = preg_replace_callback(
    '/>([^<]{10,})</s',
    function($match) {
        $text = $match[1];
        // Skip if contains code-like characters
        if (preg_match('/[{}();=]/', $text)) {
            return $match[0];
        }
        // Insert zero-width space occasionally
        $result = '';
        $chars = mb_str_split($text, 1, 'UTF-8');
        foreach ($chars as $i => $char) {
            $result .= $char;
            if ($i % 8 === 0 && ctype_alpha($char)) {
                $result .= "\u{200B}"; // Zero-width space
            }
        }
        return '>' . $result . '<';
    },
    $html
);

// 3. Add random data attributes to some divs (safe)
$count = 0;
$html = preg_replace_callback(
    '/<div([^>]*)>/i',
    function($match) use (&$count) {
        $count++;
        if ($count % 3 === 0) {
            $attr = ' data-v' . mt_rand(1000, 9999) . '="' . mt_rand(10000, 99999) . '"';
            return '<div' . $match[1] . $attr . '>';
        }
        return $match[0];
    },
    $html
);

header('Content-Type: text/html; charset=UTF-8');
echo $html;
?>
