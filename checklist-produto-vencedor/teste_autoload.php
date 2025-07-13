<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo '<pre>';
echo 'PWD: ' . getcwd() . PHP_EOL;
echo 'Conteúdo do diretório atual:' . PHP_EOL;
print_r(scandir(__DIR__));
echo 'Conteúdo do diretório vendor (na RAIZ):' . PHP_EOL;
print_r(scandir(__DIR__ . '/../vendor'));
echo '</pre>';

require_once(__DIR__ . '/../vendor/autoload.php');

if (class_exists('Mpdf\\Mpdf')) {
    echo 'mPDF CARREGADO COM SUCESSO!';
} else {
    echo 'mPDF NÃO encontrado!';
} 