<?php
/**
 * Exemplo de configuração do PHPMailer para envio de emails
 * 
 * Para usar este arquivo:
 * 1. Instale o PHPMailer via Composer: composer require phpmailer/phpmailer
 * 2. Descomente o código abaixo
 * 3. Configure suas credenciais SMTP
 */

/*
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';

function sendEmailWithPHPMailer($to_email, $to_name, $subject, $message) {
    $mail = new PHPMailer(true);
    
    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // ou seu servidor SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seu-email@gmail.com'; // seu email
        $mail->Password   = 'sua-senha-app'; // sua senha de app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // Remetente
        $mail->setFrom('seu-email@gmail.com', 'Checklist do Produto');
        $mail->addReplyTo('seu-email@gmail.com', 'Checklist do Produto');
        
        // Destinatário
        $mail->addAddress($to_email, $to_name);
        
        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro no envio de email: {$mail->ErrorInfo}");
        return false;
    }
}

// Exemplo de uso:
function sendAccessEmail($email, $password, $name) {
    $subject = "Acesso ao Checklist do Produto Lucrativo";
    $message = "
    <html>
    <head>
        <title>Acesso ao Checklist</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='margin: 0; font-size: 28px;'>🎯 Checklist do Produto Lucrativo</h1>
                <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>Seu acesso está pronto!</p>
            </div>
            
            <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;'>
                <h2 style='color: #2c3e50; margin-top: 0;'>Olá, {$name}!</h2>
                
                <p>Seu acesso ao <strong>Checklist do Produto Lucrativo</strong> foi criado com sucesso!</p>
                
                <div style='background: white; border: 2px solid #e9ecef; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                    <h3 style='color: #495057; margin-top: 0;'>📋 Suas Credenciais de Acesso:</h3>
                    <p><strong>URL:</strong> <a href='https://seudominio.com/app/' style='color: #007bff;'>https://seudominio.com/app/</a></p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Senha:</strong> {$password}</p>
                </div>
                
                <div style='background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 15px; margin: 20px 0;'>
                    <h4 style='color: #155724; margin-top: 0;'>🚀 O que você pode fazer:</h4>
                    <ul style='color: #155724; margin: 10px 0;'>
                        <li>Preencher perguntas de qualificação do produto</li>
                        <li>Marcar itens do checklist de pontuação</li>
                        <li>Obter nota final automática (0-10)</li>
                        <li>Ver análise interpretativa do resultado</li>
                    </ul>
                </div>
                
                <p style='margin-bottom: 0;'><strong>Sucesso e boas vendas! 🎉</strong></p>
            </div>
            
            <div style='text-align: center; margin-top: 20px; color: #6c757d; font-size: 12px;'>
                <p>Este é um email automático. Não responda a esta mensagem.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmailWithPHPMailer($email, $name, $subject, $message);
}
*/

// Função alternativa usando mail() do PHP (mais simples)
function sendEmailSimple($to_email, $to_name, $subject, $message) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Checklist do Produto <noreply@seudominio.com>\r\n";
    $headers .= "Reply-To: noreply@seudominio.com\r\n";
    
    return mail($to_email, $subject, $message, $headers);
}
?> 