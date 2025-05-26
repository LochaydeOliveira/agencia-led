<?php
// session_start();
// if (!isset($_SESSION['admin'])) {
//     header("Location: login.php");
//     exit;
// }

require '../conexao.php';

$stmt = $pdo->query("SELECT id, nome, email, whatsapp, status, classificacao, criado_em FROM clientes ORDER BY criado_em DESC");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Clientes - Painel Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets-agencia-led/style.css" rel="stylesheet">
</head>
<body>
<header>
  <nav class="navbar navbar-expand-lg shadow-bg container">
    <div id="iconUser" class="content-user">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30">
        <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm-4,21.164v-.164c0-2.206,1.794-4,4-4s4,1.794,4,4v.164c-1.226.537-2.578.836-4,.836s-2.774-.299-4-.836Zm9.925-1.113c-.456-2.859-2.939-5.051-5.925-5.051s-5.468,2.192-5.925,5.051c-2.47-1.823-4.075-4.753-4.075-8.051C2,6.486,6.486,2,12,2s10,4.486,10,10c0,3.298-1.605,6.228-4.075,8.051Zm-5.925-15.051c-2.206,0-4,1.794-4,4s1.794,4,4,4,4-1.794,4-4-1.794-4-4-4Zm0,6c-1.103,0-2-.897-2-2s.897-2,2-2,2,.897,2,2-.897,2-2,2Z"/>
      </svg>
      <div>
        <p class="mb-0">Olá, <strong>Admin</strong>!</p>
      </div>
    </div>
  </nav>
</header>

<main class="container py-5 main-content">
  <h2 class="mb-4">Clientes Cadastrados</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Nome</th>
          <th>Email</th>
          <th>WhatsApp</th>
          <th>Status</th>
          <th>Classificação</th>
          <th>Criado em</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($clientes as $cliente): ?>
          <tr>
            <td><?= htmlspecialchars($cliente['nome']) ?></td>
            <td><?= htmlspecialchars($cliente['email']) ?></td>
            <td><?= htmlspecialchars($cliente['whatsapp']) ?></td>
            <td><span class="badge bg-<?= $cliente['status'] === 'ativo' ? 'success' : ($cliente['status'] === 'inativo' ? 'secondary' : 'danger') ?>"><?php echo $cliente['status']; ?></span></td>
            <td><span class="badge bg-warning text-dark"><?php echo ucfirst($cliente['classificacao']); ?></span></td>
            <td><?= date('d/m/Y H:i', strtotime($cliente['criado_em'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
