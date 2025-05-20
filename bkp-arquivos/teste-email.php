<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/email/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/email/PHPMailer/src/SMTP.php';
require __DIR__ . '/email/PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.zoho.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'contato@agencialed.com';
    $mail->Password   = 'Lochayde@154719'; 
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    $mail->setFrom('contato@agencialed.com', 'Agência LED');
    $mail->addAddress('lochaydeguerreiro2@gmail.com', 'Você');

    $mail->isHTML(true);
    $mail->Subject = 'Teste de e-mail PHPMailer';
    $mail->Body    = '<b>Funcionou!</b> Este é um teste de envio com PHPMailer e Zoho.';

    $mail->send();
    echo 'E-mail enviado com sucesso.';
} catch (Exception $e) {
    echo "Erro ao enviar: {$mail->ErrorInfo}";
}
