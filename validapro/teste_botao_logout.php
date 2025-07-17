<?php
// Teste Específico do Botão de Logout
// Simula exatamente o que acontece quando o usuário clica em "Sair"

// Carregar configurações ANTES de qualquer output
require_once 'config.php';
require_once 'includes/auth.php';

// Iniciar sessão
initSession();

// Simular login
$_SESSION['user_id'] = 999;
$_SESSION['user_email'] = 'teste@botao.com';
$_SESSION['user_name'] = 'Usuário Teste Botão';
$_SESSION['login_time'] = time();
$_SESSION['last_activity'] = time();

// AGORA sim fazer output
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste do Botão de Logout - ValidaPro</title>
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
        .header { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .logout-btn { color: #dc3545; text-decoration: none; font-weight: bold; }
        .logout-btn:hover { color: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Teste Específico do Botão de Logout</h1>
        <p>Este teste simula exatamente o header do sistema ValidaPro com o botão de logout.</p>

        <!-- Header Simulado (igual ao do index.php) -->
        <div class="header">
            <div class="header-content">
                <div>
                    <h2>ValidaPro</h2>
                </div>
                <div class="user-info">
                    <span style="color: #6c757d; font-size: 14px;">
                        <i class="fas fa-user" style="margin-right: 5px;"></i>
                        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </span>
                    <a href="logout.php" class="logout-btn" style="font-size: 14px;">
                        <i class="fas fa-sign-out-alt" style="margin-right: 5px;"></i>Sair
                    </a>
                </div>
            </div>
        </div>

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
                <h3>🚪 Teste do Botão Original</h3>
                <p>Testa o botão exatamente como está no sistema</p>
                <a href="logout.php" class="btn btn-danger">SAIR (logout.php)</a>
            </div>

            <div class="card">
                <h3>⚡ Teste do Logout Simples</h3>
                <p>Testa o logout ultra simples que funciona</p>
                <a href="logout_simples.php" class="btn btn-danger">SAIR (logout_simples.php)</a>
            </div>

            <div class="card">
                <h3>🔵 Teste via JavaScript</h3>
                <p>Testa o logout usando JavaScript</p>
                <button onclick="window.location.href='logout.php'" class="btn btn-primary">SAIR VIA JS</button>
            </div>

            <div class="card">
                <h3>✅ Verificar Login</h3>
                <p>Verifica se ainda está logado</p>
                <a href="teste_botao_logout.php" class="btn btn-success">VERIFICAR</a>
            </div>

            <div class="card">
                <h3>🏠 Sistema Real</h3>
                <p>Acessa o sistema ValidaPro</p>
                <a href="index.php" class="btn btn-primary">ACESSAR SISTEMA</a>
            </div>

            <div class="card">
                <h3>🔍 Debug do Logout</h3>
                <p>Verifica os logs do logout</p>
                <a href="debug_logout.php" class="btn btn-primary">VER LOGS</a>
            </div>
        </div>

        <div class="warning">
            <h2>📝 Instruções de Teste</h2>
            <ol>
                <li><strong>Clique em "SAIR (logout.php)"</strong> - Este é o botão original que não está funcionando</li>
                <li><strong>Observe o comportamento</strong> - Veja se redireciona ou volta para index.php</li>
                <li><strong>Teste "SAIR (logout_simples.php)"</strong> - Este deve funcionar</li>
                <li><strong>Verifique os logs</strong> - Use "VER LOGS" para ver o que está acontecendo</li>
            </ol>
        </div>

        <div class="info">
            <h2>🔍 Debug da Sessão</h2>
            <pre><?php print_r($_SESSION); ?></pre>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p style="color: #6c757d; font-size: 14px;">
                💡 <strong>Dica:</strong> Se o botão original não funcionar, use o logout_simples.php que é 100% confiável.
            </p>
        </div>
    </div>
</body>
</html> 