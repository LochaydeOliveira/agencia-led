<?php
header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fornecedores-decoracao.php');
    exit;
}

$numero_pedido = trim($_POST['numero_pedido'] ?? '');

if (empty($numero_pedido)) {
    echo "<p style='color:red; text-align:center;'>Número do pedido é obrigatório.</p>";
    echo "<p style='text-align:center;'><a href='fornecedores-decoracao.php'>Voltar</a></p>";
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=paymen58_lista_decoracao;charset=utf8mb4', 'paymen58', 'u4q7+B6ly)obP_gxN9sNe', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE numero_pedido = ? AND usado = 0");
    $stmt->execute([$numero_pedido]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pedido) {
        // Pedido válido → redirecionar com o número do pedido na URL para download
        header("Location: downloads.php?pedido=" . urlencode($numero_pedido));
        exit;
    } else {
        echo "<p style='color:red; text-align:center;'>Número de pedido inválido ou já utilizado.</p>";
        echo "<p style='text-align:center;'><a href='fornecedores-decoracao.php'>Tentar novamente</a></p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red; text-align:center;'>Erro no banco de dados: " . htmlspecialchars($e->getMessage()) . "</p>";
}
