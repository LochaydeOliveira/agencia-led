<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Mailer.php';

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método não permitido');
}

// Obtém o evento a ser simulado
$event = $_POST['event'] ?? '';

// Lista de eventos permitidos
$allowed_events = ['order.created', 'order.paid', 'order.status.updated'];

if (!in_array($event, $allowed_events)) {
    die('Evento inválido');
}

// Obtém o último pedido criado
$db = Database::getInstance();
$conn = $db->getConnection();
$sql = "SELECT yampi_order_id, order_number FROM orders ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$lastOrder = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não houver pedido criado e o evento for order.paid ou order.status.updated
if (!$lastOrder && ($event === 'order.paid' || $event === 'order.status.updated')) {
    die('Nenhum pedido encontrado para simular pagamento ou atualização de status');
}

// Gera um novo ID e número de pedido apenas para order.created
$orderId = $event === 'order.created' ? rand(100000000, 999999999) : $lastOrder['yampi_order_id'];
$orderNumber = $event === 'order.created' ? rand(100000000, 999999999) : $lastOrder['order_number'];



// Define o status correto conforme o evento
$statusAlias = match ($event) {
    'order.created' => 'waiting_payment',
    'order.paid' => 'paid',
    'order.status.updated' => 'completed', // ou outro status conforme seu sistema
    default => 'unknown',
};

// Cria um payload de exemplo
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


// Gera a assinatura do webhook
$signature = hash_hmac('sha256', json_encode($payload), WEBHOOK_SECRET);

// Simula a requisição para o webhook
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

// Exibe o resultado
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simulação de Webhook</title>
    <style>

</head>
<body>
    <div class="container">
        <h1>Simulação de Webhook</h1>
        <p><strong>Evento:</strong> <?php echo htmlspecialchars($event); ?></p>
        <p><strong>Código HTTP:</strong> <?php echo $http_code; ?></p>
        
        <h2>Resposta:</h2>
        <pre><?php echo htmlspecialchars($response); ?></pre>
        
        <h2>Payload:</h2>
        <pre><?php echo htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT)); ?></pre>
        
        <a href="simulate.html" class="back-link">Voltar</a>
    </div>
</body>
</html> 