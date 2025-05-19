<?php
// ─────────────── CONFIGURAÇÕES GERAIS ───────────────
header('Content-Type: application/json');

// ─────────────── VERIFICAÇÃO DE MÉTODO ───────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
    exit;
}

// ─────────────── VALIDAÇÃO DO CÓDIGO ───────────────
$codigo = $_POST['codigo'] ?? '';
$codigo = preg_replace('/\D/', '', $codigo); // Mantém apenas dígitos

if (strlen($codigo) !== 15) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'O código deve conter exatamente 15 dígitos.']);
    exit;
}

// ─────────────── CONEXÃO COM BANCO DE DADOS ───────────────
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
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    error_log('Erro de conexão: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao conectar ao banco de dados.']);
    exit;
}

// ─────────────── VERIFICA E MARCA O CÓDIGO ───────────────
try {
    $stmt = $pdo->prepare("SELECT id, usado FROM pedidos WHERE codigo = ? LIMIT 1");
    $stmt->execute([$codigo]);
    $pedido = $stmt->fetch();

    if (!$pedido) {
        http_response_code(404);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Código não encontrado.']);
        exit;
    }

    if ((int)$pedido['usado'] === 1) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Este código já foi utilizado.']);
        exit;
    }

    $update = $pdo->prepare("UPDATE pedidos SET usado = 1 WHERE id = ?");
    $update->execute([$pedido['id']]);

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Código verificado com sucesso! O download iniciará automaticamente.',
        'link' => 'https://agencialed.com/download.php?codigo=' . $codigo

    ]);
} catch (Exception $e) {
    error_log('Erro na verificação: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno no servidor.']);
}
