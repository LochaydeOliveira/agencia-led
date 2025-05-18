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

// Define charset seguro
$conn->set_charset('utf8mb4');

$codigoPedido = trim($_POST['codigo'] ?? '');

// Validação inicial: apenas o código
if (!$codigoPedido) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'O código do pedido é obrigatório.']);
    exit;
}

// Verifica se o código tem exatamente 15 dígitos numéricos
if (!preg_match('/^\d{15}$/', $codigoPedido)) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Formato de código inválido.']);
    exit;
}

// Verifica se o código existe
$stmt = $conn->prepare("SELECT * FROM pedidos WHERE codigo_pedido = ?");
$stmt->bind_param("s", $codigoPedido);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Código não encontrado.']);
} else {
    $row = $result->fetch_assoc();

    if ((int)$row['usado'] === 1) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Este código já foi utilizado.']);
        exit;
    }

    $sku = $row['sku'];

    // Atualiza para marcar como usado (sem email)
    $update = $conn->prepare("UPDATE pedidos SET usado = 1 WHERE codigo_pedido = ?");
    $update->bind_param("s", $codigoPedido);

    if ($update->execute()) {
        // Define cookie válido por 30 minutos com opções seguras
        setcookie('ebook_decoracao_liberado', '1', [
            'expires' => time() + 1800,
            'path' => '/',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Lax'
        ]);

        echo json_encode([
            'status' => 'sucesso',
            'mensagem' => 'Código validado com sucesso.',
            'sku' => $sku
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao atualizar o pedido.']);
    }

    $update->close();
}

$stmt->close();
$conn->close();
