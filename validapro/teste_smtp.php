<?php
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configurações de teste (use as mesmas do email_config.php)
$smtp_host = 'smtp.zoho.com';
$smtp_user = 'contato@agencialed.com';
$smtp_pass = 'Lochayde@154719';
$smtp_secure = 'tls';
$smtp_port = 587;
$from_email = 'contato@agencialed.com';
$from_name = 'Teste SMTP';
$to_email = 'lochaydeguerreiro@hotmail.com'; // E-mail real do usuário
$to_name = 'Você';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_user;
    $mail->Password = $smtp_pass;
    $mail->SMTPSecure = $smtp_secure;
    $mail->Port = $smtp_port;
    $mail->setFrom($from_email, $from_name);
    $mail->addAddress($to_email, $to_name);
    $mail->Subject = 'Teste SMTP Zoho';
    $mail->Body = 'Funcionou! Se você recebeu este e-mail, o SMTP está liberado.';
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) { error_log("PHPMailer Debug [$level]: $str"); };
    $mail->send();
    echo 'Enviado com sucesso!';
} catch (Exception $e) {
    echo 'Erro: ' . $mail->ErrorInfo;
} 