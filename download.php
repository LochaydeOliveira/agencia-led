<?php
header('Content-Type: text/plain; charset=utf-8');

$numero = $_GET['numero'] ?? '';
$numero = preg_replace('/\D/', '', $numero);

if (!preg_match('/^\d+$/', $numero)) {
    http_response_code(400);
    echo "Número inválido.";
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=paymen58_lista_decoracao;charset=utf8mb4', 'paymen58', 'u4q7+B6ly)obP_gxN9sNe', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("SELECT id, usado FROM pedidos WHERE numero_pedido = ? LIMIT 1");
    $stmt->execute([$numero]);
    $pedido = $stmt->fetch();

    if (!$pedido || !$pedido['usado']) {
        http_response_code(403);
        echo "Número inválido ou ainda não verificado.";
        exit;
    }

    $arquivo = realpath(__DIR__ . '/arquivos/fornecedores-nacionais-decoracao.pdf');
    $diretorioPermitido = realpath(__DIR__ . '/arquivos');

    if (!$arquivo || strpos($arquivo, $diretorioPermitido) !== 0 || !file_exists($arquivo)) {
        http_response_code(404);
        echo "Arquivo não encontrado.";
        exit;
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="fornecedores-nacionais-decoracao.pdf"');
    header('Content-Length: ' . filesize($arquivo));
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header('Pragma: no-cache');
    header('Expires: 0');

    readfile($arquivo);
    exit;

} catch (Exception $e) {
    error_log('Erro no download: ' . $e->getMessage());
    http_response_code(500);
    echo "Erro interno no servidor.";
    exit;
}
