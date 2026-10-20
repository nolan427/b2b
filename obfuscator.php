<?php
/**
 * Advanced DOM Obfuscation System
 * Randomizes HTML structure, attributes, and content on every page load
 */

class DOMObfuscator {
    private $invisibleChars = [
        "\u{200B}", // Zero-width space
        "\u{200C}", // Zero-width non-joiner
        "\u{200D}", // Zero-width joiner
        "\u{FEFF}", // Zero-width no-break space
        "\u{2060}", // Word joiner
    ];
    
    private $usedIds = [];
    
    /**
     * Generate random string for IDs and classes
     */
    public function randomId($prefix = '') {
        do {
            $id = $prefix . substr(md5(random_bytes(16) . microtime()), 0, 12);
        } while (in_array($id, $this->usedIds));
        
        $this->usedIds[] = $id;
        return $id;
    }
    
    /**
     * Generate random meaningless attributes
     */
    public function randomAttributes($count = 3) {
        $attrs = [];
        $prefixes = ['data-', 'aria-', 'x-', 'app-', 'ui-'];
        $values = ['true', 'false', '1', '0', 'active', 'enabled', 'ready', 'init'];
        
        for ($i = 0; $i < $count; $i++) {
            $prefix = $prefixes[array_rand($prefixes)];
            $name = $prefix . substr(md5(random_bytes(8)), 0, 8);
            $value = $values[array_rand($values)] . '-' . mt_rand(1000, 9999);
            $attrs[] = $name . '="' . $value . '"';
        }
        
        return implode(' ', $attrs);
    }
    
    /**
     * Insert invisible characters into text
     */
    public function obfuscateText($text, $density = 0.3) {
        if (empty($text)) return $text;
        
        $chars = mb_str_split($text);
        $result = '';
        
        foreach ($chars as $char) {
            $result .= $char;
            if (mt_rand() / mt_getrandmax() < $density && $char !== ' ') {
                $result .= $this->invisibleChars[array_rand($this->invisibleChars)];
            }
        }
        
        return $result;
    }
    
    /**
     * Obfuscate base64 image with random padding
     */
    public function obfuscateImage($imagePath) {
        if (!file_exists($imagePath)) {
            return '';
        }
        
        $imageData = file_get_contents($imagePath);
        $base64 = base64_encode($imageData);
        
        // Add random whitespace/newlines in base64 (still valid)
        $chunks = str_split($base64, mt_rand(60, 120));
        $obfuscated = implode("\n", $chunks);
        
        // Detect mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $imagePath);
        finfo_close($finfo);
        
        return "data:$mimeType;base64," . $obfuscated;
    }
    
    /**
     * Generate random poem (for view-source obfuscation)
     */
    public function generatePoem() {
        $lines = [
            "In circuits deep where data flows,",
            "Through silicon valleys, knowledge grows,",
            "Binary dreams in endless streams,",
            "Nothing here is what it seems.",
            "",
            "Algorithms dance in quantum light,",
            "Processing thoughts throughout the night,",
            "In virtual realms where logic reigns,",
            "Pure mathematics fills the plains.",
            "",
            "From zero one to infinite code,",
            "Along the information road,",
            "Where bits and bytes forever gleam,",
            "Reality is but a dream."
        ];
        
        // Randomize order
        shuffle($lines);
        return implode("\n", $lines);
    }
    
    /**
     * Wrap HTML with view-source protection
     */
    public function wrapWithPoem($html) {
        $poem = $this->generatePoem();
        
        // The actual HTML is hidden in a script that reconstructs it
        $encoded = base64_encode($html);
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>body{display:none}</style>
</head>
<body>
<pre style="display:block;font-family:monospace;padding:40px;">
$poem

-- Generated: {date('Y-m-d H:i:s')} --
-- System: Automated Content Delivery --
</pre>
<script>
(function(){
    var d=atob('$encoded');
    document.open();
    document.write(d);
    document.close();
})();
</script>
</body>
</html>
HTML;
    }
    
    /**
     * Randomize class names in HTML
     */
    public function randomizeClasses($html) {
        // Extract all class names
        preg_match_all('/class="([^"]+)"/', $html, $matches);
        $classMap = [];
        
        foreach ($matches[1] as $classes) {
            $classList = explode(' ', $classes);
            foreach ($classList as $class) {
                if (!isset($classMap[$class])) {
                    $classMap[$class] = 'c' . substr(md5($class . microtime()), 0, 10);
                }
            }
        }
        
        // Replace classes
        foreach ($classMap as $old => $new) {
            $html = preg_replace('/\b' . preg_quote($old, '/') . '\b/', $new, $html);
        }
        
        return $html;
    }
    
    /**
     * Add random attributes to all tags
     */
    public function addRandomAttributes($html) {
        $tags = ['div', 'span', 'button', 'input', 'form', 'section', 'header', 'a'];
        
        foreach ($tags as $tag) {
            $html = preg_replace_callback(
                '/<' . $tag . '([^>]*)>/',
                function($matches) {
                    $existing = $matches[1];
                    $random = ' ' . $this->randomAttributes(mt_rand(2, 4));
                    return '<' . $matches[0][1] . $existing . $random . '>';
                },
                $html
            );
        }
        
        return $html;
    }
}

// Global instance
$obfuscator = new DOMObfuscator();
?>
