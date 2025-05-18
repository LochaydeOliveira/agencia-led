<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Chave secreta da Yampi
$chave_secreta = 'wh_rweQPzt0jQ5lRY3ZbrNYZQFFdjc8ZjDWOguYm';

// Lê o corpo bruto da requisição
$body = file_get_contents('php://input');

// Verifica a assinatura HMAC SHA256 enviada pela Yampi
$assinatura_recebida = $_SERVER['HTTP_X_YAMPI_SIGNATURE'] ?? '';
$assinatura_calculada = hash_hmac('sha256', $body, $chave_secreta);

if (!hash_equals($assinatura_calculada, $assinatura_recebida)) {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Assinatura inválida.']);
    exit;
}

// Decodifica o JSON após validar a assinatura
$data = json_decode($body, true);

// Log de debug detalhado
file_put_contents('log_yampi.txt', date('Y-m-d H:i:s') . "\n" . print_r($data, true) . "\n\n", FILE_APPEND);

// Verifica se é o evento correto
if (!isset($data['event']) || $data['event'] !== 'order.created') {
    echo json_encode(['status' => 'ignorado', 'mensagem' => 'Evento não é order.created.']);
    exit;
}

// Extrai dados essenciais
$codigo = trim($data['data']['reference'] ?? '');
$email = filter_var($data['data']['customer']['email'] ?? '', FILTER_VALIDATE_EMAIL);
$sku = trim($data['data']['products'][0]['sku'] ?? '');

// Validação
if (!$codigo || !$email || !$sku) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados inválidos ou incompletos.']);
    exit;
}

// Configuração do banco
$host = 'localhost';
$dbname = 'paymen58_lista_decoracao';
$username = 'paymen58';
$password = 'u4q7+B6ly)obP_gxN9sNe';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Verifica duplicidade
    $stmt = $pdo->prepare("SELECT id FROM pedidos WHERE codigo = ?");
    $stmt->execute([$codigo]);

    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(['status' => 'ok', 'mensagem' => 'Pedido já registrado.']);
        exit;
    }

    // Insere novo pedido
    $stmt = $pdo->prepare("INSERT INTO pedidos (codigo, email, sku, usado) VALUES (?, ?, ?, 0)");
    $stmt->execute([$codigo, $email, $sku]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no banco de dados.']);
    error_log("Erro BD webhook.php: " . $e->getMessage());
    exit;
}

// Envio de e-mail
require_once '/home1/paymen58/agencialed.com/email/PHPMailer/PHPMailer.php';
require_once '/home1/paymen58/agencialed.com/email/PHPMailer/SMTP.php';
require_once '/home1/paymen58/agencialed.com/email/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('SMTP_PASSWORD', 'Lochayde@154719');

$mail = new PHPMailer(true);
try {
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = 'smtp.zoho.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'contato@agencialed.com';
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('contato@agencialed.com', 'Agência LED');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Pronto! Baixe Sua Lista de Fornecedores Agora!';
    $mail->Body = "
        <h2>Obrigado pela sua compra!</h2>
        <p>Seu código de pedido é: <strong>{$codigo}</strong></p>
        <p>Para baixar sua lista, clique no botão abaixo e informe o código quando solicitado:</p>
        <p><a href='https://agencialed.com/fornecedores-decoracao.php' target='_blank' style='padding: 10px 20px; background-color: #38b97e; color: white; text-decoration: none; border-radius: 5px;'>Acessar PDF da Lista</a></p>
        <p><em>Guarde seu código com segurança. Ele só poderá ser usado uma vez.</em></p>
    ";
    $mail->send();
} catch (Exception $e) {
    error_log('Erro ao enviar e-mail webhook.php: ' . $mail->ErrorInfo);
}

http_response_code(200);
echo json_encode(['status' => 'sucesso', 'mensagem' => 'Pedido registrado e e-mail enviado.']);
