<?php
header('Content-Type: application/json');

// Verifica método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
    exit;
}

$codigo = $_POST['codigo'] ?? '';

// Remove tudo que não for número
$codigo = preg_replace('/\D/', '', $codigo);

// Validação do código
if (!preg_match('/^\d{15}$/', $codigo)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'O código deve conter exatamente 15 dígitos.']);
    exit;
}

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'paymen58_lista_decoracao';
$username = 'paymen58';
$password = 'u4q7+B6ly)obP_gxN9sNe';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log('Erro de conexão: ' . $e->getMessage());
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao conectar ao banco de dados.']);
    exit;
}

try {
    // Verifica se o código existe e ainda não foi usado
    $stmt = $pdo->prepare("SELECT id, usado FROM pedidos WHERE codigo = ? LIMIT 1");
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

    // Marca como usado e registra data/hora
    $update = $pdo->prepare("UPDATE pedidos SET usado = 1, data_uso = NOW() WHERE id = ?");
    $update->execute([$pedido['id']]);

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Código verificado com sucesso! O download iniciará automaticamente.',
        'link' => 'https://agencialed.com/ebooks/fornecedores-nacionais-decoracao.pdf'
    ]);
} catch (Exception $e) {
    error_log('Erro na verificação: ' . $e->getMessage());
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno no servidor.']);
    exit;
}
