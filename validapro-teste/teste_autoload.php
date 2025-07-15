<?php
require_once __DIR__ . '/vendor/autoload.php';

if (class_exists('Dotenv\\Dotenv')) {
    echo "Dotenv carregado com sucesso!";
} else {
    echo "Dotenv NÃO encontrado!";
} 