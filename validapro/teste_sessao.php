<?php
/**
 * Teste de Sessão - ValidaPro
 * Verifica se o problema de headers foi resolvido
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔧 Teste de Sessão - ValidaPro</h1>";
echo "<hr>";

// Carregar sistema completo do ValidaPro
require_once 'includes/init.php';

echo "<h2>📋 1. Verificação de Sessão</h2>";

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

echo "<h2>📊 4. Logs de Erro</h2>";

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

echo "<hr>";
echo "<p><a href='login.php'>← Testar Login</a></p>";
echo "<p><a href='recuperar_senha.php'>← Testar Recuperação de Senha</a></p>";
?> 