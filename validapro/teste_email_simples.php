<?php
/**
 * Teste Simples de Email - ValidaPro
 * Sistema completamente independente
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>📧 Teste Simples de Email - ValidaPro</h1>";
echo "<hr>";

// Carregar configurações independentes do ValidaPro
require_once 'includes/email_config.php';
require_once 'includes/mailer.php';

echo "<h2>📋 Verificando Configurações:</h2>";
echo "<ul>";
echo "<li><strong>APP_URL:</strong> " . (defined('APP_URL') ? APP_URL : 'NÃO DEFINIDA') . "</li>";
echo "<li><strong>APP_NAME:</strong> " . (defined('APP_NAME') ? APP_NAME : 'NÃO DEFINIDA') . "</li>";
echo "<li><strong>SMTP_HOST:</strong> " . SMTP_HOST . "</li>";
echo "<li><strong>SMTP_USER:</strong> " . SMTP_USER . "</li>";
echo "<li><strong>FROM_EMAIL:</strong> " . FROM_EMAIL . "</li>";
echo "<li><strong>FROM_NAME:</strong> " . FROM_NAME . "</li>";
echo "</ul>";

echo "<h2>📧 Testando Envio:</h2>";

// Email de teste (altere para um email válido)
$email_teste = 'teste@agencialed.com';
$nome_teste = 'Usuário Teste';

echo "<p><strong>Enviando para:</strong> $email_teste</p>";

try {
    // Teste 1: Email simples
    $enviado1 = sendEmailWithPHPMailer(
        $email_teste, 
        $nome_teste, 
        'Teste ValidaPro - ' . date('H:i:s'), 
        '<h2>Teste de Email</h2><p>Este é um teste do sistema ValidaPro.</p>'
    );
    
    if ($enviado1) {
        echo "<p style='color: green;'>✅ Email simples enviado com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>❌ Falha no email simples</p>";
    }
    
    // Teste 2: Email de recuperação
    $token_teste = 'teste_' . bin2hex(random_bytes(8));
    $enviado2 = sendPasswordRecoveryEmail($email_teste, $nome_teste, $token_teste);
    
    if ($enviado2) {
        echo "<p style='color: green;'>✅ Email de recuperação enviado com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>❌ Falha no email de recuperação</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='recuperar_senha.php'>← Testar Recuperação de Senha</a></p>";
echo "<p><a href='login.php'>← Voltar para Login</a></p>";
?> 