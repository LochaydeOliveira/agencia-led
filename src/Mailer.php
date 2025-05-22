<?php
// Carrega os arquivos principais do PHPMailer
require_once __DIR__ . '/../vendor/PHPMailer/Exception.php'; // Classe de exceções
require_once __DIR__ . '/../vendor/PHPMailer/SMTP.php';      // Classe para envio via SMTP
require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer.php'; // Classe principal

// Usa os namespaces corretos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Classe que encapsula o envio de e-mails da aplicação
class Mailer {
    private $mailer; // Propriedade interna que armazenará o objeto PHPMailer

    // Construtor chamado automaticamente ao criar um Mailer
    public function __construct() {
        app_log("Iniciando configuração do PHPMailer"); // Grava log

        $this->mailer = new PHPMailer(true); // Instancia o PHPMailer com tratamento de erros

        // Configurações básicas de envio via SMTP
        $this->mailer->isSMTP();                      // Define o uso de SMTP
        $this->mailer->Host = SMTP_HOST;              // Servidor SMTP (ex: smtp.zoho.com)
        $this->mailer->SMTPAuth = true;               // Habilita autenticação
        $this->mailer->Username = SMTP_USER;          // Usuário (e-mail remetente)
        $this->mailer->Password = SMTP_PASS;          // Senha do e-mail
        $this->mailer->SMTPSecure = SMTP_SECURE;      // Tipo de segurança (TLS ou SSL)
        $this->mailer->Port = SMTP_PORT;              // Porta (587 ou 465)
        $this->mailer->CharSet = 'UTF-8';             // Codificação dos caracteres

        // Informações padrão do remetente
        $this->mailer->setFrom(SMTP_USER, 'Agência LED');
        $this->mailer->isHTML(true); // Os e-mails serão enviados em HTML

        // Ativa o modo debug (importante para testes)
        $this->mailer->SMTPDebug = 2;
        $this->mailer->Debugoutput = 'error_log'; // Registra o debug no log do PHP
    }

    // Envia o link de download após pagamento confirmado
    public function sendDownloadLink($email, $name, $orderNumber, $token) {
        try {
            $this->mailer->clearAddresses(); // Remove destinatários anteriores
            $this->mailer->addAddress($email, $name); // Define o novo destinatário
            $this->mailer->Subject = '✅ PAGAMENTO CONFIRMADO - Sua Lista Está Pronta!'; // Título do e-mail

            // Corpo em HTML
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

            $this->mailer->Body = $html; // Define corpo HTML
            $this->mailer->AltBody = "Olá {$name},\n\nAcesse sua lista: https://agencialed.com/download_page.php?token={$token}\nEste link expira em 24 horas."; // Texto alternativo (caso HTML não carregue)

            return $this->mailer->send(); // Tenta enviar
        } catch (Exception $e) {
            app_log("Erro ao enviar email de download para $email: " . $e->getMessage());
            return false; // Em caso de erro
        }
    }

    // Envia um aviso de pedido aguardando pagamento
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
                    <li>Receba o email com os dados de acesso a área de clientes</li>
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

    // Envia os dados de login para a área de membros após pagamento confirmado
    public function sendMemberAccess($email, $name, $senha) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $name);
            $this->mailer->Subject = '🔐 ACESSO LIBERADO! - Área dos Clientes | Agência LED';

            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px;'>
                <h2 style='color: #28a745;font-size: 30px;'>Olá {$name},</h2>
                <p style='font-size: 17px;margin: 0 0 -18px 0;'>Seu pagamento foi confirmado e seu acesso à área de clientes foi liberado!</p>
                <p style='font-size: 15px;'><strong>Use os dados abaixo para acessar:</strong></p>
                <div style='background-color: #e6f9eae0;padding:15px;border-radius:8px;font-size: 15px;text-decoration: none!important;border-left: 6px solid #61d17b;'>
                    <p style='text-decoration: none !important;'><strong>Email:</strong> {$email}</p>
                    <p><strong>Senha:</strong> {$senha}</p>
                </div>
                <div style='text-align: center; margin: 30px;'>
                    <a href='https://agencialed.com/login.php'
                       style='max-width: 60%;margin: 0 auto;background: #28a745;color: white;padding: 15px 30px;text-decoration: none;border-radius: 8px;font-size: 18px;font-weight: bold;display: inline-block;width: 100%;'>
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
