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
  <link href="assets-admin/admin.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    
<?php include 'partials/header.php'; ?>

    <div class="d-flex">
        <?php include 'partials/sidebar.php'; ?>    

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
    </div>
    
<?php include 'partials/footer.php'; ?>

</body>
</html>
