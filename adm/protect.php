<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers para evitar cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();

// Log da sessão atual
error_log("Verificando sessão: " . print_r($_SESSION, true));

// Verifica se a sessão existe e se o usuário está logado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['nivel'])) {
    error_log("Sessão inválida: usuário ou nível não definidos");
    session_unset();
    session_destroy();
    header("Location: ../login_usuarios.php");
    exit;
}

// Verifica se o nível de acesso é válido
if (!in_array($_SESSION['nivel'], ['admin', 'operador'])) {
    error_log("Nível de acesso inválido: " . $_SESSION['nivel']);
    session_unset();
    session_destroy();
    header("Location: ../login_usuarios.php");
    exit;
}

// Verifica se o usuário ainda está ativo no banco
require '../conexao.php';
try {
    $stmt = $pdo->prepare("SELECT status FROM usuarios WHERE id = ? AND nome = ?");
    $stmt->execute([$_SESSION['id_usuario'], $_SESSION['usuario']]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        error_log("Usuário não encontrado no banco: ID=" . $_SESSION['id_usuario'] . ", Nome=" . $_SESSION['usuario']);
        session_unset();
        session_destroy();
        header("Location: ../login_usuarios.php");
        exit;
    }

    if ($usuario['status'] !== 'ativo') {
        error_log("Usuário inativo: ID=" . $_SESSION['id_usuario'] . ", Nome=" . $_SESSION['usuario']);
        session_unset();
        session_destroy();
        header("Location: ../login_usuarios.php");
        exit;
    }

    error_log("Usuário verificado com sucesso: ID=" . $_SESSION['id_usuario'] . ", Nome=" . $_SESSION['usuario']);
} catch (PDOException $e) {
    error_log("Erro ao verificar status do usuário: " . $e->getMessage());
    session_unset();
    session_destroy();
    header("Location: ../login_usuarios.php");
    exit;
}
?>
