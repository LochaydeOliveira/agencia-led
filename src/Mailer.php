<?php
require_once __DIR__ . '/../vendor/PHPMailer/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer/SMTP.php';
require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mailer;

    public function __construct() {
        app_log("Iniciando configuração do PHPMailer");

        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = SMTP_USER;
        $this->mailer->Password = SMTP_PASS;
        $this->mailer->SMTPSecure = SMTP_SECURE;
        $this->mailer->Port = SMTP_PORT;
        $this->mailer->CharSet = 'UTF-8';

        $this->mailer->setFrom(SMTP_USER, 'Agência LED');
        $this->mailer->isHTML(true);
        $this->mailer->SMTPDebug = 2;
        $this->mailer->Debugoutput = 'error_log';
    }

    public function sendDownloadLink($email, $name, $orderNumber, $token) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $name);
            $this->mailer->Subject = '✅ PAGAMENTO CONFIRMADO - Sua Lista Está Pronta!';

            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px;'>
                <h2 style='color: #28a745;'>Olá {$name},</h2>
                <p>Seu pagamento foi confirmado com sucesso!</p>
                <p><strong>Clique no botão abaixo para acessar sua lista de fornecedores agora mesmo.</strong></p>
                <div style='text-align: center; margin: 30px;'>
                    <a href='https://agencialed.com/download_page.php?token={$token}'
                       style='background-color: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px;'>
                       Acessar Lista de Fornecedores
                    </a>
                </div>
                <div style='background-color: #fff3cd; padding: 15px; border-radius: 8px;'>
                    <p><strong>⏰ ATENÇÃO:</strong> Este link expirará em 24 horas. Recomendamos que você faça o download o quanto antes.</p>
                </div>
                <p>📋 O que você vai encontrar:</p>
                <ul>
                    <li>Lista completa de fornecedores nacionais</li>
                    <li>Contatos e informações de cada fornecedor</li>
                    <li>Dicas de negociação e melhores práticas</li>
                </ul>
                <hr>
                <p style='font-size: 14px; color: #888;'>Em caso de dúvidas, entre em contato: <a href='mailto:contato@agencialed.com'>contato@agencialed.com</a></p>
            </div>";

            $this->mailer->Body = $html;
            $this->mailer->AltBody = "Olá {$name},\n\nAcesse sua lista: https://agencialed.com/download_page.php?token={$token}\nEste link expira em 24 horas.";

            return $this->mailer->send();
        } catch (Exception $e) {
            app_log("Erro ao enviar email de download para $email: " . $e->getMessage());
            return false;
        }
    }

    public function sendOrderConfirmation($to, $name, $orderNumber, $value) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to, $name);
            $this->mailer->Subject = '🚨 PAGAMENTO PENDENTE - Pedido #' . $orderNumber;

            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px;'>
                <h2 style='color: #e74c3c;'>Olá {$name},</h2>
                <p>Recebemos seu pedido #{$orderNumber} com sucesso!</p>
                <p><strong>Para garantir seu acesso à lista de fornecedores, realize o pagamento agora mesmo.</strong></p>
                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;'>
                    <strong>Valor a Pagar: R$ " . number_format($value, 2, ',', '.') . "</strong>
                </div>
                <div style='background-color: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 20px;'>
                    <p><strong>⏰ ATENÇÃO:</strong> O pagamento via PIX expira em poucos minutos.</p>
                </div>
                <ol>
                    <li>Realize o pagamento via PIX agora mesmo</li>
                    <li>Aguarde a confirmação automática do pagamento</li>
                    <li>Receba o email com o link para download</li>
                </ol>
                <hr>
                <p style='font-size: 14px; color: #888;'>Dúvidas? <a href='mailto:contato@agencialed.com'>contato@agencialed.com</a></p>
            </div>";

            $this->mailer->Body = $html;
            $this->mailer->AltBody = "Olá {$name}, seu pedido foi registrado. Valor: R$ " . number_format($value, 2, ',', '.') . ". Pague via PIX para garantir o acesso.";

            return $this->mailer->send();
        } catch (Exception $e) {
            app_log("Erro ao enviar confirmação para $to: " . $e->getMessage());
            return false;
        }
    }

    public function sendMemberAccess($email, $name, $senha) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $name);
            $this->mailer->Subject = '🔐 Acesso Liberado - Área dos Clientes | Agência LED';

            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px;'>
                <h2 style='color: #0d6efd;'>Olá {$name},</h2>
                <p>Seu pagamento foi confirmado e seu acesso à área de clientes foi liberado!</p>
                <p><strong>Use os dados abaixo para acessar:</strong></p>
                <div style='background-color: #f1f1f1; padding: 15px; border-radius: 8px;'>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Senha:</strong> {$senha}</p>
                </div>
                <div style='text-align: center; margin: 30px;'>
                    <a href='https://agencialed.com/login.php'
                       style='background-color: #0d6efd; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px;'>
                       Acessar Área de Clientes
                    </a>
                </div>
                <hr>
                <p style='font-size: 14px; color: #888;'>Dúvidas? <a href='mailto:contato@agencialed.com'>contato@agencialed.com</a></p>
            </div>";

            $this->mailer->Body = $html;
            $this->mailer->AltBody = "Olá {$name},\n\nSeu acesso à área dos clientes foi liberado.\nEmail: {$email}\nSenha: {$senha}\nAcesse: https://agencialed.com/login.php";

            return $this->mailer->send();
        } catch (Exception $e) {
            app_log("Erro ao enviar dados de acesso para $email: " . $e->getMessage());
            return false;
        }
    }
}
