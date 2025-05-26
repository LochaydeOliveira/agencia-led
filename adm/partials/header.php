
<?php
  require 'version.php';
  $versao = SISTEMA_VERSAO;
  ?>

<header class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
  <div class="d-flex align-items-center gap-3">
    <img src="assets-admin/logo-agencia-led-slim.png" alt="Logo" class="logo">
    <span class="text-muted small">Versão <?php echo $versao; ?></span>
  </div>

  <div class="d-flex align-items-center gap-3">
    <span class="text-muted small">Olá, Admin</span>
    <a href="../logout.php" class="btn btn-sm btn-outline-danger">Sair</a>
  </div>
</header>
