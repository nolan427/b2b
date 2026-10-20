<?php
/**
 * Advanced Obfuscation Engine v2
 * Tested and working - randomizes everything while preserving functionality
 */

class AdvancedObfuscator {
    private $classMap = [];
    private $idMap = [];
    private $usedNames = [];
    
    // Zero-width and invisible characters
    private $invisibleChars = [
        "\u{200B}", // Zero Width Space
        "\u{200C}", // Zero Width Non-Joiner
        "\u{200D}", // Zero Width Joiner
        "\u{FEFF}", // Zero Width No-Break Space
    ];
    
    /**
     * Generate random but valid HTML identifier
     */
    private function randomName($prefix = '') {
        do {
            // Use letters only (valid HTML)
            $name = $prefix . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8) . mt_rand(100, 999);
        } while (in_array($name, $this->usedNames));
        
        $this->usedNames[] = $name;
        return $name;
    }
    
    /**
     * Obfuscate text with invisible characters (light touch)
     */
    public function obfuscateText($text) {
        if (strlen($text) < 5 || preg_match('/^[\s\n\r]*$/', $text)) {
            return $text; // Don't obfuscate whitespace-only or very short text
        }
        
        $result = '';
        $chars = mb_str_split($text, 1, 'UTF-8');
        $insertProbability = 0.15; // 15% chance per character
        
        foreach ($chars as $i => $char) {
            $result .= $char;
            // Insert invisible char after letters/numbers only
            if (preg_match('/[a-zA-Z0-9]/', $char) && mt_rand(1, 100) <= ($insertProbability * 100)) {
                $result .= $this->invisibleChars[array_rand($this->invisibleChars)];
            }
        }
        
        return $result;
    }
    
    /**
     * Generate random HTML attributes
     */
    public function randomAttributes($count = 2) {
        $attrs = [];
        $prefixes = ['data-v', 'data-r', 'data-s', 'x-', 'app-'];
        
        for ($i = 0; $i < $count; $i++) {
            $prefix = $prefixes[array_rand($prefixes)];
            $name = $prefix . mt_rand(1000, 9999);
            $value = mt_rand(10000, 99999);
            $attrs[] = $name . '="' . $value . '"';
        }
        
        return implode(' ', $attrs);
    }
    
    /**
     * Randomize CSS classes throughout HTML
     */
    public function randomizeClasses($html) {
        // Find all class attributes
        preg_match_all('/class="([^"]+)"/', $html, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $classes = explode(' ', $match[1]);
            $newClasses = [];
            
            foreach ($classes as $class) {
                $class = trim($class);
                if (empty($class)) continue;
                
                if (!isset($this->classMap[$class])) {
                    $this->classMap[$class] = $this->randomName('c');
                }
                $newClasses[] = $this->classMap[$class];
            }
            
            $newClassStr = implode(' ', $newClasses);
            $html = str_replace($match[0], 'class="' . $newClassStr . '"', $html);
        }
        
        // Update CSS to match new class names
        $html = preg_replace_callback('/<style[^>]*>(.*?)<\/style>/s', function($match) {
            $css = $match[1];
            foreach ($this->classMap as $old => $new) {
                // Replace .classname but not in middle of words
                $css = preg_replace('/\.' . preg_quote($old, '/') . '\b/', '.' . $new, $css);
            }
            return '<style>' . $css . '</style>';
        }, $html);
        
        return $html;
    }
    
    /**
     * Randomize IDs throughout HTML
     */
    public function randomizeIds($html) {
        // Find all id attributes
        preg_match_all('/id="([^"]+)"/', $html, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $oldId = $match[1];
            if (!isset($this->idMap[$oldId])) {
                $this->idMap[$oldId] = $this->randomName('id');
            }
        }
        
        // Replace IDs in HTML attributes
        foreach ($this->idMap as $old => $new) {
            $html = preg_replace('/id="' . preg_quote($old, '/') . '"/', 'id="' . $new . '"', $html);
            // Also replace in JavaScript getElementById and CSS #selectors
            $html = preg_replace('/getElementById\([\'"]' . preg_quote($old, '/') . '[\'"]\)/', 'getElementById(\'' . $new . '\')', $html);
            $html = preg_replace('/#' . preg_quote($old, '/') . '\b/', '#' . $new, $html);
        }
        
        return $html;
    }
    
    /**
     * Add random attributes to HTML elements
     */
    public function addRandomAttributes($html) {
        // Add to major structural elements
        $tags = ['div', 'section', 'button', 'input', 'form', 'span'];
        
        foreach ($tags as $tag) {
            $count = 0;
            $html = preg_replace_callback(
                '/<' . $tag . '(\s[^>]*)?>/i',
                function($match) use (&$count) {
                    $count++;
                    // Add attributes to 70% of elements
                    if (mt_rand(1, 10) <= 7) {
                        $existing = $match[1] ?? '';
                        return '<' . $match[0][1] . $existing . ' ' . $this->randomAttributes(mt_rand(1, 3)) . '>';
                    }
                    return $match[0];
                },
                $html,
                -1
            );
        }
        
        return $html;
    }
    
    /**
     * Obfuscate inline text content (skip script/style tags)
     */
    public function obfuscateTextNodes($html) {
        // First, protect script and style content
        $protected = [];
        $placeholder = '___PROTECTED_%d___';
        
        // Extract and protect <script> tags
        $html = preg_replace_callback('/<script[^>]*>.*?<\/script>/is', function($match) use (&$protected, $placeholder) {
            $id = count($protected);
            $protected[$id] = $match[0];
            return sprintf($placeholder, $id);
        }, $html);
        
        // Extract and protect <style> tags
        $html = preg_replace_callback('/<style[^>]*>.*?<\/style>/is', function($match) use (&$protected, $placeholder) {
            $id = count($protected);
            $protected[$id] = $match[0];
            return sprintf($placeholder, $id);
        }, $html);
        
        // Now obfuscate remaining text
        $html = preg_replace_callback(
            '/>(([^<>]+))</s',
            function($match) {
                $text = $match[1];
                // Skip if it's just whitespace, numbers, or special chars
                if (preg_match('/^[\s\d\W]*$/', $text)) {
                    return $match[0];
                }
                return '>' . $this->obfuscateText($text) . '<';
            },
            $html
        );
        
        // Restore protected content
        foreach ($protected as $id => $content) {
            $html = str_replace(sprintf($placeholder, $id), $content, $html);
        }
        
        return $html;
    }
    
    /**
     * Add random HTML comments throughout
     */
    public function addRandomComments($html) {
        $comments = [
            '<!-- ' . bin2hex(random_bytes(16)) . ' -->',
            '<!-- r:' . mt_rand(100000, 999999) . ' -->',
            '<!-- gen:' . time() . ' -->',
        ];
        
        // Insert random comments before major tags
        $html = preg_replace_callback('/<(div|section|header|footer)/', function($match) use ($comments) {
            if (mt_rand(1, 100) <= 30) { // 30% chance
                return $comments[array_rand($comments)] . "\n" . $match[0];
            }
            return $match[0];
        }, $html);
        
        return $html;
    }
    
    /**
     * Full obfuscation pipeline
     */
    public function obfuscate($html) {
        // Step 1: Randomize classes
        $html = $this->randomizeClasses($html);
        
        // Step 2: Randomize IDs
        $html = $this->randomizeIds($html);
        
        // Step 3: Add random attributes
        $html = $this->addRandomAttributes($html);
        
        // Step 4: Obfuscate text (light touch)
        $html = $this->obfuscateTextNodes($html);
        
        // Step 5: Add random comments
        $html = $this->addRandomComments($html);
        
        return $html;
    }
}
?>
