<?php

  require 'protect.php';
  require '../conexao.php';

  // Processar exclusão
  if (isset($_POST['delete']) && isset($_POST['id'])) {
      $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
      $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
      $stmt->execute([$id]);
      header('Location: clientes.php?msg=deleted');
      exit;
  }

  // Processar edição
  if (isset($_POST['edit']) && isset($_POST['id'])) {
      $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
      $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
      $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
      $whatsapp = filter_input(INPUT_POST, 'whatsapp', FILTER_SANITIZE_STRING);
      $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
      $classificacao = filter_input(INPUT_POST, 'classificacao', FILTER_SANITIZE_STRING);

      $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, email = ?, whatsapp = ?, status = ?, classificacao = ? WHERE id = ?");
      $stmt->execute([$nome, $email, $whatsapp, $status, $classificacao, $id]);
      header('Location: clientes.php?msg=updated');
      exit;
  }

  // Busca e filtros
  $search = isset($_GET['search']) ? $_GET['search'] : '';
  $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
  $classificacao_filter = isset($_GET['classificacao']) ? $_GET['classificacao'] : '';

  // Construir query base
  $query = "SELECT id, nome, email, whatsapp, status, classificacao, criado_em FROM clientes WHERE 1=1";
  $params = [];

  if ($search) {
      $query .= " AND (nome LIKE ? OR email LIKE ? OR whatsapp LIKE ?)";
      $search_param = "%$search%";
      $params = array_merge($params, [$search_param, $search_param, $search_param]);
  }

  if ($status_filter) {
      $query .= " AND status = ?";
      $params[] = $status_filter;
  }

  if ($classificacao_filter) {
      $query .= " AND classificacao = ?";
      $params[] = $classificacao_filter;
  }

  $query .= " ORDER BY criado_em DESC";

  // Paginação
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $per_page = 10;
  $offset = ($page - 1) * $per_page;

  // Total de registros
  $count_query = str_replace("id, nome, email, whatsapp, status, classificacao, criado_em", "COUNT(*)", $query);
  $stmt = $pdo->prepare($count_query);
  $stmt->execute($params);
  $total_records = $stmt->fetchColumn();
  $total_pages = ceil($total_records / $per_page);

  // Buscar registros paginados
  $query .= " LIMIT " . (int)$per_page . " OFFSET " . (int)$offset;

  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Clientes - Painel Admin</title>
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
      <h2>Clientes Cadastrados</h2>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClienteModal">
        Novo Cliente
      </button>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" class="row g-3">
          <div class="col-md-4">
            <input type="text" class="form-control" name="search" placeholder="Buscar..." value="<?= htmlspecialchars($search) ?>">
          </div>
          <div class="col-md-3">
            <select class="form-select" name="status">
              <option value="">Todos os Status</option>
              <option value="ativo" <?= $status_filter === 'ativo' ? 'selected' : '' ?>>Ativo</option>
              <option value="inativo" <?= $status_filter === 'inativo' ? 'selected' : '' ?>>Inativo</option>
              <option value="suspenso" <?= $status_filter === 'suspenso' ? 'selected' : '' ?>>Suspenso</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select" name="classificacao">
              <option value="">Todas as Classificações</option>
              <option value="prata" <?= $classificacao_filter === 'prata' ? 'selected' : '' ?>>Prata</option>
              <option value="ouro" <?= $classificacao_filter === 'ouro' ? 'selected' : '' ?>>Ouro</option>
              <option value="diamante" <?= $classificacao_filter === 'diamante' ? 'selected' : '' ?>>Diamante</option>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabela de Clientes -->
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
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($clientes as $cliente): ?>
            <tr>
              <td><?= htmlspecialchars($cliente['nome']) ?></td>
              <td><?= htmlspecialchars($cliente['email']) ?></td>
              <td><?= htmlspecialchars($cliente['whatsapp']) ?></td>
              <td>
                <span class="badge bg-<?= 
                  $cliente['status'] === 'ativo' ? 'success' : 
                  ($cliente['status'] === 'inativo' ? 'secondary' : 'warning') 
                ?>">
                  <?= ucfirst($cliente['status']) ?>
                </span>
              </td>
              <td>
                <span class="badge bg-<?= 
                  $cliente['classificacao'] === 'prata' ? 'secondary' : 
                  ($cliente['classificacao'] === 'ouro' ? 'warning' : 'info') 
                ?> text-dark">
                  <?= ucfirst($cliente['classificacao']) ?>
                </span>
              </td>
              <td><?= date('d/m/Y H:i', strtotime($cliente['criado_em'])) ?></td>
              <td>
                <button class="btn btn-sm btn-primary" onclick="editCliente(<?= htmlspecialchars(json_encode($cliente)) ?>)">
                  Editar
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteCliente(<?= $cliente['id'] ?>)">
                  Excluir
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
              <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&classificacao=<?= urlencode($classificacao_filter) ?>">
                <?= $i ?>
              </a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </main>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editClienteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" id="edit_nome" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="edit_email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">WhatsApp</label>
            <input type="text" class="form-control" name="whatsapp" id="edit_whatsapp" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" id="edit_status" required>
              <option value="ativo">Ativo</option>
              <option value="inativo">Inativo</option>
              <option value="suspenso">Suspenso</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Classificação</label>
            <select class="form-select" name="classificacao" id="edit_classificacao" required>
              <option value="prata">Prata</option>
              <option value="ouro">Ouro</option>
              <option value="diamante">Diamante</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="edit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de Adição -->
