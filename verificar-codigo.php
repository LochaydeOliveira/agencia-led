<?php
header('Content-Type: application/json');

$codigo = $_POST['codigo'] ?? '';

if (!preg_match('/^\d{15}$/', $codigo)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Código inválido.']);
    exit;
}

// Configuração do banco - alterar para seu usuário e senha
$host = 'localhost';
$db = 'paymen58_lista_decoracao';
$user = 'paymen58';         // ajustar se necessário
$pass = 'u4q7+B6ly)obP_gxN9sNe'; // ajustar se necessário
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Verifica código
    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE codigo = ?");
    $stmt->execute([$codigo]);
    $pedido = $stmt->fetch();

    if (!$pedido) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Código não encontrado.']);
        exit;
    }

    if ($pedido['usado']) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Este código já foi utilizado.']);
        exit;
    }

    // Atualiza para usado
    $stmt = $pdo->prepare("UPDATE pedidos SET usado = 1 WHERE id = ?");
    $stmt->execute([$pedido['id']]);

    // Retorna link para download
    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Código verificado com sucesso! O download iniciará automaticamente.',
        'link' => 'https://agencialed.com/ebooks/fornecedores-nacionais-decoracao.pdf'
    ]);

} catch (PDOException $e) {
    error_log('Erro BD verificar-codigo.php: ' . $e->getMessage());
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao conectar ao banco de dados.']);
    exit;
}
