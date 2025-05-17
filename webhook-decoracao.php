<?php



ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);



// Dados de conexão

$host = 'localhost';

$dbname = 'paymen58_fornecedores_nacionais';

$username = 'paymen58';

$password = 'u4q7+B6ly)obP_gxN9sNe';



// Secret da Yampi (caso ative depois)

$secret = 'wh_rweQPzt0jQ5lRY3ZbrNYZQFFdjc8ZjDWOguYm';



// Captura o corpo bruto da requisição

$rawPost = file_get_contents('php://input');



// (Desativado por enquanto)

// $headers = getallheaders();

// $signatureYampi = $headers['X-Yampi-Signature'] ?? '';

// $signatureLocal = hash_hmac('sha256', $rawPost, $secret);

// if (!hash_equals($signatureYampi, $signatureLocal)) {

//     http_response_code(401);

//     exit('Requisição não autorizada.');

// }



// Conecta ao banco

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {

    http_response_code(500);

    exit('Erro de conexão: ' . $conn->connect_error);

}



// Decodifica JSON

$data = json_decode($rawPost, true);

if (!$data) {

    http_response_code(400);

    exit('JSON inválido ou vazio.');

}



// Extrai dados

$orderCode = $data['code'] ?? null;

$email = $data['customer']['email'] ?? null;

$sku = $data['items'][0]['product']['sku'] ?? '';



if (!$orderCode || !$email) {

    http_response_code(400);

    exit('Dados incompletos.');

}



if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(400);

    exit('E-mail inválido.');

}



// Verifica duplicidade

$check = $conn->prepare("SELECT id FROM pedidos WHERE codigo_pedido = ?");

$check->bind_param("s", $orderCode);

$check->execute();

$check->store_result();



if ($check->num_rows > 0) {

    $check->close();

    http_response_code(200);

    exit('Pedido já registrado.');

}

$check->close();



// Insere pedido

$stmt = $conn->prepare("INSERT INTO pedidos (codigo_pedido, email_cliente, ebook, usado, sku) VALUES (?, ?, 'decoracao', 0, ?)");

$stmt->bind_param("sss", $orderCode, $email, $sku);



if (!$stmt->execute()) {

    http_response_code(500);

    exit('Erro ao inserir pedido.');

}

$stmt->close();



// Envia o e-mail com link de acesso

require_once '/home1/paymen58/agencialed.com/email/PHPMailer/PHPMailer.php';

require_once '/home1/paymen58/agencialed.com/email/PHPMailer/SMTP.php';

require_once '/home1/paymen58/agencialed.com/email/PHPMailer/Exception.php';



use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\Exception;



// Idealmente, use variável de ambiente ou um arquivo seguro para senhas

define('SMTP_PASSWORD', 'Lochayde@154719');



$mail = new PHPMailer(true);



try {

    $mail->CharSet = 'UTF-8'; // CORREÇÃO PARA UTF-8

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

    $mail->Subject = 'Pronto! Baixe Sua Lista de Fonecedores Agora!';

    $mail->Body = "

        <h2>Obrigado pela sua compra!</h2>

        <p>Seu código de pedido é: <strong>{$orderCode}</strong></p>

        <p>Para fazer o download da lista, clique no botão abaixo e informe seu código quando solicitado:</p>

        <p><a href='https://agencialed.com/fornecedores-decoracao.php' target='_blank' style='padding: 10px 20px; background-color: #38b97e; color: white; text-decoration: none; border-radius: 5px;'>Acessar PDF da Lista</a></p>

        <p><em>Guarde seu código com segurança. Ele só poderá ser usado uma vez.</em></p>

    ";



    $mail->send();

} catch (Exception $e) {

    error_log('Erro ao enviar e-mail: ' . $mail->ErrorInfo);

}



// Fecha conexão

$conn->close();



http_response_code(200);

echo 'Webhook processado com sucesso.';

?>

