<?php
// Logout Ultra Simples - Funciona em qualquer situação
// Versão 4.0 - Máxima compatibilidade

// Log do início
error_log("=== LOGOUT ULTRA SIMPLES INICIADO ===");

// 1. Tentar limpar sessão se possível
try {
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Limpar dados da sessão
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
        error_log("Sessão destruída com sucesso");
    }
} catch (Exception $e) {
    error_log("Erro ao limpar sessão: " . $e->getMessage());
}

// 2. Limpar cookies alternativos
try {
    if (isset($_COOKIE['validapro_session'])) {
        setcookie('validapro_session', '', time() - 42000, '/');
        unset($_COOKIE['validapro_session']);
        error_log("Cookie alternativo removido");
    }
} catch (Exception $e) {
    error_log("Erro ao remover cookie alternativo: " . $e->getMessage());
}

// 3. Redirecionar
error_log("Tentando redirecionamento...");

if (!headers_sent()) {
    error_log("Redirecionamento via HTTP header");
    header('Location: login.php');
    exit();
} else {
    error_log("Headers já enviados - usando JavaScript");
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Logout - ValidaPro</title>
        <meta charset="UTF-8">
        <style>
            body { 
                font-family: Arial, sans-serif; 
                text-align: center; 
                padding: 50px; 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container { 
                background: white; 
                padding: 40px; 
                border-radius: 15px; 
                box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
                max-width: 400px; 
                width: 100%;
            }
            .success { 
                color: #28a745; 
                font-size: 24px; 
                margin-bottom: 20px; 
                font-weight: bold;
            }
            .loading { 
                color: #6c757d; 
                margin-bottom: 30px; 
                font-size: 16px;
            }
            .manual-link { 
                margin-top: 30px; 
            }
            .manual-link a { 
                background: #007bff; 
                color: white; 
                padding: 12px 24px; 
                text-decoration: none; 
                border-radius: 25px; 
                font-weight: bold;
                transition: all 0.3s ease;
            }
            .manual-link a:hover {
                background: #0056b3;
                transform: translateY(-2px);
            }
            .spinner {
                border: 3px solid #f3f3f3;
                border-top: 3px solid #007bff;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="spinner"></div>
            <div class="success">✅ Logout realizado!</div>
            <div class="loading">Redirecionando para a página de login...</div>
            <div class="manual-link">
                <a href="login.php">Clique aqui se não for redirecionado</a>
            </div>
        </div>
        <script>
            // Aguardar um pouco e redirecionar
            setTimeout(function() {
                window.location.href = "login.php";
            }, 2000);
            
            // Fallback após 5 segundos
            setTimeout(function() {
                if (window.location.pathname.indexOf("logout") !== -1) {
                    window.location.href = "login.php";
                }
            }, 5000);
            
            // Fallback adicional após 10 segundos
            setTimeout(function() {
                if (window.location.pathname.indexOf("logout") !== -1) {
                    window.location.replace("login.php");
                }
            }, 10000);
        </script>
    </body>
    </html>';
    exit();
}

// 4. Se chegou até aqui, algo deu muito errado
error_log("ERRO CRÍTICO: Logout não conseguiu redirecionar");
echo '<script>window.location.href = "login.php";</script>';
exit();
?> 