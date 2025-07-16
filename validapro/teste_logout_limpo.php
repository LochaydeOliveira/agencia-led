<?php
// Teste Limpo do Logout - Sem Output Antecipado
// Este teste não faz echo antes de carregar as configurações

// Carregar configurações ANTES de qualquer output
require_once 'config.php';
require_once 'includes/auth.php';

// Iniciar sessão
initSession();

// Simular login
$_SESSION['user_id'] = 999;
$_SESSION['user_email'] = 'teste@logout.com';
$_SESSION['user_name'] = 'Usuário Teste';
$_SESSION['login_time'] = time();
$_SESSION['last_activity'] = time();

// AGORA sim fazer output
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Limpo do Logout - ValidaPro</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 5px; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
        .card { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Teste Limpo do Sistema de Logout</h1>
        <p>Este teste carrega as configurações ANTES de fazer qualquer output.</p>

        <div class="success">
            <h2>✅ Status do Sistema</h2>
            <p><strong>Usuário logado:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
            <p><strong>Login time:</strong> <?php echo date('d/m/Y H:i:s', $_SESSION['login_time']); ?></p>
        </div>

        <div class="info">
            <h2>📊 Informações Técnicas</h2>
            <p><strong>Session Status:</strong> <?php echo session_status(); ?></p>
            <p><strong>Session Name:</strong> <?php echo session_name(); ?></p>
            <p><strong>Headers Sent:</strong> <?php echo headers_sent() ? 'Sim' : 'Não'; ?></p>
            <p><strong>Debug Mode:</strong> <?php echo defined('DEBUG_MODE') ? (DEBUG_MODE ? 'ON' : 'OFF') : 'Não definido'; ?></p>
            <p><strong>Session Timeout:</strong> <?php echo defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT . ' segundos' : 'Não definido'; ?></p>
        </div>

        <div class="grid">
            <div class="card">
                <h3>🚪 Logout Normal</h3>
                <p>Testa o logout padrão do sistema</p>
                <a href="logout.php" class="btn btn-danger">FAZER LOGOUT</a>
            </div>

            <div class="card">
                <h3>🔵 Logout via JavaScript</h3>
                <p>Testa o logout usando JavaScript</p>
                <button onclick="window.location.href='logout.php'" class="btn btn-primary">LOGOUT JS</button>
            </div>

            <div class="card">
                <h3>✅ Verificar Login</h3>
                <p>Verifica se ainda está logado</p>
                <a href="teste_logout_limpo.php" class="btn btn-success">VERIFICAR</a>
            </div>

            <div class="card">
                <h3>🏠 Sistema Real</h3>
                <p>Acessa o sistema ValidaPro</p>
                <a href="index.php" class="btn btn-primary">ACESSAR SISTEMA</a>
            </div>
        </div>

        <div class="warning">
            <h2>📝 Instruções de Teste</h2>
            <ol>
                <li><strong>Faça logout</strong> usando um dos botões acima</li>
                <li><strong>Verifique</strong> se foi redirecionado para login.php</li>
                <li><strong>Teste novamente</strong> clicando em 'Verificar' para ver se ainda está logado</li>
                <li><strong>Acesse o sistema</strong> para testar o fluxo completo</li>
            </ol>
        </div>

        <div class="info">
            <h2>🔍 Debug da Sessão</h2>
            <pre><?php print_r($_SESSION); ?></pre>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p style="color: #6c757d; font-size: 14px;">
                💡 <strong>Dica:</strong> Este teste não faz output antes de carregar as configurações, evitando problemas de headers.
            </p>
        </div>
    </div>
</body>
</html> 