<?php
// download.php
header('Content-Type: text/plain; charset=utf-8');

$codigo = $_GET['codigo'] ?? '';
$codigo = preg_replace('/\D/', '', $codigo);

if (!preg_match('/^\d{15}$/', $codigo)) {
    http_response_code(400);
    echo "Código inválido.";
    exit;
}

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

    // Caminho absoluto e verificação extra
    $arquivo = realpath(__DIR__ . '/arquivos/fornecedores-nacionais-decoracao.pdf');

    // Verifica se arquivo existe e está dentro do diretório esperado
    $diretorioPermitido = realpath(__DIR__ . '/arquivos');
    if (!$arquivo || strpos($arquivo, $diretorioPermitido) !== 0 || !file_exists($arquivo)) {
        http_response_code(404);
        echo "Arquivo não encontrado.";
        exit;
    }

    // Define cabeçalhos para forçar download e evitar cache
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="fornecedores-nacionais-decoracao.pdf"');
    header('Content-Length: ' . filesize($arquivo));
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Lê o arquivo e envia para o cliente
    readfile($arquivo);
    exit;

} catch (Exception $e) {
    error_log('Erro no download: ' . $e->getMessage());
    http_response_code(500);
    echo "Erro interno no servidor.";
    exit;
}
