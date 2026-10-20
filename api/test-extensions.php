<?php
header('Content-Type: text/plain');

echo "PHP Version: " . PHP_VERSION . "\n\n";
echo "Loaded Extensions:\n";
print_r(get_loaded_extensions());

echo "\n\nMongoDB Extension: " . (extension_loaded('mongodb') ? 'YES' : 'NO') . "\n";
echo "PDO Extension: " . (extension_loaded('pdo') ? 'YES' : 'NO') . "\n";
echo "cURL Extension: " . (extension_loaded('curl') ? 'YES' : 'NO') . "\n";

if (extension_loaded('mongodb')) {
    echo "\nMongoDB Extension Info:\n";
    $reflection = new ReflectionExtension('mongodb');
    echo $reflection;
}
?>
