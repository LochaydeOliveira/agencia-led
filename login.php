<?php
require 'conexao.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($senha, $user["senha"])) {
        $_SESSION["usuario_id"] = $user["id"];
        header("Location: painel.php");
        exit;
    } else {
        echo "Email ou senha incorretos.";
    }
}
?>
<form method="post">
    <h2>Login</h2>
    Email: <input type="email" name="email" required><br>
    Senha: <input type="password" name="senha" required><br>
    <button type="submit">Entrar</button>
</form>