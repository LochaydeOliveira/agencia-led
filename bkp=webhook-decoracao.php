<?php

$secret = 'wh_rweQPzt0jQ5lRY3ZbrNYZQFFdjc8ZjDWOguYm';

$rawPost = file_get_contents('php://input');
$headers = getallheaders();

$signatureYampi = $headers['X-Yampi-Signature'] ?? '';

$signatureLocal = hash_hmac('sha256', $rawPost, $secret);


if (!hash_equals($signatureYampi, $signatureLocal)) {
    http_response_code(401);
    exit('Requisição não autorizada.');
}


$host = 'localhost';
$dbname = 'paymen58_fornecedores_nacionais';
$username = 'paymen58';
$password = 'u4q7+B6ly)obP_gxN9sNe';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}


$data = json_decode($rawPost, true);


$orderCode = $data['code'] ?? null;
$email = $data['customer']['email'] ?? null;
$sku = $data['items'][0]['product']['sku'] ?? '';



if (!$orderCode || !$email) {
    http_response_code(400);
    exit('Dados incompletos.');
}


$stmt = $conn->prepare("INSERT INTO pedidos (codigo_pedido, email_cliente, ebook, usado, sku) VALUES (?, ?, 'decoracao', 0, ?)");
$stmt->bind_param("sss", $orderCode, $email, $sku);
$stmt->execute();
$stmt->close();


require_once __DIR__ . '/email/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/email/PHPMailer/SMTP.php';
require_once __DIR__ . '/email/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host = 'smtp.zoho.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'contato@agencialed.com';
    $mail->Password = 'Lochayde@154719';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Remetente e destinatário
    $mail->setFrom('contato@agencialed.com', 'Agência LED');
    $mail->addAddress($email);

    // Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Sua lista está pronto para download!';
    $mail->Body    = "
        <h2>Obrigado pela sua compra!</h2>
        <p>Seu código de pedido é: <strong>{$orderCode}</strong></p>
        <p>Para fazer o download do seu eBook, clique no botão abaixo e informe seu código quando solicitado:</p>
        <p><a href='https://agencialed.com/fornecedores-decoracao' target='_blank' style='padding: 10px 20px; background-color: #38b97e; color: white; text-decoration: none; border-radius: 5px;'>Acessar PDF da Lista</a></p>
        <p><em>Guarde seu código com segurança. Ele só poderá ser usado uma vez.</em></p>
    ";

    $mail->send();
} catch (Exception $e) {
    error_log('Erro ao enviar e-mail: ' . $mail->ErrorInfo);
}

$conn->close();
http_response_code(200);
echo 'Webhook processado com sucesso.';
?>
