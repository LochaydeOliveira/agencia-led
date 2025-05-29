<?php

require 'protect.php';
require '../conexao.php';

    $stmt = $pdo->query("SELECT id, nome, preco, link_de_compra FROM listas ORDER BY id DESC");
    $listas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-br">


<head>
  <meta charset="UTF-8">
  <title>Listas - Painel Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets-admin/admin.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/sidebar.php'; ?>

    <main class="flex-grow-1 p-4 main-adm-content"> 
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listas Cadastradas</h2>
        <a href="nova-lista.php" class="btn btn-success">Nova Lista</a>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>Nome</th>
              <th>Preço</th>
              <th>Link de Compra</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($listas as $lista): ?>
              <tr>
                <td><?= htmlspecialchars($lista['nome']) ?></td>
                <td>R$ <?= number_format($lista['preco'], 2, ',', '.') ?></td>
                <td><a href="<?= htmlspecialchars($lista['link_de_compra']) ?>" target="_blank">Ver Link</a></td>
                <td>
                  <a href="editar-lista.php?id=<?= $lista['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                  <a href="excluir-lista.php?id=<?= $lista['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta lista?')">Excluir</a>
                  <a href="ver-html.php?id=<?= $lista['id'] ?>" class="btn btn-sm btn-secondary">Ver HTML</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>

</div>

<?php include 'partials/footer.php'; ?>


</body>
</html>

