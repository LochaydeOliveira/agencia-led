<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verifica se a sessão existe e se o usuário está logado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['nivel'])) {
    header("Location: ../login_usuarios.php");
    exit;
}

// Verifica se o nível de acesso é válido
if (!in_array($_SESSION['nivel'], ['admin', 'operador'])) {
    header("Location: ../login_usuarios.php");
    exit;
}

// Verifica se o usuário ainda está ativo no banco
require '../conexao.php';
try {
    $stmt = $pdo->prepare("SELECT status FROM usuarios WHERE id = ? AND nome = ?");
    $stmt->execute([$_SESSION['id_usuario'], $_SESSION['usuario']]);
    $usuario = $stmt->fetch();

    if (!$usuario || $usuario['status'] !== 'ativo') {
        session_destroy();
        header("Location: ../login_usuarios.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Erro ao verificar status do usuário: " . $e->getMessage());
    header("Location: ../login_usuarios.php");
    exit;
}
?>
