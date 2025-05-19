<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fornecedores-decoracao.php');
    exit;
}

$numero_pedido = $_POST['numero_pedido'] ?? '';

if (empty($numero_pedido)) {
    die('Número do pedido é obrigatório.');
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=paymen58_lista_decoracao;charset=utf8mb4', 'paymen58', 'u4q7+B6ly)obP_gxN9sNe', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE numero_pedido = ? AND usado = 0");
    $stmt->execute([$numero_pedido]);
    $pedido = $stmt->fetch();

    if ($pedido) {
        // Pedido válido → redirecionar com o número do pedido na URL
        header("Location: downloads.php?pedido=$numero_pedido");
        exit;
    } else {
        echo "<p style='color:red; text-align:center;'>Número de pedido inválido ou já utilizado.</p>";
        echo "<p style='text-align:center;'><a href='fornecedores-decoracao.php'>Tentar novamente</a></p>";
    }
} catch (Exception $e) {
    die("Erro no banco de dados: " . $e->getMessage());
}
