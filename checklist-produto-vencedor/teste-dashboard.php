<?php
// Ativar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Teste do Dashboard - Checklist do Produto</h2>";

try {
    // 1. Testar inclusão dos arquivos
    echo "<p>1. Testando inclusão dos arquivos...</p>";
    
    if (file_exists('includes/db.php')) {
        echo "<p style='color: green;'>✅ Arquivo includes/db.php existe</p>";
    } else {
        echo "<p style='color: red;'>❌ Arquivo includes/db.php não existe</p>";
        exit;
    }
    
    if (file_exists('includes/auth.php')) {
        echo "<p style='color: green;'>✅ Arquivo includes/auth.php existe</p>";
    } else {
        echo "<p style='color: red;'>❌ Arquivo includes/auth.php não existe</p>";
        exit;
    }
    
    // 2. Testar conexão com banco
    echo "<p>2. Testando conexão com banco...</p>";
    require_once 'includes/db.php';
    echo "<p style='color: green;'>✅ Conexão com banco estabelecida</p>";
    
    // 3. Testar funções de autenticação
    echo "<p>3. Testando funções de autenticação...</p>";
    require_once 'includes/auth.php';
    echo "<p style='color: green;'>✅ Funções de autenticação carregadas</p>";
    
    // 4. Testar sessão
    echo "<p>4. Testando sessão...</p>";
    session_start();
    echo "<p>ID da sessão: " . session_id() . "</p>";
    echo "<p>Dados da sessão: " . print_r($_SESSION, true) . "</p>";
    
    // 5. Testar função getCurrentUser
    echo "<p>5. Testando getCurrentUser...</p>";
    $user = getCurrentUser();
    echo "<p>Usuário atual: " . print_r($user, true) . "</p>";
    
    // 6. Simular dashboard
    echo "<p>6. Simulando dashboard...</p>";
    if ($user) {
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h3 style='color: #155724; margin-top: 0;'>✅ Dashboard funcionando!</h3>";
        echo "<p><strong>Usuário logado:</strong> " . htmlspecialchars($user['name']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>❌ Usuário não está logado</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
    echo "<p>Arquivo: " . $e->getFile() . "</p>";
    echo "<p>Linha: " . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>← Ir para o dashboard real</a></p>";
echo "<p><a href='index.php'>← Voltar para o login</a></p>";
?> 