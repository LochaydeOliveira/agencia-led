<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Conexão com o banco
$host = 'localhost';
$dbname = 'paymen58_lista_decoracao';
$username = 'paymen58';
$password = 'u4q7+B6ly)obP_gxN9sNe';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro de conexão com o banco.']);
    exit;
}
$conn->set_charset('utf8mb4');

// Lê os dados do webhook
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['code'])) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados inválidos ou incompletos.']);
    exit;
}

$codigo = $data['code'];
$email = $data['customer']['email'] ?? null;
$sku = $data['items'][0]['product']['sku'] ?? '';

if (!$codigo || !$email) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Código ou e-mail ausente.']);
    exit;
}

// Verifica duplicidade
$check = $conn->prepare("SELECT id FROM pedidos WHERE codigo = ?");
$check->bind_param("s", $codigo);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['status' => 'ok', 'mensagem' => 'Pedido já registrado.']);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Insere novo pedido
$insert = $conn->prepare("INSERT INTO pedidos (codigo, email, sku, usado) VALUES (?, ?, ?, 0)");
$insert->bind_param("sss", $codigo, $email, $sku);
$insert->execute();
$insert->close();

// Envia e-mail
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
    error_log('Erro ao enviar e-mail: ' . $mail->ErrorInfo);
}

$conn->close();
echo json_encode(['status' => 'sucesso', 'mensagem' => 'Pedido registrado e e-mail enviado.']);
?>
