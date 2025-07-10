<?php
session_start();
require_once 'config/database.php';

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$usuario = $stmt->fetch();

// Buscar configurações
$stmt = $pdo->prepare("SELECT * FROM configuracoes_usuario WHERE usuario_id = ?");
$stmt->execute([$user_id]);
$config = $stmt->fetch();

// Processar formulário de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        if ($_POST['acao'] === 'atualizar_perfil') {
            $nome = sanitize($_POST['nome']);
            $altura = !empty($_POST['altura']) ? (float)$_POST['altura'] : null;
            $peso = !empty($_POST['peso']) ? (float)$_POST['peso'] : null;
            $objetivo = sanitize($_POST['objetivo']);
            
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, altura = ?, peso = ?, objetivo = ?, data_atualizacao = NOW() WHERE id = ?");
            $stmt->execute([$nome, $altura, $peso, $objetivo, $user_id]);
            
            $_SESSION['user_name'] = $nome;
            
            header('Location: perfil.php?success=1');
            exit();
        } elseif ($_POST['acao'] === 'alterar_senha') {
            $senha_atual = $_POST['senha_atual'];
            $nova_senha = $_POST['nova_senha'];
            $confirmar_senha = $_POST['confirmar_senha'];
            
            if (!verifyPassword($senha_atual, $usuario['senha_hash'])) {
                $error = 'Senha atual incorreta.';
            } elseif (strlen($nova_senha) < 6) {
                $error = 'A nova senha deve ter pelo menos 6 caracteres.';
            } elseif ($nova_senha !== $confirmar_senha) {
                $error = 'As senhas não coincidem.';
            } else {
                $nova_senha_hash = hashPassword($nova_senha);
                $stmt = $pdo->prepare("UPDATE usuarios SET senha_hash = ?, data_atualizacao = NOW() WHERE id = ?");
                $stmt->execute([$nova_senha_hash, $user_id]);
                
                header('Location: perfil.php?password_success=1');
                exit();
            }
        } elseif ($_POST['acao'] === 'atualizar_config') {
            $tema_escuro = isset($_POST['tema_escuro']) ? 1 : 0;
            $notificacoes_treino = isset($_POST['notificacoes_treino']) ? 1 : 0;
            $notificacoes_alimentacao = isset($_POST['notificacoes_alimentacao']) ? 1 : 0;
            $horario_treino = !empty($_POST['horario_treino']) ? $_POST['horario_treino'] : null;
            $horario_alimentacao = !empty($_POST['horario_alimentacao']) ? $_POST['horario_alimentacao'] : null;
            
            if ($config) {
                $stmt = $pdo->prepare("UPDATE configuracoes_usuario SET tema_escuro = ?, notificacoes_treino = ?, notificacoes_alimentacao = ?, horario_treino = ?, horario_alimentacao = ?, data_atualizacao = NOW() WHERE usuario_id = ?");
                $stmt->execute([$tema_escuro, $notificacoes_treino, $notificacoes_alimentacao, $horario_treino, $horario_alimentacao, $user_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO configuracoes_usuario (usuario_id, tema_escuro, notificacoes_treino, notificacoes_alimentacao, horario_treino, horario_alimentacao) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $tema_escuro, $notificacoes_treino, $notificacoes_alimentacao, $horario_treino, $horario_alimentacao]);
            }
            
            header('Location: perfil.php?config_success=1');
            exit();
        }
    }
}

