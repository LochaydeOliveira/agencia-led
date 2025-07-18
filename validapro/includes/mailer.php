<?php
/**
 * Sistema de Email do ValidaPro
 * Configurado para Zoho Mail com PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Carregar autoload do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Carregar configurações de email
require_once __DIR__ . '/email_config.php';

/**
 * Função principal para envio de emails com PHPMailer
 */
function sendEmailWithPHPMailer($to_email, $to_name, $subject, $message) {
    $mail = new PHPMailer(true);
    
    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Configurações de timeout e debug
        $mail->Timeout = SMTP_TIMEOUT;
        $mail->SMTPDebug = SMTP_DEBUG ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;
        
        // Configurações de segurança
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Remetente
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addReplyTo(FROM_EMAIL, FROM_NAME);
        
        // Destinatário
        $mail->addAddress($to_email, $to_name);
        
        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        // Enviar email
        $result = $mail->send();
        
        // Log de sucesso
        error_log("Email enviado com sucesso para: $to_email");
        return true;
        
    } catch (Exception $e) {
        // Log detalhado do erro
        error_log("Erro no envio de email para $to_email: " . $e->getMessage());
        error_log("Detalhes do erro: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Função específica para envio de email de recuperação de senha
 */
function sendPasswordRecoveryEmail($email, $name, $token) {
    $link = APP_URL . 'redefinir_senha.php?token=' . $token;
    $assunto = 'Recuperação de Senha - ValidaPro';
    
    $corpo = "
    <!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Recuperação de Senha - ValidaPro</title>
    </head>
    <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
            <!-- Header -->
            <div style='background: linear-gradient(135deg, #f97316 0%, #ec4899 100%); padding: 40px 30px; text-align: center;'>
                <h1 style='color: white; margin: 0; font-size: 32px; font-weight: bold;'>ValidaPro</h1>
                <p style='color: white; margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>Recuperação de Senha</p>
            </div>
            
            <!-- Conteúdo -->
            <div style='padding: 40px 30px;'>
                <h2 style='color: #2c3e50; margin-top: 0; font-size: 24px;'>Olá, {$name}!</h2>
                
                <p style='color: #555; font-size: 16px; line-height: 1.6;'>
                    Recebemos uma solicitação para redefinir sua senha no <strong>ValidaPro</strong>.
                </p>
                
                <p style='color: #555; font-size: 16px; line-height: 1.6;'>
                    Para criar uma nova senha, clique no botão abaixo:
                </p>
                
                <!-- Botão -->
                <div style='text-align: center; margin: 40px 0;'>
                    <a href='{$link}' style='display: inline-block; background: linear-gradient(135deg, #f97316 0%, #ec4899 100%); color: white; padding: 18px 36px; text-decoration: none; border-radius: 8px; font-size: 18px; font-weight: bold;'>
                        🔐 Redefinir Senha
                    </a>
                </div>
                
                <!-- Link alternativo -->
                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 30px 0;'>
                    <p style='color: #666; font-size: 14px; margin: 0 0 10px 0;'><strong>Ou copie e cole este link no navegador:</strong></p>
                    <p style='color: #007bff; font-size: 14px; word-break: break-all; margin: 0;'>{$link}</p>
                </div>
                
                <!-- Avisos -->
                <div style='background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin: 30px 0;'>
                    <h4 style='color: #856404; margin-top: 0; font-size: 16px;'>⚠️ Importante:</h4>
                    <ul style='color: #856404; margin: 10px 0; padding-left: 20px;'>
                        <li>Este link expira em 1 hora</li>
                        <li>Se você não solicitou esta recuperação, ignore este email</li>
                        <li>Nunca compartilhe este link com outras pessoas</li>
                    </ul>
                </div>
            </div>
            
            <!-- Footer -->
            <div style='background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;'>
                <p style='color: #6c757d; font-size: 14px; margin: 0;'>
                    Este é um email automático do ValidaPro. Não responda a esta mensagem.
                </p>
                <p style='color: #6c757d; font-size: 12px; margin: 10px 0 0 0;'>
                    © 2024 ValidaPro - Todos os direitos reservados
                </p>
            </div>
        </div>
    </body>
    </html>";
    
    return sendEmailWithPHPMailer($email, $name, $assunto, $corpo);
}

/**
 * Função para envio de email de acesso (quando usuário é criado)
 */
function sendAccessEmail($email, $password, $name) {
    $assunto = "Acesso ao ValidaPro - Suas Credenciais";
    
    $corpo = "
    <!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Acesso ao ValidaPro</title>
    </head>
    <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
            <!-- Header -->
            <div style='background: linear-gradient(135deg, #f97316 0%, #ec4899 100%); padding: 40px 30px; text-align: center;'>
                <h1 style='color: white; margin: 0; font-size: 32px; font-weight: bold;'>🎯 ValidaPro</h1>
                <p style='color: white; margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>Seu acesso está pronto!</p>
            </div>
            
            <!-- Conteúdo -->
            <div style='padding: 40px 30px;'>
                <h2 style='color: #2c3e50; margin-top: 0; font-size: 24px;'>Olá, {$name}!</h2>
                
                <p style='color: #555; font-size: 16px; line-height: 1.6;'>
                    Seu acesso ao <strong>ValidaPro</strong> foi criado com sucesso!
                </p>
                
                <!-- Credenciais -->
                <div style='background-color: #f8f9fa; border: 2px solid #e9ecef; border-radius: 8px; padding: 25px; margin: 30px 0;'>
                    <h3 style='color: #495057; margin-top: 0; font-size: 18px;'>📋 Suas Credenciais de Acesso:</h3>
                    <div style='margin: 20px 0;'>
                        <p style='color: #555; margin: 10px 0;'><strong>URL:</strong> <a href='" . APP_URL . "' style='color: #007bff;'>" . APP_URL . "</a></p>
                        <p style='color: #555; margin: 10px 0;'><strong>Email:</strong> {$email}</p>
                        <p style='color: #555; margin: 10px 0;'><strong>Senha:</strong> {$password}</p>
                    </div>
                </div>
                
                <!-- Funcionalidades -->
                <div style='background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 25px; margin: 30px 0;'>
                    <h4 style='color: #155724; margin-top: 0; font-size: 16px;'>🚀 O que você pode fazer:</h4>
                    <ul style='color: #155724; margin: 15px 0; padding-left: 20px;'>
                        <li>Preencher perguntas de qualificação do produto</li>
                        <li>Marcar itens do checklist de pontuação</li>
                        <li>Obter nota final automática (0-10)</li>
                        <li>Ver análise interpretativa do resultado</li>
                    </ul>
                </div>
                
                <p style='color: #555; font-size: 16px; margin-bottom: 0;'>
                    <strong>Sucesso e boas vendas! 🎉</strong>
                </p>
            </div>
            
            <!-- Footer -->
            <div style='background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;'>
                <p style='color: #6c757d; font-size: 14px; margin: 0;'>
                    Este é um email automático do ValidaPro. Não responda a esta mensagem.
                </p>
                <p style='color: #6c757d; font-size: 12px; margin: 10px 0 0 0;'>
                    © 2024 ValidaPro - Todos os direitos reservados
                </p>
            </div>
        </div>
    </body>
    </html>";
    
    return sendEmailWithPHPMailer($email, $name, $assunto, $corpo);
}

/**
 * Função de teste para verificar configuração SMTP
 */
function testSMTPConnection() {
    $mail = new PHPMailer(true);
    
    try {
        // Configurações básicas
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Configurações de debug
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Timeout = SMTP_TIMEOUT;
        
        // Configurações de segurança
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Testar conexão
        $mail->smtpConnect();
        $mail->smtpClose();
        
        return true;
        
    } catch (Exception $e) {
        error_log("Teste SMTP falhou: " . $e->getMessage());
        return false;
    }
}
?> 