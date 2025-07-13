<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo '<pre>';
echo 'PWD: ' . getcwd() . PHP_EOL;
echo 'Conteúdo do diretório atual:' . PHP_EOL;
print_r(scandir(__DIR__));
echo 'Conteúdo do diretório vendor:' . PHP_EOL;
print_r(scandir(__DIR__ . '/vendor'));
echo '</pre>'; 

$autoload = __DIR__ . '/../vendor/autoload.php'; 