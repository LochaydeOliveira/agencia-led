<?php

require 'protect.php';
require '../conexao.php';

// Processar atualização de status
if (isset($_POST['update_status']) && isset($_POST['order_number'])) {
    $order_number = filter_input(INPUT_POST, 'order_number', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_number = ?");
    $stmt->execute([$status, $order_number]);
    header('Location: pedidos.php?msg=updated');
    exit;
}

// Busca e filtros
$search = isset($_GET['search']) ? $_GET['search'] : '';
$statusFiltro = isset($_GET['status']) ? $_GET['status'] : '';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

// Construir query base
$query = "SELECT order_number, customer_name, customer_email, product_id, status, created_at FROM orders WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (order_number LIKE ? OR customer_name LIKE ? OR customer_email LIKE ? OR product_id LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if ($statusFiltro) {
    $query .= " AND status = ?";
    $params[] = $statusFiltro;
}

if ($data_inicio) {
    $query .= " AND DATE(created_at) >= ?";
    $params[] = $data_inicio;
}

if ($data_fim) {
    $query .= " AND DATE(created_at) <= ?";
    $params[] = $data_fim;
}

$query .= " ORDER BY created_at DESC";

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Total de registros
$count_query = str_replace("o.id, o.order_number, c.nome as cliente_nome, c.email as cliente_email, o.product_id, o.status, o.created_at", "COUNT(*)", $query);
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $per_page);

// Buscar registros paginados
$query .= " LIMIT " . (int)$per_page . " OFFSET " . (int)$offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>   
  <meta charset="UTF-8">
  <title>Pedidos - Painel Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets-admin/admin.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/sidebar.php'; ?>

  <main class="flex-grow-1 p-4 main-adm-content"> 
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Pedidos</h2>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" class="row g-3">
          <div class="col-md-3">
            <input type="text" class="form-control" name="search" placeholder="Buscar..." value="<?= htmlspecialchars($search) ?>">
          </div>
          <div class="col-md-2">
            <select name="status" class="form-select">
              <option value="">Todos os Status</option>
              <option value="paid" <?= $statusFiltro === 'paid' ? 'selected' : '' ?>>Pago</option>
              <option value="pending" <?= $statusFiltro === 'pending' ? 'selected' : '' ?>>Pendente</option>
              <option value="cancelled" <?= $statusFiltro === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
              <option value="refused" <?= $statusFiltro === 'refused' ? 'selected' : '' ?>>Recusado</option>
            </select>
          </div>
          <div class="col-md-2">
            <input type="date" class="form-control" name="data_inicio" value="<?= $data_inicio ?>" placeholder="Data Início">
          </div>
          <div class="col-md-2">
            <input type="date" class="form-control" name="data_fim" value="<?= $data_fim ?>" placeholder="Data Fim">
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Nº do Pedido</th>
            <th>Cliente</th>
            <th>Email</th>
            <th>Produto</th>
            <th>Status</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pedidos as $pedido): ?>
            <tr>
              <td><?= htmlspecialchars($pedido['order_number']) ?></td>
              <td><?= htmlspecialchars($pedido['customer_name']) ?></td>
              <td><?= htmlspecialchars($pedido['customer_email']) ?></td>
              <td><?= htmlspecialchars($pedido['product_id']) ?></td>
              <td>
                <span class="badge bg-<?= 
                  $pedido['status'] === 'paid' ? 'success' : 
                  ($pedido['status'] === 'pending' ? 'warning' : 
                  ($pedido['status'] === 'cancelled' ? 'danger' : 
                  ($pedido['status'] === 'refused' ? 'secondary' : 'primary'))) 
                ?>">
                  <?= ucfirst($pedido['status']) ?>
                </span>
              </td>
              <td><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></td>
              <td>
                <button class="btn btn-sm btn-primary" onclick="editStatus('<?= $pedido['order_number'] ?>', '<?= $pedido['status'] ?>')">
                  Alterar Status
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginação -->
    <?php if ($total_pages > 1): ?>
      <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFiltro) ?>&data_inicio=<?= urlencode($data_inicio) ?>&data_fim=<?= urlencode($data_fim) ?>">
                <?= $i ?>
              </a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </main>
</div>

<!-- Modal de Edição de Status -->
<div class="modal fade" id="editStatusModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alterar Status do Pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="order_number" id="edit_order_number">
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" id="edit_status" required>
              <option value="paid">Pago</option>
              <option value="pending">Pendente</option>
              <option value="cancelled">Cancelado</option>
              <option value="refused">Recusado</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="update_status" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function editStatus(orderNumber, currentStatus) {
  document.getElementById('edit_order_number').value = orderNumber;
  document.getElementById('edit_status').value = currentStatus;
  
  new bootstrap.Modal(document.getElementById('editStatusModal')).show();
}

// Mostrar mensagens de sucesso/erro
<?php if (isset($_GET['msg'])): ?>
  Swal.fire({
    icon: 'success',
    title: 'Status atualizado com sucesso!',
    showConfirmButton: false,
    timer: 1500
  });
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
  Swal.fire({
    icon: 'error',
    title: 'Erro!',
    text: 'Ocorreu um erro ao processar sua solicitação.',
    showConfirmButton: false,
    timer: 1500
  });
<?php endif; ?>
</script>

</body>
</html>
