<?php
require 'conexao.php';
$mensagem = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
    if ($stmt->execute([$email, $senha])) {
        $mensagem = "Cadastro realizado com sucesso.";
    } else {
        $mensagem = "Erro ao cadastrar.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Área de Membros</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
               min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .form-container { background: #fff; border-radius: 15px; padding: 2rem;
                          box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .btn-custom { background: linear-gradient(135deg, #198754 0%, #157347 100%);
                      border: none; color: white; }
    </style>
</head>
<body>
<div class="form-container animate__animated animate__fadeIn">
    <h2 class="mb-4 text-center">Cadastro</h2>
    <?php if ($mensagem): ?>
        <div class="alert alert-info"><?php echo $mensagem; ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="E-mail" required>
        </div>
        <div class="mb-3">
            <input type="password" name="senha" class="form-control" placeholder="Senha" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-custom">Cadastrar</button>
        </div>
    </form>
</div>
</body>
</html>