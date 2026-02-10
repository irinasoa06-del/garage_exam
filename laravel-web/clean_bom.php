<?php

$dir = __DIR__ . '/app';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
$bom = pack('H*', 'EFBBBF');

foreach ($regex as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    if (substr($content, 0, 3) === $bom) {
        $content = substr($content, 3);
        file_put_contents($path, $content);
        echo "Cleaned BOM from: " . basename($path) . "\n";
    }
}
echo "Recursive cleanup done.\n";
