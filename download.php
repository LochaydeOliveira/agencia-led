<?php
// download.php
// Ex: chamada via https://agencialed.com/download.php?codigo=123456789012345

$codigo = $_GET['codigo'] ?? '';
$codigo = preg_replace('/\D/', '', $codigo);

if (!preg_match('/^\d{15}$/', $codigo)) {
    http_response_code(400);
    echo "Código inválido.";
    exit;
}

// Conexão com o banco
$host = 'localhost';
$dbname = 'paymen58_lista_decoracao';
$username = 'paymen58';
$password = 'u4q7+B6ly)obP_gxN9sNe';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("SELECT id, usado FROM pedidos WHERE codigo = ? LIMIT 1");
    $stmt->execute([$codigo]);
    $pedido = $stmt->fetch();

    if (!$pedido || !$pedido['usado']) {
        http_response_code(403);
        echo "Código inválido ou ainda não verificado.";
        exit;
    }

    // Caminho seguro do arquivo (ajuste se necessário)
    $arquivo = __DIR__ . '/arquivos/fornecedores-nacionais-decoracao.pdf';

    if (!file_exists($arquivo)) {
        http_response_code(404);
        echo "Arquivo não encontrado.";
        exit;
    }

    // Força o download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="fornecedores-nacionais-decoracao.pdf"');
    header('Content-Length: ' . filesize($arquivo));
    readfile($arquivo);
    exit;

} catch (Exception $e) {
    error_log('Erro no download: ' . $e->getMessage());
    http_response_code(500);
    echo "Erro interno no servidor.";
    exit;
}
