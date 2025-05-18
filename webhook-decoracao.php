<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Configuração do banco
$host = 'localhost';
$dbname = 'paymen58_lista_decoracao';
$username = 'paymen58';
$password = 'u4q7+B6ly)obP_gxN9sNe';
$charset = 'utf8mb4';

// Lê dados JSON do webhook
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['code']) || empty($data['customer']['email']) || empty($data['items'][0]['product']['sku'])) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados inválidos ou incompletos.']);
    exit;
}

$codigo = trim($data['code']);
$email = filter_var($data['customer']['email'], FILTER_VALIDATE_EMAIL);
$sku = trim($data['items'][0]['product']['sku']);

if (!$email) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'E-mail inválido.']);
    exit;
}

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
    // Não interrompe o processo, mas avisa no log
}

echo json_encode(['status' => 'sucesso', 'mensagem' => 'Pedido registrado e e-mail enviado.']);
