<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database.php';

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

// Conecta ao banco de dados
$db = Database::getInstance();
$conn = $db->getConnection();

// Define valores padrão
$email = 'lochaydeguerreiro@hotmail.com';
$nome = 'Lochayde Teste';
$product_id = YAMPI_PRODUCT_ID;
$product_title = 'Lista Secreta de Fornecedores Nacionais - Decoração';

// Obtém o último pedido criado
$sql = "SELECT yampi_order_id, order_number FROM orders ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$lastOrder = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não houver pedido ainda e o evento exigir um, exibe erro
if (!$lastOrder && $event !== 'order.created') {
    die('Nenhum pedido encontrado para simular este evento.');
}

// Define os dados do pedido dependendo do evento
$yampi_order_id = $event === 'order.created' ? rand(100000000, 999999999) : $lastOrder['yampi_order_id'];
$order_number = $event === 'order.created' ? rand(100000000, 999999999) : $lastOrder['order_number'];

// Monta o payload
$payload = [
    'event' => $event,
    'time' => date('Y-m-d H:i:s'),
    'merchant' => [
        'id' => 339833,
        'alias' => YAMPI_STORE_ALIAS
    ],
    'resource' => [
        'id' => $yampi_order_id,
        'merchant_id' => 339833,
        'customer_id' => 229164211,
        'status_id' => 3,
        'number' => $order_number,
        'value_total' => 19.90,
        'customer' => [
            'data' => [
                'id' => 229164211,
                'name' => $nome,
                'email' => $email
            ]
        ],
        'items' => [
            'data' => [
                [
                    'product_id' => $product_id,
                    'title' => $product_title
                ]
            ]
        ],
        'status' => [
            'data' => [
                'alias' => $event === 'order.status.updated' ? 'paid' : 'created'
            ]
        ]
    ]
];

// Gera a assinatura do webhook
$signature = hash_hmac('sha256', json_encode($payload), WEBHOOK_SECRET);

// Simula a requisição
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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Simulação de Webhook</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        pre { background: #f8f8f8; padding: 10px; border-radius: 4px; }
        .back-link { display: inline-block; margin-top: 20px; color: #0066cc; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
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

    <a href="simulate.html" class="back-link">← Voltar</a>
</div>
</body>
</html>
