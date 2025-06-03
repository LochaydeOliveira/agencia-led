<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conexao.php';
session_start();

// Se já estiver logado, redireciona para o painel
if (isset($_SESSION['usuario']) && isset($_SESSION['nivel'])) {
    header("Location: adm/index.php");
    exit;
}

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validação dos campos
        if (empty($_POST["nome"]) || empty($_POST["senha"])) {
            throw new Exception("Por favor, preencha todos os campos.");
        }

        $nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING);
        $senha = $_POST["senha"];

        // Verifica se o usuário existe e está ativo
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nome = ? AND status = 'ativo'");
        $stmt->execute([$nome]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario["senha"])) {
            // Regenera o ID da sessão para prevenir session fixation
            session_regenerate_id(true);
            
            // Configura as variáveis de sessão
            $_SESSION["usuario"] = $usuario["nome"];
            $_SESSION["nivel"] = $usuario["nivel"];
            $_SESSION["id_usuario"] = $usuario["id"];
            $_SESSION["ultimo_acesso"] = time();

            // Registra o login no log
            error_log("Login bem-sucedido para o usuário: " . $usuario["nome"]);

            header("Location: adm/index.php");
            exit;
        } else {
            throw new Exception("Nome ou senha incorretos, ou usuário inativo.");
        }
    } catch (Exception $e) {
        $erro = $e->getMessage();
        error_log("Erro no login: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Administração</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            color: white;
        }
    </style>
</head>
<body>
<div class="form-container animate__animated animate__fadeIn">
    <h2 class="mb-4 text-center">Login Admin</h2>
    <?php if ($erro): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo $erro; ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <input type="text" name="nome" class="form-control" placeholder="Nome" required>
        </div>
        <div class="mb-3">
            <input type="password" name="senha" class="form-control" placeholder="Senha" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-custom">Entrar</button>
        </div>
    </form>
</div>
</body>
</html>
