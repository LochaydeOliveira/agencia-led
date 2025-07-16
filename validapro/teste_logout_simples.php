<?php
// Teste Simples do Logout - Versão Melhorada
echo "<h1>🧪 TESTE SIMPLES DO LOGOUT - VERSÃO MELHORADA</h1>";

// 0. Carregar configurações primeiro
echo "<h2>0. Carregando Configurações</h2>";
try {
    require_once 'config.php';
    echo "✅ config.php carregado<br>";
    echo "Session timeout: " . (defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 'Não definido') . "<br>";
    echo "Debug mode: " . (defined('DEBUG_MODE') ? (DEBUG_MODE ? 'ON' : 'OFF') : 'Não definido') . "<br>";
} catch (Exception $e) {
    echo "❌ Erro ao carregar config.php: " . $e->getMessage() . "<br>";
}

// 1. Verificar se conseguimos incluir o auth.php
echo "<h2>1. Teste de Inclusão</h2>";
try {
    require_once 'includes/auth.php';
    echo "✅ auth.php incluído com sucesso<br>";
} catch (Exception $e) {
    echo "❌ Erro ao incluir auth.php: " . $e->getMessage() . "<br>";
    exit();
}

// 2. Testar função initSession
echo "<h2>2. Teste da Função initSession</h2>";
try {
    initSession();
    echo "✅ initSession executada<br>";
    echo "Session ID: " . session_id() . "<br>";
    echo "Session Name: " . session_name() . "<br>";
} catch (Exception $e) {
    echo "❌ Erro em initSession: " . $e->getMessage() . "<br>";
}

// 3. Simular dados de login
echo "<h2>3. Simulando Login</h2>";
$_SESSION['user_id'] = 999;
$_SESSION['user_email'] = 'teste@logout.com';
$_SESSION['user_name'] = 'Usuário Teste';
$_SESSION['login_time'] = time();
$_SESSION['last_activity'] = time();
echo "✅ Dados de sessão simulados<br>";

// 4. Verificar se isLoggedIn funciona
echo "<h2>4. Teste isLoggedIn</h2>";
if (isLoggedIn()) {
    echo "✅ isLoggedIn retorna true<br>";
} else {
    echo "❌ isLoggedIn retorna false<br>";
}

// 5. Testar logout
echo "<h2>5. Teste do Logout</h2>";
echo "<p>Clique no botão abaixo para testar o logout:</p>";
echo "<a href='logout.php' style='background: red; color: white; padding: 15px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px; display: inline-block;'>🚪 TESTAR LOGOUT</a>";

// 6. Teste alternativo com JavaScript
echo "<h2>6. Teste Alternativo</h2>";
echo "<button onclick=\"window.location.href='logout.php'\" style='background: blue; color: white; padding: 15px; border: none; border-radius: 5px; font-weight: bold; margin: 10px; cursor: pointer;'>🔵 LOGOUT VIA JAVASCRIPT</button>";

echo "<hr>";
echo "<h3>Informações de Debug:</h3>";
echo "Session Status: " . session_status() . "<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Name: " . session_name() . "<br>";
echo "Headers Sent: " . (headers_sent() ? 'Sim' : 'Não') . "<br>";
echo "Session Data: <pre>" . print_r($_SESSION, true) . "</pre>";

// 7. Teste de redirecionamento
echo "<h2>7. Teste de Redirecionamento</h2>";
echo "<p>Se o logout não funcionar, teste estes links:</p>";
echo "<a href='login.php' style='background: green; color: white; padding: 10px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>🟢 IR PARA LOGIN</a>";
echo "<a href='index.php' style='background: orange; color: white; padding: 10px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>🟠 IR PARA INDEX</a>";
?> 