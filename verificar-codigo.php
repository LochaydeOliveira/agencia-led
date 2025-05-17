<?php

header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'paymen58_fornecedores_nacionais';
$username = 'paymen58';
$password = 'u4q7+B6ly)obP_gxN9sNe';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit;
}

$codigoPedido = trim($_POST['codigo'] ?? '');
$emailCliente = trim($_POST['email'] ?? '');

if (!$codigoPedido || !$emailCliente) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Código e e-mail são obrigatórios.']);
    exit;
}

if (!filter_var($emailCliente, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'E-mail inválido.']);
    exit;
}

// Verifica se o código é válido e ainda não foi usado
$stmt = $conn->prepare("SELECT * FROM pedidos WHERE codigo_pedido = ? AND usado = 0");
$stmt->bind_param("s", $codigoPedido);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Código inválido.']);
} else {
    $row = $result->fetch_assoc();
    $sku = $row['sku'];

    // Atualiza para marcar como usado
    $update = $conn->prepare("UPDATE pedidos SET usado = 1, email_cliente = ? WHERE codigo_pedido = ?");
    $update->bind_param("ss", $emailCliente, $codigoPedido);
    $update->execute();

    // Define cookie válido por 30 minutos
    setcookie('ebook_decoracao_liberado', '1', time() + 1800, "/");

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Código validado com sucesso.',
        'sku' => $sku
    ]);
}

$stmt->close();
$conn->close();
