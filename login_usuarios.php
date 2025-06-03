<?php
session_start();
require 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $senha = $_POST["senha"];

    try {
        // Busca o usuário pelo nome
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nome = ? AND status = 'ativo'");
        $stmt->execute([$nome]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario["senha"])) {
            // Login bem sucedido
            $_SESSION["usuario"] = $usuario["nome"];
            $_SESSION["nivel"] = $usuario["nivel"];
            $_SESSION["id_usuario"] = $usuario["id"];
            $_SESSION["email"] = $usuario["email"];
            
            header("Location: adm/index.php");
            exit;
        } else {
            if (!$usuario) {
                $erro = "Usuário não encontrado ou inativo.";
            } else {
                $erro = "Senha incorreta.";
            }
        }
    } catch (PDOException $e) {
        $erro = "Erro ao tentar fazer login. Por favor, tente novamente.";
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
    <style>
        body {
            background: #f5f7fa;
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
    </style>
</head>
<body>
<div class="form-container">
    <h2 class="mb-4 text-center">Login Admin</h2>
    <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <input type="text" name="nome" class="form-control" placeholder="Nome" required>
        </div>
        <div class="mb-3">
            <input type="password" name="senha" class="form-control" placeholder="Senha" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Entrar</button>
        </div>
    </form>
</div>
</body>
</html>
