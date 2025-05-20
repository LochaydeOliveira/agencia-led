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

        

        // Configurações do servidor

        $this->mailer->isSMTP();

        $this->mailer->Host = SMTP_HOST;

        $this->mailer->SMTPAuth = true;

        $this->mailer->Username = SMTP_USER;

        $this->mailer->Password = SMTP_PASS;

        $this->mailer->SMTPSecure = SMTP_SECURE;

        $this->mailer->Port = SMTP_PORT;

        $this->mailer->CharSet = 'UTF-8';

        

        // Configurações padrão

        $this->mailer->setFrom(SMTP_USER, 'Agência LED');

        $this->mailer->isHTML(true);

        

        // Ativa o debug SMTP

        $this->mailer->SMTPDebug = 2;

        $this->mailer->Debugoutput = 'error_log';

        

        app_log("Configurações SMTP: Host=" . SMTP_HOST . ", Port=" . SMTP_PORT . ", User=" . SMTP_USER);

    }



    public function sendDownloadLink($customerEmail, $customerName, $orderNumber, $token) {

        try {

            app_log("Iniciando envio de email para $customerEmail");

            

            // Limpa destinatários anteriores

            $this->mailer->clearAddresses();

            

            // Adiciona o destinatário

            $this->mailer->addAddress($customerEmail, $customerName);

            $this->mailer->Subject = '✅ PAGAMENTO CONFIRMADO - Sua Lista Está Pronta!';

            

            $html = "

            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #ffffff;'>

                <!-- Cabeçalho -->

                <div style='text-align: center; padding: 20px 0; border-bottom: 2px solid #f0f0f0;'>

                    <h1 style='color: #28a745; margin: 0; font-size: 24px;'>BAIXE AGORA SUA LISTA SECRETA DE FORNECEDORES</h1>

                </div>

                

                <!-- Conteúdo Principal -->

                <div style='padding: 30px 20px;'>

                    <h2 style='color: #2c3e50; margin-top: 0;'>Olá {$customerName},</h2>

                    

                    <p style='color: #34495e; font-size: 16px; line-height: 1.6;'>

                        Seu pagamento foi confirmado com sucesso! 

                        <strong>Clique no botão abaixo para acessar sua lista de fornecedores agora mesmo.</strong>

                    </p>

                    

                    <!-- Botão de Download -->

                    <div style='text-align: center; margin: 30px 0;'>

                        <a href='https://agencialed.com/download_page.php?token={$token}' 

                           style='background: #28a745; 

                                  color: white; 

                                  padding: 15px 30px; 

                                  text-decoration: none; 

                                  border-radius: 8px; 

                                  font-size: 18px; 

                                  font-weight: bold; 

                                  display: inline-block;

                                  box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>

                            Acessar Lista de Fornecedores

                        </a>

                    </div>

                    

                    <!-- Aviso de Expiração -->

                    <div style='background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;'>

                        <p style='color: #856404; margin: 0; font-size: 14px;'>

                            <strong>⏰ ATENÇÃO:</strong> Este link expirará em 24 horas. 

                            Recomendamos que você faça o download o quanto antes para garantir seu acesso.

                        </p>

                    </div>

                    

                    <!-- Informações Adicionais -->

                    <div style='background-color: #e8f5e9; padding: 20px; border-radius: 8px; margin: 20px 0;'>

                        <h3 style='color: #2c3e50; margin-top: 0;'>📋 O que você vai encontrar:</h3>

                        <ul style='color: #34495e; padding-left: 20px; margin: 0;'>

                            <li style='margin-bottom: 10px;'>Lista completa de fornecedores nacionais</li>

                            <li style='margin-bottom: 10px;'>Contatos e informações de cada fornecedor</li>

                            <li style='margin-bottom: 10px;'>Dicas de negociação e melhores práticas</li>

                        </ul>

                    </div>

                </div>

                

                <!-- Rodapé -->

                <div style='text-align: center; padding: 20px; border-top: 2px solid #f0f0f0; background-color: #f8f9fa;'>

                    <p style='color: #7f8c8d; margin: 0; font-size: 14px;'>

                        Se precisar de ajuda, entre em contato conosco:<br>

                        <a href='mailto:contato@agencialed.com' style='color: #3498db; text-decoration: none;'>contato@agencialed.com</a>

                    </p>

                </div>

            </div>";

            

            $this->mailer->Body = $html;

            $this->mailer->AltBody = "Olá {$customerName},\n\n" .

                "✅ PAGAMENTO CONFIRMADO - Sua Lista Está Pronta!\n\n" .

                "Seu pagamento foi confirmado com sucesso! Clique no link abaixo para acessar sua lista de fornecedores:\n\n" .

                "https://agencialed.com/download_page.php?token={$token}\n\n" .

                "⏰ ATENÇÃO: Este link expirará em 24 horas. Recomendamos que você faça o download o quanto antes.\n\n" .

                "📋 O que você vai encontrar:\n" .

                "- Lista completa de fornecedores nacionais\n" .

                "- Contatos e informações de cada fornecedor\n" .

                "- Dicas de negociação e melhores práticas\n\n" .

                "Se precisar de ajuda, entre em contato conosco: contato@agencialed.com";

            

            app_log("Tentando enviar email para $customerEmail");

            $result = $this->mailer->send();

            app_log("Email enviado com sucesso para $customerEmail");

            

            return $result;

        } catch (Exception $e) {

            app_log("Erro ao enviar email para $customerEmail: " . $e->getMessage());

            app_log("Detalhes do erro: " . print_r($e, true));

            return false;

        }

    }



    public function sendOrderConfirmation($to, $name, $orderNumber, $value) {

        try {

            app_log("Iniciando envio de email de confirmação para $to");

            app_log("Detalhes do email: Nome=$name, Pedido=$orderNumber, Valor=$value");

            

            // Limpa destinatários anteriores

            $this->mailer->clearAddresses();

            

            // Adiciona o destinatário

            $this->mailer->addAddress($to, $name);

            $this->mailer->isHTML(true);

            $this->mailer->Subject = '🚨 PAGAMENTO PENDENTE - Pedido #' . $orderNumber;

            

            $html = "

            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #ffffff;'>

                <!-- Cabeçalho -->

                <div style='text-align: center; padding: 20px 0; border-bottom: 2px solid #f0f0f0;'>

                    <h1 style='color: #e74c3c; margin: 0; font-size: 24px;'>PAGUE COM O PIX AGORA!</h1>

                </div>

                

                <!-- Conteúdo Principal -->

                <div style='padding: 30px 20px;'>

                    <h2 style='color: #2c3e50; margin-top: 0;'>Olá {$name},</h2>

                    

                    <p style='color: #34495e; font-size: 16px; line-height: 1.6;'>

                        Recebemos seu pedido #{$orderNumber} com sucesso! 

                        <strong>Para garantir seu acesso à lista de fornecedores, realize o pagamento agora mesmo.</strong>

                    </p>

                    

                    <!-- Box de Valor -->

                    <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; border: 2px dashed #e74c3c;'>

                        <p style='color: #e74c3c; font-size: 18px; margin: 0; font-weight: bold;'>

                            Valor a Pagar: R$ " . number_format($value, 2, ',', '.') . "

                        </p>

                    </div>

                    

                    <!-- Aviso de Urgência -->

                    <div style='background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;'>

                        <p style='color: #856404; margin: 0; font-size: 14px;'>

                            <strong>⏰ ATENÇÃO:</strong> O pagamento via PIX expira em poucos minutos. 

                            Após a confirmação, você receberá imediatamente o link para download da sua lista de fornecedores.

                        </p>

                    </div>

                    

                    <!-- Próximos Passos -->

                    <div style='background-color: #e8f5e9; padding: 20px; border-radius: 8px; margin: 20px 0;'>

                        <h3 style='color: #2c3e50; margin-top: 0;'>✅ Próximos Passos</h3>

                        <ol style='color: #34495e; padding-left: 20px; margin: 0;'>

                            <li style='margin-bottom: 10px;'>Realize o pagamento via PIX agora mesmo</li>

                            <li style='margin-bottom: 10px;'>Aguarde a confirmação automática do pagamento</li>

                            <li style='margin-bottom: 10px;'>Receba o email com o link para download da sua lista</li>

                        </ol>

                    </div>

                </div>

                

                <!-- Rodapé -->

                <div style='text-align: center; padding: 20px; border-top: 2px solid #f0f0f0; background-color: #f8f9fa;'>

                    <p style='color: #7f8c8d; margin: 0; font-size: 14px;'>

                        Se precisar de ajuda, entre em contato conosco:<br>

                        <a href='mailto:contato@agencialed.com' style='color: #3498db; text-decoration: none;'>contato@agencialed.com</a>

                    </p>

                </div>

            </div>";

            

            $this->mailer->Body = $html;

            $this->mailer->AltBody = "Olá {$name},\n\n" .

                "🚨 PAGAMENTO PENDENTE - Pedido #{$orderNumber}\n\n" .

                "Recebemos seu pedido com sucesso! Para garantir seu acesso à lista de fornecedores, realize o pagamento agora mesmo.\n\n" .

                "Valor a Pagar: R$ " . number_format($value, 2, ',', '.') . "\n\n" .

                "⏰ ATENÇÃO: O pagamento via PIX expira em poucos minutos. Após a confirmação, você receberá imediatamente o link para download da sua lista de fornecedores.\n\n" .

                "✅ Próximos Passos:\n" .

                "1. Realize o pagamento via PIX agora mesmo\n" .

                "2. Aguarde a confirmação automática do pagamento\n" .

                "3. Receba o email com o link para download da sua lista\n\n" .

                "Se precisar de ajuda, entre em contato conosco: contato@agencialed.com";

            

            app_log("Tentando enviar email de confirmação para $to");

            app_log("Configurações finais do PHPMailer: " . print_r([

                'Host' => $this->mailer->Host,

                'Port' => $this->mailer->Port,

                'SMTPAuth' => $this->mailer->SMTPAuth,

                'SMTPSecure' => $this->mailer->SMTPSecure,

                'Username' => $this->mailer->Username,

                'From' => $this->mailer->From,

                'FromName' => $this->mailer->FromName

            ], true));

            

            $result = $this->mailer->send();

            app_log("Email de confirmação enviado com sucesso para $to");

            

            return $result;

        } catch (Exception $e) {

            app_log("Erro ao enviar email de confirmação para $to: " . $e->getMessage());

            app_log("Detalhes do erro: " . print_r($e, true));

            return false;

        }

    }

} 