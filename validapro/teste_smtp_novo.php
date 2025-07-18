<?php
/**
 * Teste SMTP do ValidaPro
 * Script para verificar se a configuração de email está funcionando
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔧 Teste SMTP - ValidaPro</h1>";
echo "<hr>";

// Carregar configurações
require_once 'includes/email_config.php';
require_once 'includes/mailer.php';

echo "<h2>📋 Configurações Carregadas:</h2>";
echo "<ul>";
echo "<li><strong>Host:</strong> " . SMTP_HOST . "</li>";
echo "<li><strong>Usuário:</strong> " . SMTP_USER . "</li>";
echo "<li><strong>Porta:</strong> " . SMTP_PORT . "</li>";
echo "<li><strong>Segurança:</strong> " . SMTP_SECURE . "</li>";
echo "<li><strong>Timeout:</strong> " . SMTP_TIMEOUT . "s</li>";
echo "</ul>";

echo "<h2>🔍 Teste 1: Conexão SMTP</h2>";
try {
    $resultado = testSMTPConnection();
    if ($resultado) {
        echo "<p style='color: green;'>✅ Conexão SMTP estabelecida com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>❌ Falha na conexão SMTP</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>📧 Teste 2: Envio de Email</h2>";

// Email de teste
$email_teste = 'teste@agencialed.com'; // Altere para um email válido
$nome_teste = 'Usuário Teste';
$assunto_teste = 'Teste SMTP - ValidaPro';
$corpo_teste = "
<html>
<head>
    <title>Teste SMTP</title>
</head>
<body>
    <h2>Teste de Email - ValidaPro</h2>
    <p>Este é um email de teste para verificar se a configuração SMTP está funcionando.</p>
    <p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>
    <p><strong>Servidor:</strong> " . $_SERVER['SERVER_NAME'] . "</p>
</body>
</html>";

echo "<p><strong>Enviando para:</strong> $email_teste</p>";

try {
    $enviado = sendEmailWithPHPMailer($email_teste, $nome_teste, $assunto_teste, $corpo_teste);
    
    if ($enviado) {
        echo "<p style='color: green;'>✅ Email enviado com sucesso!</p>";
        echo "<p>Verifique a caixa de entrada de <strong>$email_teste</strong></p>";
    } else {
        echo "<p style='color: red;'>❌ Falha no envio do email</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>🔍 Teste 3: Função de Recuperação</h2>";

// Token de teste
$token_teste = bin2hex(random_bytes(16));

try {
    $enviado = sendPasswordRecoveryEmail($email_teste, $nome_teste, $token_teste);
    
    if ($enviado) {
        echo "<p style='color: green;'>✅ Email de recuperação enviado com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>❌ Falha no envio do email de recuperação</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>📊 Logs de Erro:</h2>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";

// Verificar se existe arquivo de log
$log_file = ini_get('error_log');
if ($log_file && file_exists($log_file)) {
    $logs = file_get_contents($log_file);
    $linhas = explode("\n", $logs);
    $ultimas_linhas = array_slice($linhas, -20); // Últimas 20 linhas
    echo implode("\n", $ultimas_linhas);
} else {
    echo "Arquivo de log não encontrado ou não configurado.";
}

echo "</pre>";

echo "<hr>";
echo "<p><a href='recuperar_senha.php'>← Voltar para Recuperação de Senha</a></p>";
echo "<p><a href='login.php'>← Voltar para Login</a></p>";
?> 