<div class="modal fade" id="addClienteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Novo Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="add_cliente.php">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">WhatsApp</label>
            <input type="text" class="form-control" name="whatsapp" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" class="form-control" name="senha" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" required>
              <option value="ativo">Ativo</option>
              <option value="inativo">Inativo</option>
              <option value="suspenso">Suspenso</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Classificação</label>
            <select class="form-select" name="classificacao" id="classificacao" required onchange="toggleListasField()">
              <option value="prata">Prata</option>
              <option value="ouro">Ouro</option>
              <option value="diamante">Diamante</option>
            </select>
          </div>
          <div class="mb-3" id="listasField" style="display: none;">
            <label class="form-label">Listas Disponíveis</label>
            <select class="form-select" name="listas[]" multiple>
              <?php foreach ($listas as $lista): ?>
                <option value="<?= $lista['id'] ?>"><?= htmlspecialchars($lista['nome']) ?></option>
              <?php endforeach; ?>
            </select>
            <small class="form-text text-muted">Segure CTRL para selecionar múltiplas listas</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Adicionar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function editCliente(cliente) {
  document.getElementById('edit_id').value = cliente.id;
  document.getElementById('edit_nome').value = cliente.nome;
  document.getElementById('edit_email').value = cliente.email;
  document.getElementById('edit_whatsapp').value = cliente.whatsapp;
  document.getElementById('edit_status').value = cliente.status;
  document.getElementById('edit_classificacao').value = cliente.classificacao;
  
  new bootstrap.Modal(document.getElementById('editClienteModal')).show();
}

function deleteCliente(id) {
  Swal.fire({
    title: 'Tem certeza?',
    text: "Esta ação não poderá ser revertida!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sim, excluir!',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.innerHTML = `
        <input type="hidden" name="delete" value="1">
        <input type="hidden" name="id" value="${id}">
      `;
      document.body.appendChild(form);
      form.submit();
    }
  });
}

function toggleListasField() {
  const classificacao = document.getElementById('classificacao').value;
  const listasField = document.getElementById('listasField');
  listasField.style.display = (classificacao === 'ouro' || classificacao === 'diamante') ? 'block' : 'none';
}

// Mostrar mensagens de sucesso/erro
<?php if (isset($_GET['msg'])): ?>
    Swal.fire({
        icon: '<?= $_GET['msg'] === 'deleted' ? 'success' : ($_GET['msg'] === 'added' ? 'success' : 'success') ?>',
        title: '<?= $_GET['msg'] === 'deleted' ? 'Cliente excluído com sucesso!' : ($_GET['msg'] === 'added' ? 'Cliente adicionado com sucesso!' : 'Cliente atualizado com sucesso!') ?>',
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
