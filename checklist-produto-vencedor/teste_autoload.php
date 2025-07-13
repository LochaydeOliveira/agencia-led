<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$autoload = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoload)) {
    die('Autoload NÃO encontrado em: ' . $autoload);
}
require_once $autoload;

if (class_exists('Mpdf\Mpdf')) {
    echo 'mPDF CARREGADO COM SUCESSO!';
} else {
    echo 'mPDF NÃO encontrado!';
} 