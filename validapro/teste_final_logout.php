<?php
// Teste Final do Sistema de Logout - Simulação Real
// Este teste simula o uso real do sistema

echo "<h1>🎯 TESTE FINAL DO SISTEMA DE LOGOUT</h1>";
echo "<p>Este teste simula o uso real do sistema ValidaPro</p>";

// Carregar configurações
require_once 'config.php';
require_once 'includes/auth.php';

// Iniciar sessão
initSession();

// Simular login real
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'usuario@teste.com';
$_SESSION['user_name'] = 'Usuário de Teste';
$_SESSION['login_time'] = time();
$_SESSION['last_activity'] = time();

echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ Status do Sistema</h2>";
echo "<p><strong>Usuário logado:</strong> " . htmlspecialchars($_SESSION['user_name']) . "</p>";
echo "<p><strong>Email:</strong> " . htmlspecialchars($_SESSION['user_email']) . "</p>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Login time:</strong> " . date('d/m/Y H:i:s', $_SESSION['login_time']) . "</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🧪 Testes Disponíveis</h2>";
echo "<p>Escolha uma das opções abaixo para testar o logout:</p>";

echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 20px 0;'>";

// Teste 1: Logout normal
echo "<div style='background: white; padding: 20px; border-radius: 8px; border: 2px solid #dc3545;'>";
echo "<h3 style='color: #dc3545; margin-top: 0;'>🚪 Logout Normal</h3>";
echo "<p>Testa o logout padrão do sistema</p>";
echo "<a href='logout.php' style='background: #dc3545; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>FAZER LOGOUT</a>";
echo "</div>";

// Teste 2: Logout com JavaScript
echo "<div style='background: white; padding: 20px; border-radius: 8px; border: 2px solid #007bff;'>";
echo "<h3 style='color: #007bff; margin-top: 0;'>🔵 Logout via JavaScript</h3>";
echo "<p>Testa o logout usando JavaScript</p>";
echo "<button onclick=\"window.location.href='logout.php'\" style='background: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;'>LOGOUT JS</button>";
echo "</div>";

// Teste 3: Verificar se ainda está logado
echo "<div style='background: white; padding: 20px; border-radius: 8px; border: 2px solid #28a745;'>";
echo "<h3 style='color: #28a745; margin-top: 0;'>✅ Verificar Login</h3>";
echo "<p>Verifica se ainda está logado</p>";
echo "<a href='teste_final_logout.php' style='background: #28a745; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>VERIFICAR</a>";
echo "</div>";

// Teste 4: Ir para o sistema real
echo "<div style='background: white; padding: 20px; border-radius: 8px; border: 2px solid #ffc107;'>";
echo "<h3 style='color: #ffc107; margin-top: 0;'>🏠 Sistema Real</h3>";
echo "<p>Acessa o sistema ValidaPro</p>";
echo "<a href='index.php' style='background: #ffc107; color: black; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>ACESSAR SISTEMA</a>";
echo "</div>";

echo "</div>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>📊 Informações Técnicas</h2>";
echo "<p><strong>Session Status:</strong> " . session_status() . "</p>";
echo "<p><strong>Session Name:</strong> " . session_name() . "</p>";
echo "<p><strong>Headers Sent:</strong> " . (headers_sent() ? 'Sim' : 'Não') . "</p>";
echo "<p><strong>Debug Mode:</strong> " . (defined('DEBUG_MODE') ? (DEBUG_MODE ? 'ON' : 'OFF') : 'Não definido') . "</p>";
echo "<p><strong>Session Timeout:</strong> " . (defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT . ' segundos' : 'Não definido') . "</p>";
echo "</div>";

echo "<div style='background: #e2e3e5; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>📝 Instruções de Teste</h2>";
echo "<ol>";
echo "<li><strong>Faça logout</strong> usando um dos botões acima</li>";
echo "<li><strong>Verifique</strong> se foi redirecionado para login.php</li>";
echo "<li><strong>Teste novamente</strong> clicando em 'Verificar' para ver se ainda está logado</li>";
echo "<li><strong>Acesse o sistema</strong> para testar o fluxo completo</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 Debug da Sessão</h2>";
echo "<pre style='background: white; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
print_r($_SESSION);
echo "</pre>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<p style='color: #6c757d; font-size: 14px;'>";
echo "💡 <strong>Dica:</strong> Se o logout não funcionar, verifique os logs do servidor para mais detalhes.";
echo "</p>";
echo "</div>";
?> 