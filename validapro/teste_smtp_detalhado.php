<?php
require_once 'includes/email_config.php';
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "<h2>Teste de Configuração SMTP - ValidaPro</h2>";

// Mostrar configurações (sem mostrar a senha completa)
echo "<h3>Configurações atuais:</h3>";
echo "Host: " . SMTP_HOST . "<br>";
echo "Porta: " . SMTP_PORT . "<br>";
echo "Segurança: " . SMTP_SECURE . "<br>";
echo "Usuário: " . SMTP_USER . "<br>";
echo "Senha: " . substr(SMTP_PASS, 0, 3) . "***<br>";
echo "From Email: " . FROM_EMAIL . "<br>";
echo "From Name: " . FROM_NAME . "<br><br>";

try {
    $mail = new PHPMailer(true);
    
    // Configurar debug
    $mail->SMTPDebug = 2; // Mostrar detalhes do debug
    $mail->Debugoutput = 'html';
    
    // Configurações SMTP
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;
    
    // Configurações do e-mail
    $mail->setFrom(FROM_EMAIL, FROM_NAME);
    $mail->addAddress('lochaydeguerreiro@hotmail.com', 'Teste');
    $mail->Subject = 'Teste SMTP ValidaPro';
    $mail->isHTML(true);
    $mail->Body = '<h1>Teste de envio</h1><p>Se você receber este e-mail, o SMTP está funcionando!</p>';
    
    echo "<h3>Iniciando teste de envio...</h3>";
    
    if ($mail->send()) {
        echo "<p style='color: green;'><strong>SUCESSO!</strong> E-mail enviado com sucesso!</p>";
    } else {
        echo "<p style='color: red;'><strong>FALHA!</strong> Não foi possível enviar o e-mail.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>ERRO:</strong> " . $e->getMessage() . "</p>";
    echo "<h3>Detalhes do erro:</h3>";
    echo "<pre>" . $mail->ErrorInfo . "</pre>";
}

echo "<br><h3>Possíveis soluções:</h3>";
echo "<ul>";
echo "<li>Verificar se o e-mail e senha estão corretos no Zoho</li>";
echo "<li>Verificar se a autenticação de 2 fatores está desativada ou usar senha de app</li>";
echo "<li>Verificar se o acesso SMTP está habilitado no painel do Zoho</li>";
echo "<li>Verificar se não há bloqueio de IP no servidor</li>";
echo "</ul>";
?> 