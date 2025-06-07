<?php

    require 'protect.php';
    require '../conexao.php';

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    $stmt = $pdo->prepare("SELECT nome, conteudo_html FROM listas WHERE id = ?");
    $stmt->execute([$id]);
    $lista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lista) {
        echo "<p>Lista não encontrada.</p>";
        exit;
  }
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Visualizar HTML - <?= htmlspecialchars($lista['nome']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets-admin/admin.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="icon" href="assets-agencia-led/img/icone-favorito-led.png" type="image/png">
  <link rel="apple-touch-icon" href="assets-agencia-led/img/icone-favorito-led.png">
</head>


<body class="p-4">

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/sidebar.php'; ?>

  <main class="container py-5 main-adm-content">
    <div class="container">
      <h2 class="mb-4">Visualizando HTML da Lista: <?= htmlspecialchars($lista['nome']) ?></h2>
      <div class="preview-box">
        <?= $lista['conteudo_html'] ?>
      </div>
        <a href="listas.php" class="btn btn-secondary mt-4">Voltar para Listas</a>
    </div>
  </main>

</div>

<?php include 'partials/footer.php'; ?>


</body>


</html>
