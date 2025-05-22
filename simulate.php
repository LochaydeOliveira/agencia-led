<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método não permitido');
}

$event = $_POST['event'] ?? '';
$allowed_events = ['order.created', 'order.paid', 'order.status.updated'];

if (!in_array($event, $allowed_events)) {
    die('Evento inválido');
}

$db = Database::getInstance();
$conn = $db->getConnection();
$sql = "SELECT yampi_order_id, order_number FROM orders ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$lastOrder = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lastOrder && ($event === 'order.paid' || $event === 'order.status.updated')) {
    die('Nenhum pedido encontrado para simular pagamento ou atualização de status');
}

$orderId = $event === 'order.created' ? rand(100000000, 999999999) : $lastOrder['yampi_order_id'];
$orderNumber = $event === 'order.created' ? rand(100000000, 999999999) : $lastOrder['order_number'];

$statusAlias = match ($event) {
    'order.created' => 'waiting_payment',
    'order.paid' => 'paid',
    'order.status.updated' => 'completed',
    default => 'unknown',
};

$payload = [
    'event' => $event,
    'time' => date('Y-m-d H:i:s'),
    'merchant' => [
        'id' => 339833,
        'alias' => 'tutoriais-store'
    ],
    'resource' => [
        'id' => $orderId,
        'merchant_id' => 339833,
        'customer_id' => 229164211,
        'status_id' => 3,
        'number' => $orderNumber,
        'value_total' => 19.90,
        'customer' => [
            'data' => [
                'id' => 229164211,
                'name' => 'Lochayde Teste',
                'email' => 'lochaydeguerreiro@hotmail.com'
            ]
        ],
        'items' => [
            'data' => [
                [
                    'product_id' => '40621209',
                    'title' => 'Lista Secreta de Forcedores Nacionais - Decoração'
                ]
            ]
        ],
        'status' => [
            'data' => [
                'alias' => $statusAlias
            ]
        ]
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
    <title>Simulação de Webhook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="mb-4">
            <?php if ($http_code === 200): ?>
                <div class="alert alert-success">
                    ✅ Simulação do evento <strong><?php echo htmlspecialchars($event); ?></strong> executada com sucesso!
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    ❌ A simulação do evento <strong><?php echo htmlspecialchars($event); ?></strong> falhou. Código HTTP: <?php echo $http_code; ?>
                </div>
            <?php endif; ?>
        </div>

        <h2>Detalhes da Simulação</h2>
        <p><strong>Evento:</strong> <?php echo htmlspecialchars($event); ?></p>
        <p><strong>Código HTTP:</strong> <?php echo $http_code; ?></p>

        <h4>Resposta:</h4>
        <pre><?php echo htmlspecialchars($response); ?></pre>

        <h4>Payload Enviado:</h4>
        <pre><?php echo htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT)); ?></pre>

        <a href="simulate.html" class="btn btn-secondary mt-3">← Voltar</a>
    </div>
</body>
</html>
