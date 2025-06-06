<?php
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login_usuarios.php");
    exit;
}
?>
<header class="d-flex justify-content-between align-items-center p-3 border-bottom">

  <div class="d-flex logo-adm">
    <img src="assets-admin/logo-agencia-led-slim.png" alt="Logo" class="logo">
  </div>

  <div class="d-flex align-items-center gap-3">
    <span class="text-muted small">
      Olá, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
    </span>
  </div>

</header>
