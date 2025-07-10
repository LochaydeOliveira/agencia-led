<?php
session_start();
require_once 'config/database.php';

// Se já estiver logado, redirecionar para home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Validações
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $error = 'Por favor, preencha todos os campos.';
    } elseif (strlen($nome) < 3) {
        $error = 'O nome deve ter pelo menos 3 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, insira um email válido.';
    } elseif (strlen($senha) < 6) {
        $error = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmar_senha) {
        $error = 'As senhas não coincidem.';
    } else {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Este email já está cadastrado.';
        } else {
            // Criar usuário
            $senha_hash = hashPassword($senha);
            
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$nome, $email, $senha_hash])) {
                $user_id = $pdo->lastInsertId();
                
                // Criar configurações padrão
                $stmt = $pdo->prepare("INSERT INTO configuracoes_usuario (usuario_id) VALUES (?)");
                $stmt->execute([$user_id]);
                
                $success = 'Conta criada com sucesso! Você pode fazer login agora.';
            } else {
                $error = 'Erro ao criar conta. Tente novamente.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - My Training</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo e Título -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4">
                <i class="fas fa-dumbbell text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">My Training</h1>
            <p class="text-white text-opacity-90">Crie sua conta</p>
        </div>

        <!-- Card de Cadastro -->
        <div class="bg-white rounded-lg card-shadow p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Criar Conta</h2>
            
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Nome Completo
                    </label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        required
                        minlength="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        placeholder="Seu nome completo"
                        value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        placeholder="seu@email.com"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    >
                </div>

                <div>
                    <label for="senha" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Senha
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="senha" 
                            name="senha" 
                            required
                            minlength="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors pr-12"
                            placeholder="Mínimo 6 caracteres"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('senha', 'senhaIcon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <i class="fas fa-eye" id="senhaIcon"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="confirmar_senha" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Confirmar Senha
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="confirmar_senha" 
                            name="confirmar_senha" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors pr-12"
                            placeholder="Confirme sua senha"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('confirmar_senha', 'confirmarSenhaIcon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <i class="fas fa-eye" id="confirmarSenhaIcon"></i>
                        </button>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                >
                    <i class="fas fa-user-plus mr-2"></i>
                    Criar Conta
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Já tem uma conta? 
                    <a href="login.php" class="text-blue-600 hover:text-blue-700 font-medium">
                        Fazer login
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-white text-opacity-70 text-sm">
                &copy; 2024 My Training. Todos os direitos reservados.
            </p>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Validação em tempo real
        document.getElementById('confirmar_senha').addEventListener('input', function() {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = this.value;
            
            if (senha !== confirmarSenha && confirmarSenha.length > 0) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });

        // Focar no primeiro campo ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('nome').focus();
        });
    </script>
</body>
</html> 