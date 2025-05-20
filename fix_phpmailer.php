<?php
// URLs dos arquivos necess��rios
$files = [
    'PHPMailer.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php',
    'Exception.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php',
    'SMTP.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php'
];

// Cria o diret��rio PHPMailer se n�0�0o existir
if (!file_exists(__DIR__ . '/vendor/PHPMailer')) {
    mkdir(__DIR__ . '/vendor/PHPMailer', 0755, true);
}

// Baixa cada arquivo
foreach ($files as $filename => $url) {
    $content = file_get_contents($url);
    if ($content !== false) {
        file_put_contents(__DIR__ . '/vendor/PHPMailer/' . $filename, $content);
        echo "Arquivo $filename baixado com sucesso!\n";
    } else {
        echo "Erro ao baixar $filename\n";
    }
}

echo "PHPMailer corrigido com sucesso!\n"; 