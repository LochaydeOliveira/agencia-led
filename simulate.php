<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método não permitido');
}

$event = $_POST['event'] ?? 'order.paid';

$allowed_events = ['order.created', 'order.paid'];
if (!in_array($event, $allowed_events)) {
    die('Evento inválido');
}

$db = Database::getInstance();
$conn = $db->getConnection();

if ($event === 'order.created') {
    $productId = $_POST['product_id'] ?? '40621209';
    $orderId = rand(100000000, 999999999);
    $orderNumber = rand(100000000, 999999999);
    $statusAlias = 'waiting_payment';

    // Insere na tabela orders para manter histórico da simulação
    $stmt = $conn->prepare("INSERT INTO orders (yampi_order_id, order_number, customer_name, customer_email, status, created_at, product_id) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
    $stmt->execute([$orderId, $orderNumber, 'Lochayde Teste', 'lochaydeguerreiro@hotmail.com', $statusAlias, $productId]);

} else { // order.paid
    // Pega último pedido criado
    $stmt = $conn->query("SELECT yampi_order_id, order_number, product_id FROM orders ORDER BY id DESC LIMIT 1");
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die('Nenhum pedido encontrado para simular pagamento');
    }

    $orderId = $order['yampi_order_id'];
    $orderNumber = $order['order_number'];
    $productId = $order['product_id'];
    $statusAlias = 'paid';
}

$payload = [
    'event' => $event,
    'time' => date('Y-m-d H:i:s'),
    'merchant' => ['id' => 339833, 'alias' => 'tutoriais-store'],
    'resource' => [
        'id' => $orderId,
        'merchant_id' => 339833,
        'customer_id' => 229164211,
        'status_id' => 3,
        'number' => $orderNumber,
        'value_total' => 19.90,
        'customer' => ['data' => ['id' => 229164211, 'name' => 'Lochayde Teste', 'email' => 'lochaydeguerreiro@hotmail.com']],
        'items' => ['data' => [['product_id' => $productId, 'title' => 'Produto Teste']]],
        'status' => ['data' => ['alias' => $statusAlias]]
    ]
];

$signature = hash_hmac('sha256', json_encode($payload), WEBHOOK_SECRET);

$ch = curl_init(WEBHOOK_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Yampi-Signature: ' . $signature
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Resultado Simulação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="mb-4">
        <?php if ($http_code === 200): ?>
            <div class="alert alert-success">
                ✅ Simulação de <strong><?php echo htmlspecialchars($event); ?></strong> com produto <strong><?php echo htmlspecialchars($productId); ?></strong> feita com sucesso!
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                ❌ Falha na simulação. Código HTTP: <?php echo $http_code; ?>
            </div>
        <?php endif; ?>
    </div>

    <h4>Resposta:</h4>
    <pre><?php echo htmlspecialchars($response); ?></pre>

    <h4>Payload Enviado:</h4>
    <pre><?php echo htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT)); ?></pre>

    <a href="simulate.html" class="btn btn-secondary mt-3">← Voltar</a>
</div>
</body>
</html>
