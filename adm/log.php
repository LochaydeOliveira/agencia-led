<?php
// session_start();
// if (!isset($_SESSION['admin'])) {
//     header("Location: login.php");
//     exit;
// }

require '../config/config.php';

$logPath = LOG_FILE;
$logContent = file_exists($logPath) ? array_reverse(file($logPath)) : [];
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Log do Webhook - Painel Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets-admin/admin.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>


<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/sidebar.php'; ?>

  <main class="container py-5 main-content">
      <div class="container">
        <h2 class="mb-4">Log de Atividades do Webhook</h2>
        <?php if (!empty($logContent)): ?>
        <pre><?php echo implode('', $logContent); ?></pre>
        <?php else: ?>
        <p class="text-muted">Nenhum log encontrado.</p>
        <?php endif; ?>
        <a href="dashboard.php" class="btn btn-secondary mt-4">Voltar ao Dashboard</a>
      </div>
  </main>
</div>

<?php include 'partials/footer.php'; ?>

</body>


</html>
