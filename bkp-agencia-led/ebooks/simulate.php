<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Mailer.php';

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método não permitido');
}

// Obtém o evento a ser simulado
$event = $_POST['event'] ?? '';
if (!in_array($event, ['order.created', 'order.paid', 'order.status.updated'])) {
    die('Evento inválido');
}

// Cria um payload de exemplo
$payload = [
    'event' => $event,
    'time' => date('Y-m-d H:i:s'),
    'merchant' => [
        'id' => 339833,
        'alias' => YAMPI_STORE_ALIAS
    ],
    'resource' => [
        'id' => rand(100000000, 999999999),
        'merchant_id' => 339833,
        'customer_id' => 229164211,
        'status_id' => 3,
        'number' => rand(100000000, 999999999),
        'value_total' => 19.90,
        'customer' => [
            'data' => [
                'id' => 229164211,
                'name' => 'Cliente Teste',
                'email' => 'teste@exemplo.com'
            ]
        ],
        'items' => [
            'data' => [
                [
                    'product_id' => YAMPI_PRODUCT_ID,
                    'title' => 'Lista Secreta de Forcedores Nacionais - Decoração'
                ]
            ]
        ],
        'status' => [
            'data' => [
                'alias' => $event === 'order.paid' ? 'paid' : 'waiting_payment'
            ]
        ]
    ]
];

// Gera a assinatura do webhook
$signature = hash_hmac('sha256', json_encode($payload), YAMPI_WEBHOOK_SECRET);

// Simula a requisição para o webhook
$ch = curl_init(SITE_URL . '/webhook.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Yampi-Signature: ' . $signature
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Exibe o resultado
echo "<h2>Simulação de Webhook</h2>";
echo "<p>Evento: {$event}</p>";
echo "<p>Código HTTP: {$httpCode}</p>";
echo "<p>Resposta: {$response}</p>";
echo "<p>Payload:</p>";
echo "<pre>" . json_encode($payload, JSON_PRETTY_PRINT) . "</pre>";
echo "<p><a href='simulate.php'>Voltar</a></p>"; 