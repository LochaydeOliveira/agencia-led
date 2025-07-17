<?php
// Debug do Logout - Verifica logs e diagnostica problemas
// Carregar configurações ANTES de qualquer output
require_once 'config.php';
require_once 'includes/auth.php';

// Iniciar sessão
initSession();

// Simular login se não estiver logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 999;
    $_SESSION['user_email'] = 'debug@logout.com';
    $_SESSION['user_name'] = 'Usuário Debug';
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
}

// AGORA sim fazer output
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug do Logout - ValidaPro</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 5px; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        .log-section { margin: 20px 0; }
        .log-title { font-weight: bold; margin-bottom: 10px; color: #495057; }
        .test-section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug do Sistema de Logout</h1>
        <p>Este arquivo verifica os logs e diagnostica problemas no logout.</p>

        <div class="success">
            <h2>✅ Status Atual</h2>
            <p><strong>Usuário:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
            <p><strong>Session Status:</strong> <?php echo session_status(); ?></p>
            <p><strong>Headers Sent:</strong> <?php echo headers_sent() ? 'Sim' : 'Não'; ?></p>
        </div>

        <div class="test-section">
            <h2>🧪 Teste de Logout em Tempo Real</h2>
            <p>Clique nos botões abaixo para testar o logout e ver os logs:</p>
            
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="logout.php" class="btn btn-danger">🚪 TESTAR logout.php</a>
                <a href="logout_simples.php" class="btn btn-danger">⚡ TESTAR logout_simples.php</a>
                <button onclick="testLogoutJS()" class="btn btn-primary">🔵 TESTAR VIA JAVASCRIPT</button>
                <a href="debug_logout.php" class="btn btn-success">🔄 ATUALIZAR LOGS</a>
            </div>
        </div>

        <div class="log-section">
            <div class="log-title">📊 Informações do Sistema</div>
            <pre><?php
echo "=== INFORMAÇÕES DO SISTEMA ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Session Save Path: " . session_save_path() . "\n";
echo "Session Name: " . session_name() . "\n";
echo "Session Status: " . session_status() . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Headers Sent: " . (headers_sent() ? 'Sim' : 'Não') . "\n";
echo "Debug Mode: " . (defined('DEBUG_MODE') ? (DEBUG_MODE ? 'ON' : 'OFF') : 'Não definido') . "\n";
echo "Session Timeout: " . (defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT . ' segundos' : 'Não definido') . "\n";
echo "Current Time: " . date('Y-m-d H:i:s') . "\n";
echo "Server Time: " . gmdate('Y-m-d H:i:s') . " UTC\n";
echo "Timezone: " . date_default_timezone_get() . "\n";
echo "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'N/A') . "\n";
echo "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A') . "\n";
echo "Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "\n";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) ? 'Sim' : 'Não') . "\n";
echo "Server Port: " . ($_SERVER['SERVER_PORT'] ?? 'N/A') . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "\n";
echo "Display Errors: " . ini_get('display_errors') . "\n";
echo "Log Errors: " . ini_get('log_errors') . "\n";
echo "Error Log: " . ini_get('error_log') . "\n";
echo "Session Cookie Params:\n";
$params = session_get_cookie_params();
foreach ($params as $key => $value) {
    echo "  $key: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
}
echo "Session Data:\n";
print_r($_SESSION);
echo "\nCookies:\n";
print_r($_COOKIE);
echo "\nGET Parameters:\n";
print_r($_GET);
echo "\nPOST Parameters:\n";
print_r($_POST);
echo "\nServer Variables:\n";
$server_vars = $_SERVER;
// Remover informações sensíveis
unset($server_vars['HTTP_COOKIE']);
unset($server_vars['HTTP_AUTHORIZATION']);
print_r($server_vars);
            ?></pre>
        </div>

        <div class="log-section">
            <div class="log-title">📝 Logs de Erro (Últimas 50 linhas)</div>
            <pre><?php
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $lines = file($error_log);
    $recent_lines = array_slice($lines, -50);
    echo "=== ÚLTIMAS 50 LINHAS DO LOG DE ERRO ===\n";
    echo "Arquivo: $error_log\n";
    echo "Tamanho: " . filesize($error_log) . " bytes\n";
    echo "Última modificação: " . date('Y-m-d H:i:s', filemtime($error_log)) . "\n\n";
    foreach ($recent_lines as $line) {
        echo htmlspecialchars($line);
    }
} else {
    echo "=== LOG DE ERRO NÃO ENCONTRADO ===\n";
    echo "Error Log Path: " . ($error_log ?: 'Não definido') . "\n";
    echo "Arquivo existe: " . ($error_log && file_exists($error_log) ? 'Sim' : 'Não') . "\n";
    
    // Tentar encontrar logs alternativos
    $possible_logs = [
        '/var/log/apache2/error.log',
        '/var/log/httpd/error_log',
        '/var/log/nginx/error.log',
        '/var/log/php_errors.log',
        error_log(),
        ini_get('error_log')
    ];
    
    echo "\n=== PROCURANDO LOGS ALTERNATIVOS ===\n";
    foreach ($possible_logs as $log) {
        if ($log && file_exists($log)) {
            echo "Log encontrado: $log\n";
            $lines = file($log);
            $recent_lines = array_slice($lines, -20);
            echo "Últimas 20 linhas:\n";
            foreach ($recent_lines as $line) {
                echo htmlspecialchars($line);
            }
            break;
        }
    }
}
            ?></pre>
        </div>

        <div class="log-section">
            <div class="log-title">🔧 Teste de Funções de Logout</div>
            <pre><?php
