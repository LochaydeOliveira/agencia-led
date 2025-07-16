<?php
session_name('VALIDAPRO_TESTE');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/auth.php';
requireLogin();
require_once '../includes/db.php';

// Configurações de email (ajuste conforme seu servidor)
$smtp_host = 'smtp.gmail.com'; // ou seu servidor SMTP
$smtp_port = 587;
$smtp_username = 'seu-email@gmail.com'; // Substitua pelo seu email
$smtp_password = 'sua-senha-app'; // Substitua pela sua senha de app
$from_email = 'seu-email@gmail.com';
$from_name = 'Checklist do Produto';

// Função para enviar email
function sendAccessEmail($email, $password, $name) {
    global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $from_email, $from_name;
    
    $subject = "Acesso ao Checklist do Produto Lucrativo";
    $message = "
    <html>
    <head>
        <title>Acesso ao Checklist</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='margin: 0; font-size: 28px;'>🎯 Checklist do Produto Lucrativo</h1>
                <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>Seu acesso está pronto!</p>
            </div>
            
            <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;'>
                <h2 style='color: #2c3e50; margin-top: 0;'>Olá, {$name}!</h2>
                
                <p>Seu acesso ao <strong>Checklist do Produto Lucrativo</strong> foi criado com sucesso!</p>
                
                <div style='background: white; border: 2px solid #e9ecef; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                    <h3 style='color: #495057; margin-top: 0;'>📋 Suas Credenciais de Acesso:</h3>
                    <p><strong>URL:</strong> <a href='https://seudominio.com/app/' style='color: #007bff;'>https://seudominio.com/app/</a></p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Senha:</strong> {$password}</p>
                </div>
                
                <div style='background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 15px; margin: 20px 0;'>
                    <h4 style='color: #155724; margin-top: 0;'>🚀 O que você pode fazer:</h4>
                    <ul style='color: #155724; margin: 10px 0;'>
                        <li>Preencher perguntas de qualificação do produto</li>
                        <li>Marcar itens do checklist de pontuação</li>
                        <li>Obter nota final automática (0-10)</li>
                        <li>Ver análise interpretativa do resultado</li>
                    </ul>
                </div>
                
                <p style='margin-bottom: 0;'><strong>Sucesso e boas vendas! 🎉</strong></p>
            </div>
            
            <div style='text-align: center; margin-top: 20px; color: #6c757d; font-size: 12px;'>
                <p>Este é um email automático. Não responda a esta mensagem.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: {$from_name} <{$from_email}>\r\n";
    $headers .= "Reply-To: {$from_email}\r\n";
    
    // Tentar enviar usando mail() do PHP (mais simples)
    if (mail($email, $subject, $message, $headers)) {
        return true;
    }
    
    // Se mail() falhar, você pode implementar PHPMailer aqui
    return false;
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $send_email = isset($_POST['send_email']);
    
    if ($name && $email && $password) {
        try {
            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = "Este email já está cadastrado!";
            } else {
                // Criar usuário
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
                $stmt->execute([$email, $hashed_password, $name]);
                
                $success = "Usuário criado com sucesso!";
                
                // Enviar email se solicitado
                if ($send_email) {
                    if (sendAccessEmail($email, $password, $name)) {
                        $success .= " Email de acesso enviado!";
                    } else {
                        $success .= " Usuário criado, mas houve erro ao enviar email.";
                    }
                }
            }
        } catch (PDOException $e) {
            $error = "Erro ao criar usuário: " . $e->getMessage();
        }
    } else {
        $error = "Todos os campos são obrigatórios!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Usuário - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Adicionar Novo Usuário</h1>
                <p class="text-gray-600">Crie acesso para novos usuários do checklist</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Nome Completo
                    </label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nome do usuário">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="usuario@exemplo.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Senha
                    </label>
                    <input type="text" id="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Senha temporária">
                    <p class="text-sm text-gray-500 mt-1">A senha será criptografada automaticamente</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="send_email" name="send_email" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                    <label for="send_email" class="ml-2 text-sm text-gray-700">
                        Enviar email com credenciais de acesso
                    </label>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition duration-200">
                    <i class="fas fa-user-plus mr-2"></i>Criar Usuário
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <a href="../index.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <i class="fas fa-arrow-left mr-1"></i>Voltar ao Login
                </a>
            </div>
        </div>
    </div>
</body>
</html> 