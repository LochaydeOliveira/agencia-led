<?php
$numero = $_GET['pedido'] ?? '';
$numero = preg_replace('/\D/', '', $numero); // mantém só números

if ($numero === '') {
    http_response_code(400);
    echo "Número do pedido inválido.";
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=paymen58_lista_decoracao;charset=utf8mb4', 'paymen58', 'u4q7+B6ly)obP_gxN9sNe', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("SELECT id, usado FROM pedidos WHERE numero_pedido = ? LIMIT 1");
    $stmt->execute([$numero]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        http_response_code(403);
        echo "Pedido não encontrado.";
        exit;
    }

    if ($pedido['usado']) {
        http_response_code(403);
        echo "Este pedido já foi utilizado para download.";
        exit;
    }

    // Marcar como usado
    $update = $pdo->prepare("UPDATE pedidos SET usado = 1 WHERE numero_pedido = ?");
    $update->execute([$numero]);

    $arquivo = __DIR__ . '/arquivos/fornecedores-nacionais-decoracao.pdf';

    if (!file_exists($arquivo)) {
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
    echo "Erro interno ao processar seu pedido.";
    exit;
}
