<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
    exit;
}

$numero = $_POST['codigo'] ?? '';
$numero = preg_replace('/\D/', '', $numero);

if (strlen($numero) < 5 || strlen($numero) > 20) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Número do pedido inválido.']);
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=paymen58_lista_decoracao;charset=utf8mb4', 'paymen58', 'u4q7+B6ly)obP_gxN9sNe', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $stmt = $pdo->prepare("SELECT id, usado FROM pedidos WHERE numero_pedido = ? LIMIT 1");
    $stmt->execute([$numero]);
    $pedido = $stmt->fetch();

    if (!$pedido) {
        http_response_code(404);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Número do pedido não encontrado.']);
        exit;
    }

    if ((int)$pedido['usado'] === 1) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Este número de pedido já foi utilizado.']);
        exit;
    }

    $pdo->prepare("UPDATE pedidos SET usado = 1 WHERE id = ?")->execute([$pedido['id']]);

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Pedido verificado com sucesso! O download iniciará automaticamente.',
        'link' => 'https://agencialed.com/download.php?numero=' . $numero,
    ]);

} catch (Exception $e) {
    error_log('Erro na verificação: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno no servidor.']);
}
