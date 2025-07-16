<?php
// Teste do sistema de logout
echo "<h1>🔍 TESTE DO SISTEMA DE LOGOUT</h1>";

// 1. Verificar se a sessão está funcionando
echo "<h2>1. Status da Sessão</h2>";
if (session_status() === PHP_SESSION_NONE) {
    echo "❌ Sessão não iniciada<br>";
    session_start();
    echo "✅ Sessão iniciada agora<br>";
} else {
    echo "✅ Sessão já estava iniciada<br>";
}

// 2. Verificar se há dados na sessão
echo "<h2>2. Dados da Sessão</h2>";
if (empty($_SESSION)) {
    echo "❌ Sessão vazia<br>";
} else {
    echo "✅ Sessão tem dados:<br>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
}

// 3. Simular login
echo "<h2>3. Simulando Login</h2>";
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'teste@exemplo.com';
$_SESSION['user_name'] = 'Usuário Teste';
echo "✅ Dados de login simulados<br>";

// 4. Verificar se o logout funciona
echo "<h2>4. Testando Logout</h2>";
echo "<a href='logout.php' style='background: red; color: white; padding: 10px; text-decoration: none;'>TESTAR LOGOUT</a><br><br>";

// 5. Verificar configurações
echo "<h2>5. Configurações do Sistema</h2>";
echo "Session timeout: " . (defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 'Não definido') . "<br>";
echo "Debug mode: " . (defined('DEBUG_MODE') ? (DEBUG_MODE ? 'ON' : 'OFF') : 'Não definido') . "<br>";
echo "Show errors: " . (defined('SHOW_ERRORS') ? (SHOW_ERRORS ? 'ON' : 'OFF') : 'Não definido') . "<br>";

// 6. Verificar se o banco está funcionando
echo "<h2>6. Teste do Banco de Dados</h2>";
try {
    require_once 'includes/db.php';
    if ($pdo) {
        echo "✅ Conexão com banco OK<br>";
        
        // Testar se a tabela users existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->fetch()) {
            echo "✅ Tabela users existe<br>";
            
            // Contar usuários
            $stmt = $pdo->query("SELECT COUNT(*) FROM users");
            $count = $stmt->fetchColumn();
            echo "✅ Número de usuários: $count<br>";
        } else {
            echo "❌ Tabela users não existe<br>";
        }
    } else {
        echo "❌ Falha na conexão com banco<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro no banco: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>Para testar o logout, clique no botão vermelho acima.</strong></p>";
?> 