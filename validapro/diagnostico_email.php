<?php
/**
 * Diagnóstico Completo de Email - ValidaPro
 * Script para identificar problemas de autenticação SMTP
 * Sistema completamente independente
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carregar sistema completo do ValidaPro
require_once 'includes/init.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Agora sim pode começar a saída

echo "<h1>🔍 Diagnóstico Completo de Email - ValidaPro</h1>";
echo "<hr>";

echo "<h2>📋 1. Verificação de Configurações</h2>";
echo "<ul>";
echo "<li><strong>APP_URL:</strong> " . (defined('APP_URL') ? APP_URL : 'NÃO DEFINIDA') . "</li>";
echo "<li><strong>APP_NAME:</strong> " . (defined('APP_NAME') ? APP_NAME : 'NÃO DEFINIDA') . "</li>";
echo "<li><strong>SMTP_HOST:</strong> " . SMTP_HOST . "</li>";
echo "<li><strong>SMTP_USER:</strong> " . SMTP_USER . "</li>";
echo "<li><strong>SMTP_PASS:</strong> " . (strlen(SMTP_PASS) > 0 ? 'DEFINIDA (' . strlen(SMTP_PASS) . ' chars)' : 'NÃO DEFINIDA') . "</li>";
echo "<li><strong>SMTP_PORT:</strong> " . SMTP_PORT . "</li>";
echo "<li><strong>SMTP_SECURE:</strong> " . SMTP_SECURE . "</li>";
echo "<li><strong>FROM_EMAIL:</strong> " . FROM_EMAIL . "</li>";
echo "<li><strong>FROM_NAME:</strong> " . FROM_NAME . "</li>";
echo "</ul>";

echo "<h2>🔧 2. Teste de Conexão SMTP Manual</h2>";

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
    
    echo "<p><strong>Iniciando conexão SMTP...</strong></p>";
    
    // Capturar output do debug
    ob_start();
    $mail->smtpConnect();
    $debug_output = ob_get_clean();
    
    echo "<p style='color: green;'>✅ Conexão SMTP estabelecida!</p>";
    echo "<details>";
    echo "<summary>Ver detalhes da conexão</summary>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>" . htmlspecialchars($debug_output) . "</pre>";
    echo "</details>";
    
    $mail->smtpClose();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na conexão SMTP: " . $e->getMessage() . "</p>";
    echo "<details>";
    echo "<summary>Ver detalhes do erro</summary>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>" . htmlspecialchars($mail->ErrorInfo) . "</pre>";
    echo "</details>";
}

echo "<h2>📧 3. Teste de Envio Completo</h2>";

// Email de teste
$email_teste = 'teste@agencialed.com'; // Altere para um email válido
$nome_teste = 'Usuário Teste';

echo "<p><strong>Enviando para:</strong> $email_teste</p>";

try {
    $enviado = sendEmailWithPHPMailer(
        $email_teste, 
        $nome_teste, 
        'Diagnóstico ValidaPro - ' . date('d/m/Y H:i:s'), 
        '<h2>Teste de Diagnóstico</h2><p>Este é um teste do sistema ValidaPro.</p><p><strong>Data/Hora:</strong> ' . date('d/m/Y H:i:s') . '</p>'
    );
    
    if ($enviado) {
        echo "<p style='color: green;'>✅ Email enviado com sucesso!</p>";
        echo "<p>Verifique a caixa de entrada de <strong>$email_teste</strong></p>";
    } else {
        echo "<p style='color: red;'>❌ Falha no envio do email</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>🔍 4. Verificação de Logs</h2>";

// Verificar logs de erro
$log_file = ini_get('error_log');
if ($log_file && file_exists($log_file)) {
    $logs = file_get_contents($log_file);
    $linhas = explode("\n", $logs);
    $ultimas_linhas = array_slice($linhas, -30); // Últimas 30 linhas
    
    echo "<details>";
    echo "<summary>Ver últimas 30 linhas do log de erro</summary>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars(implode("\n", $ultimas_linhas)) . "</pre>";
    echo "</details>";
} else {
    echo "<p>Arquivo de log não encontrado ou não configurado.</p>";
}

echo "<h2>💡 5. Possíveis Soluções</h2>";

echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px; border-left: 4px solid #007bff;'>";
echo "<h3>Se o problema persistir:</h3>";
echo "<ol>";
echo "<li><strong>Verificar credenciais:</strong> Confirme se a senha do Zoho está correta</li>";
echo "<li><strong>Senha de app:</strong> Use uma senha de aplicativo do Zoho em vez da senha principal</li>";
echo "<li><strong>Configurações SMTP:</strong> Verifique se as configurações do Zoho estão corretas</li>";
echo "<li><strong>Firewall/Antivírus:</strong> Verifique se não está bloqueando conexões SMTP</li>";
echo "<li><strong>Servidor:</strong> Confirme se o servidor permite conexões SMTP externas</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><a href='recuperar_senha.php'>← Testar Recuperação de Senha</a></p>";
echo "<p><a href='login.php'>← Voltar para Login</a></p>";
?> 