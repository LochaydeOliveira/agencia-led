<?php
// verificar-codigo.php

header('Content-Type: application/json');

$codigo = $_POST['codigo'] ?? '';

if (!preg_match('/^\d{15}$/', $codigo)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Código inválido.']);
    exit;
}

// Conexão
$host = 'localhost';
$db = 'paymen58_lista_decoracao';
$user = 'SEU_USUARIO';
$pass = 'SUA_SENHA';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao conectar.']);
    exit;
}

// Verifica se o código já foi usado
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE codigo = ?");
$stmt->execute([$codigo]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Código não encontrado.']);
    exit;
}

if ($pedido['usado']) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Este código já foi utilizado.']);
    exit;
}

// Atualiza para "usado"
$stmt = $pdo->prepare("UPDATE pedidos SET usado = 1 WHERE id = ?");
$stmt->execute([$pedido['id']]);

// Resposta com link para download
echo json_encode([
    'status' => 'sucesso',
    'mensagem' => 'Código verificado com sucesso! O download iniciará automaticamente.',
    'link' => 'https://agencialed.com/ebooks/fornecedores-nacionais-decoracao.pdf'
]);
