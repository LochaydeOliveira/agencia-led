<?php
// verificar-codigo.php
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

$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'paymen58_lista_decoracao',
    'user' => 'paymen58',
    'pass' => 'u4q7+B6ly)obP_gxN9sNe',
    'charset' => 'utf8mb4',
];

$dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";

try {
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    error_log('Erro de conexão: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao conectar ao banco de dados.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, usado FROM pedidos WHERE numero = ? LIMIT 1");
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

    $update = $pdo->prepare("UPDATE pedidos SET usado = 1 WHERE id = ?");
    $update->execute([$pedido['id']]);

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