// Buscar dados atualizados
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$usuario = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM configuracoes_usuario WHERE usuario_id = ?");
$stmt->execute([$user_id]);
$config = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - My Training</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="index.php" class="mr-4">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Meu Perfil</h1>
                        <p class="text-sm opacity-90">Gerencie suas informações</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mensagens -->
    <?php if (isset($_GET['success'])): ?>
        <div class="container mx-auto px-4 py-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                Perfil atualizado com sucesso!
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['password_success'])): ?>
        <div class="container mx-auto px-4 py-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                Senha alterada com sucesso!
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['config_success'])): ?>
        <div class="container mx-auto px-4 py-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                Configurações atualizadas com sucesso!
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="container mx-auto px-4 py-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Conteúdo -->
    <div class="container mx-auto px-4 py-6">
        <!-- Informações Pessoais -->
        <div class="bg-white rounded-lg card-shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-user text-blue-500 mr-2"></i>
                Informações Pessoais
            </h3>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="acao" value="atualizar_perfil">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                    <input type="text" name="nome" required value="<?php echo htmlspecialchars($usuario['nome']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Altura (m)</label>
                        <input type="number" name="altura" min="0" step="0.01" value="<?php echo $usuario['altura']; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: 1.75">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Peso (kg)</label>
                        <input type="number" name="peso" min="0" step="0.1" value="<?php echo $usuario['peso']; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: 75.5">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Objetivo</label>
                    <textarea name="objetivo" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Descreva seu objetivo de treino..."><?php echo htmlspecialchars($usuario['objetivo']); ?></textarea>
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Salvar Alterações
                </button>
            </form>
        </div>

        <!-- Alterar Senha -->
        <div class="bg-white rounded-lg card-shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-lock text-red-500 mr-2"></i>
                Alterar Senha
            </h3>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="acao" value="alterar_senha">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Senha Atual</label>
                    <input type="password" name="senha_atual" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                    <input type="password" name="nova_senha" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nova Senha</label>
                    <input type="password" name="confirmar_senha" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-key mr-2"></i>Alterar Senha
                </button>
            </form>
        </div>

        <!-- Configurações -->
        <div class="bg-white rounded-lg card-shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-cog text-gray-500 mr-2"></i>
                Configurações
            </h3>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="acao" value="atualizar_config">
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" id="tema_escuro" name="tema_escuro" <?php echo ($config && $config['tema_escuro']) ? 'checked' : ''; ?> class="mr-3">
                        <label for="tema_escuro" class="text-sm font-medium text-gray-700">Tema Escuro</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="notificacoes_treino" name="notificacoes_treino" <?php echo ($config && $config['notificacoes_treino']) ? 'checked' : ''; ?> class="mr-3">
                        <label for="notificacoes_treino" class="text-sm font-medium text-gray-700">Notificações de Treino</label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="notificacoes_alimentacao" name="notificacoes_alimentacao" <?php echo ($config && $config['notificacoes_alimentacao']) ? 'checked' : ''; ?> class="mr-3">
                        <label for="notificacoes_alimentacao" class="text-sm font-medium text-gray-700">Notificações de Alimentação</label>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Horário Preferido para Treino</label>
                        <input type="time" name="horario_treino" value="<?php echo $config ? $config['horario_treino'] : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Horário Preferido para Alimentação</label>
                        <input type="time" name="horario_alimentacao" value="<?php echo $config ? $config['horario_alimentacao'] : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Salvar Configurações
                </button>
            </form>
        </div>

        <!-- Informações da Conta -->
        <div class="bg-white rounded-lg card-shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-green-500 mr-2"></i>
                Informações da Conta
            </h3>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-sm font-medium text-gray-700">Email:</span>
                    <span class="text-sm text-gray-600"><?php echo htmlspecialchars($usuario['email']); ?></span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-sm font-medium text-gray-700">Membro desde:</span>
                    <span class="text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($usuario['data_criacao'])); ?></span>
                </div>
                
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm font-medium text-gray-700">Última atualização:</span>
                    <span class="text-sm text-gray-600"><?php echo date('d/m/Y H:i', strtotime($usuario['data_atualizacao'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Ações da Conta -->
        <div class="bg-white rounded-lg card-shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                Ações da Conta
            </h3>
            
            <div class="space-y-3">
                <button onclick="exportarDados()" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Exportar Meus Dados
                </button>
                
                <button onclick="excluirConta()" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-trash mr-2"></i>Excluir Conta
                </button>
            </div>
        </div>
    </div>

    <script>
        function exportarDados() {
            alert('Funcionalidade de exportação será implementada em breve!');
        }
        
        function excluirConta() {
            if (confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')) {
                if (confirm('Digite "EXCLUIR" para confirmar a exclusão da conta:')) {
                    alert('Funcionalidade de exclusão será implementada em breve!');
                }
            }
        }
        
        // Validação de senha em tempo real
        document.querySelector('input[name="confirmar_senha"]').addEventListener('input', function() {
            const novaSenha = document.querySelector('input[name="nova_senha"]').value;
            const confirmarSenha = this.value;
            
            if (novaSenha !== confirmarSenha && confirmarSenha.length > 0) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html> 