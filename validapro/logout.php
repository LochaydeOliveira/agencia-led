<?php
// Logout ValidaPro - Versão 3.0 (Ultra Robusta)
// Sistema que funciona em qualquer situação

// 1. Carregar configurações
require_once 'config.php';

// 2. Incluir sistema de autenticação
require_once 'includes/auth.php';

// 3. Log do início do logout
error_log("=== INÍCIO DO LOGOUT ===");
error_log("Timestamp: " . date('Y-m-d H:i:s'));
error_log("User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'N/A'));
error_log("IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A'));

// 4. Verificar se há dados de sessão para log
if (isset($_SESSION['user_email'])) {
    error_log("Logout do usuário: " . $_SESSION['user_email']);
} else {
    error_log("Logout sem dados de usuário na sessão");
}

// 5. Executar logout
try {
    logout();
    error_log("Logout executado com sucesso");
} catch (Exception $e) {
    error_log("Erro no logout: " . $e->getMessage());
    
    // Fallback manual
    try {
        // Limpar sessão manualmente
        $_SESSION = [];
        
        // Destruir cookie de sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destruir sessão
        session_destroy();
        
        error_log("Fallback manual executado");
    } catch (Exception $e2) {
        error_log("Erro no fallback: " . $e2->getMessage());
    }
}

// 6. Verificar se os headers já foram enviados
if (headers_sent($file, $line)) {
    error_log("Headers já enviados em $file:$line");
    
    // Usar JavaScript para redirecionamento
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Logout - ValidaPro</title>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
            .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
            .success { color: #28a745; font-size: 24px; margin-bottom: 20px; }
            .loading { color: #6c757d; margin-bottom: 20px; }
            .manual-link { margin-top: 20px; }
            .manual-link a { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="success">✅ Logout realizado com sucesso!</div>
            <div class="loading">Redirecionando para a página de login...</div>
            <div class="manual-link">
                <a href="login.php">Clique aqui se não for redirecionado automaticamente</a>
            </div>
        </div>
        <script>
            // Aguardar um pouco e redirecionar
            setTimeout(function() {
                window.location.href = "login.php";
            }, 2000);
            
            // Fallback após 5 segundos
            setTimeout(function() {
                if (window.location.pathname.indexOf("logout.php") !== -1) {
                    window.location.href = "login.php";
                }
            }, 5000);
        </script>
    </body>
    </html>';
    
    error_log("Redirecionamento via JavaScript executado");
    exit();
} else {
    // Headers não foram enviados, usar redirecionamento HTTP
    error_log("Redirecionamento via HTTP header");
    header('Location: login.php');
    exit();
}

// 7. Se chegou até aqui, algo deu muito errado
error_log("ERRO CRÍTICO: Logout não conseguiu redirecionar");
echo '<script>window.location.href = "login.php";</script>';
exit();
?> 