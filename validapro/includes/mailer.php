<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendRecoveryEmail($email, $nome, $token) {
    require __DIR__ . '/email_config.php'; // Define as constantes SMTP_*

    $link = "https://agencialed.com/validapro/redefinir_senha.php?token=" . urlencode($token);

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($email, $nome);
        $mail->Subject = 'Recuperação de Senha - ValidaPro';
        $mail->isHTML(true);
        $mail->Body = "
            <h2>Olá, {$nome}</h2>
            <p>Você solicitou a recuperação de senha.</p>
            <p><a href='{$link}' target='_blank'>Clique aqui para redefinir sua senha</a></p>
            <p>Se não foi você, apenas ignore este e-mail.</p>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}
?>