echo "=== TESTE DE FUNÇÕES DE LOGOUT ===\n";

// Teste 1: Verificar se as funções existem
echo "1. Verificando funções:\n";
echo "   initSession() existe: " . (function_exists('initSession') ? 'Sim' : 'Não') . "\n";
echo "   logout() existe: " . (function_exists('logout') ? 'Sim' : 'Não') . "\n";
echo "   isLoggedIn() existe: " . (function_exists('isLoggedIn') ? 'Sim' : 'Não') . "\n";

// Teste 2: Verificar se está logado
echo "\n2. Status de login:\n";
echo "   isLoggedIn(): " . (isLoggedIn() ? 'Sim' : 'Não') . "\n";
echo "   Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Não definido') . "\n";
echo "   Session user_email: " . (isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'Não definido') . "\n";

// Teste 3: Verificar timeout
echo "\n3. Verificação de timeout:\n";
if (isset($_SESSION['last_activity'])) {
    $timeout = defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 3600;
    $time_diff = time() - $_SESSION['last_activity'];
    echo "   Última atividade: " . date('Y-m-d H:i:s', $_SESSION['last_activity']) . "\n";
    echo "   Tempo decorrido: " . $time_diff . " segundos\n";
    echo "   Timeout configurado: " . $timeout . " segundos\n";
    echo "   Sessão expirada: " . ($time_diff > $timeout ? 'Sim' : 'Não') . "\n";
} else {
    echo "   Última atividade: Não definida\n";
}

// Teste 4: Verificar configurações de sessão
echo "\n4. Configurações de sessão:\n";
echo "   session.cookie_httponly: " . ini_get('session.cookie_httponly') . "\n";
echo "   session.use_only_cookies: " . ini_get('session.use_only_cookies') . "\n";
echo "   session.cookie_secure: " . ini_get('session.cookie_secure') . "\n";
echo "   session.cookie_samesite: " . ini_get('session.cookie_samesite') . "\n";
echo "   session.gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . "\n";

// Teste 5: Verificar se os arquivos existem
echo "\n5. Verificação de arquivos:\n";
$files = ['config.php', 'includes/auth.php', 'includes/db.php', 'logout.php', 'logout_simples.php'];
foreach ($files as $file) {
    echo "   $file: " . (file_exists($file) ? 'Existe' : 'NÃO EXISTE') . "\n";
}
            ?></pre>
        </div>

        <div class="warning">
            <h2>⚠️ Possíveis Problemas Identificados</h2>
            <ul>
                <li><strong>Headers já enviados:</strong> Se aparecer "Headers já enviados: Sim", o logout pode falhar</li>
                <li><strong>Sessão não iniciada:</strong> Se Session Status não for 2, há problemas com a sessão</li>
                <li><strong>Arquivos ausentes:</strong> Se algum arquivo não existir, o sistema pode falhar</li>
                <li><strong>Permissões de log:</strong> Se não conseguir ler os logs, pode haver problemas de permissão</li>
            </ul>
        </div>

        <div class="info">
            <h2>💡 Soluções Recomendadas</h2>
            <ol>
                <li><strong>Use logout_simples.php:</strong> Este arquivo funciona mesmo com problemas de headers</li>
                <li><strong>Verifique os logs:</strong> Os logs mostram exatamente o que está acontecendo</li>
                <li><strong>Teste via JavaScript:</strong> Se o logout normal falhar, use JavaScript</li>
                <li><strong>Limpe o cache:</strong> Às vezes o navegador cacheia páginas antigas</li>
            </ol>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="teste_botao_logout.php" class="btn btn-primary">🧪 VOLTAR AO TESTE DO BOTÃO</a>
            <a href="index.php" class="btn btn-success">🏠 IR PARA O SISTEMA</a>
        </div>
    </div>

    <script>
        function testLogoutJS() {
            console.log('Testando logout via JavaScript...');
            window.location.href = 'logout.php';
        }
        
        // Log de eventos do navegador
        window.addEventListener('beforeunload', function() {
            console.log('Página sendo descarregada...');
        });
        
        window.addEventListener('load', function() {
            console.log('Página carregada completamente');
        });
    </script>
</body>
</html> 