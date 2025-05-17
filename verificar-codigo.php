<?php

$host = 'localhost';
$dbname = 'paymen58_fornecedores_nacionais';
$username = 'paymen58';
$password = 'u4q7+B6ly)obP_gxN9sNe';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

$codigo = $_POST['codigo'] ?? '';

if (!$codigo) {
    echo 'Código não informado.';
    exit;
}

$stmt = $conn->prepare("SELECT * FROM codigos_pedidos WHERE codigo = ? AND usado = 0");
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo 'Código inválido ou já utilizado.';
} else {
    $update = $conn->prepare("UPDATE codigos_pedidos SET usado = 1 WHERE codigo = ?");
    $update->bind_param("s", $codigo);
    $update->execute();

    // Define cookie para liberar o ebook por 30 minutos
    setcookie('ebook_liberado', '1', time() + 1800, "/");

    echo 'sucesso';
}

$stmt->close();
$conn->close();
