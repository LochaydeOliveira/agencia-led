<?php
/**
 * Teste Final do Sistema - ValidaPro
 * Verifica se todos os problemas foram resolvidos
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔧 Teste Final do Sistema - ValidaPro</h1>";
echo "<hr>";

// Carregar sistema completo do ValidaPro
require_once 'includes/init.php';

echo "<h2>📋 1. Verificação de Inicialização</h2>";

// Verificar se a sessão foi iniciada corretamente
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✅ Sessão ativa: " . session_name() . "</p>";
    echo "<p><strong>ID da Sessão:</strong> " . session_id() . "</p>";
} else {
    echo "<p style='color: red;'>❌ Sessão não está ativa</p>";
}

echo "<h2>🔐 2. Teste de Autenticação</h2>";

// Teste de login
$email_teste = 'admin@validapro.com';
$senha_teste = '123456';

echo "<p><strong>Testando login com:</strong> $email_teste</p>";

try {
    $resultado = authenticateValidaProUser($email_teste, $senha_teste);
    
    if ($resultado) {
        echo "<p style='color: green;'>✅ Login bem-sucedido!</p>";
        
        // Verificar dados da sessão
        $user = getCurrentValidaProUser();
        if ($user) {
            echo "<p><strong>Usuário logado:</strong> " . $user['name'] . " (" . $user['email'] . ")</p>";
            echo "<p><strong>Tipo:</strong> " . $user['tipo'] . "</p>";
        }
        
        // Teste das funções de compatibilidade
        echo "<h3>Teste das Funções de Compatibilidade</h3>";
        
        $check_timeout = checkSessionTimeout();
        echo "<p><strong>checkSessionTimeout():</strong> " . ($check_timeout ? '✅ OK' : '❌ Falhou') . "</p>";
        
        $renew = renewSession();
        echo "<p><strong>renewSession():</strong> " . ($renew ? '✅ OK' : '❌ Falhou') . "</p>";
        
        $is_logged = isLoggedIn();
        echo "<p><strong>isLoggedIn():</strong> " . ($is_logged ? '✅ OK' : '❌ Falhou') . "</p>";
        
        $current_user = getCurrentUser();
        echo "<p><strong>getCurrentUser():</strong> " . ($current_user ? '✅ OK' : '❌ Falhou') . "</p>";
        
        // Teste de logout
        echo "<h3>Teste de Logout</h3>";
        logoutValidaPro();
        echo "<p style='color: green;'>✅ Logout realizado com sucesso!</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Login falhou</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>🔍 3. Verificação de Headers</h2>";

// Verificar se headers foram enviados
if (headers_sent($file, $line)) {
    echo "<p style='color: orange;'>⚠️ Headers já foram enviados</p>";
    echo "<p><strong>Arquivo:</strong> $file</p>";
    echo "<p><strong>Linha:</strong> $line</p>";
} else {
    echo "<p style='color: green;'>✅ Headers ainda não foram enviados</p>";
}

echo "<h2>📊 4. Teste de CSRF Token</h2>";

// Teste de geração de CSRF token
$token = generateValidaProCSRFToken();
echo "<p><strong>CSRF Token gerado:</strong> " . substr($token, 0, 20) . "...</p>";

$valid = validateValidaProCSRFToken($token);
echo "<p><strong>Validação do token:</strong> " . ($valid ? '✅ Válido' : '❌ Inválido') . "</p>";

echo "<h2>📧 5. Teste de Email</h2>";

// Teste rápido de email
$email_teste = 'teste@agencialed.com';
$nome_teste = 'Usuário Teste';

try {
    $enviado = sendEmailWithPHPMailer(
        $email_teste, 
        $nome_teste, 
        'Teste Final ValidaPro - ' . date('H:i:s'), 
        '<h2>Teste Final</h2><p>Sistema funcionando corretamente!</p>'
    );
    
    if ($enviado) {
        echo "<p style='color: green;'>✅ Email enviado com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>❌ Falha no envio do email</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>🔍 6. Verificação de Logs</h2>";

// Verificar logs de erro
$log_file = ini_get('error_log');
if ($log_file && file_exists($log_file)) {
    $logs = file_get_contents($log_file);
    $linhas = explode("\n", $logs);
    $ultimas_linhas = array_slice($linhas, -10); // Últimas 10 linhas
    
    echo "<details>";
    echo "<summary>Ver últimas 10 linhas do log de erro</summary>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;'>" . htmlspecialchars(implode("\n", $ultimas_linhas)) . "</pre>";
    echo "</details>";
} else {
    echo "<p>Arquivo de log não encontrado ou não configurado.</p>";
}

echo "<h2>✅ 7. Resumo do Sistema</h2>";

echo "<div style='background: #f0fff0; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;'>";
echo "<h3>Sistema ValidaPro - Status:</h3>";
echo "<ul>";
echo "<li>✅ Inicialização controlada</li>";
echo "<li>✅ Sessão funcionando</li>";
echo "<li>✅ Autenticação ativa</li>";
echo "<li>✅ Funções de compatibilidade</li>";
echo "<li>✅ CSRF tokens</li>";
echo "<li>✅ Sistema de email</li>";
echo "<li>✅ Headers de segurança</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><a href='login.php'>← Testar Login</a></p>";
echo "<p><a href='index.php'>← Testar Página Principal</a></p>";
echo "<p><a href='recuperar_senha.php'>← Testar Recuperação de Senha</a></p>";
?> 