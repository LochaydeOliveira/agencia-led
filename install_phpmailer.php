<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// URL do PHPMailer no GitHub
$phpmailer_url = 'https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.8.1.zip';

// Diretório de destino
$install_dir = __DIR__ . '/vendor/PHPMailer';

// Cria o diretório se não existir
if (!file_exists($install_dir)) {
    mkdir($install_dir, 0777, true);
}

// Baixa o arquivo ZIP
echo "Baixando PHPMailer...\n";
$zip_file = __DIR__ . '/phpmailer.zip';
file_put_contents($zip_file, file_get_contents($phpmailer_url));

// Extrai o arquivo ZIP
echo "Extraindo arquivos...\n";
$zip = new ZipArchive;
if ($zip->open($zip_file) === TRUE) {
    $zip->extractTo(__DIR__ . '/temp');
    $zip->close();
    
    // Move os arquivos necessários
    $source_dir = __DIR__ . '/temp/PHPMailer-6.8.1/src';
    $files = ['PHPMailer.php', 'SMTP.php', 'Exception.php'];
    
    foreach ($files as $file) {
        copy($source_dir . '/' . $file, $install_dir . '/' . $file);
        echo "Arquivo $file copiado com sucesso!\n";
    }
    
    // Limpa arquivos temporários
    unlink($zip_file);
    array_map('unlink', glob(__DIR__ . '/temp/PHPMailer-6.8.1/src/*.*'));
    rmdir(__DIR__ . '/temp/PHPMailer-6.8.1/src');
    rmdir(__DIR__ . '/temp/PHPMailer-6.8.1');
    rmdir(__DIR__ . '/temp');
    
    echo "PHPMailer instalado com sucesso!\n";
} else {
    echo "Erro ao extrair o arquivo ZIP\n";
} 