<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id_ymp = $_POST['product_id_ymp'] ?? '';
    $nome_arquivo = $_FILES['pdf_file']['name'] ?? '';

    if (empty($product_id_ymp) || empty($nome_arquivo)) {
        echo "Preencha todos os campos.";
        exit;
    }

    $upload_dir = __DIR__ . '/files/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $caminho_relativo = '/files/' . basename($nome_arquivo);
    $caminho_completo = $upload_dir . basename($nome_arquivo);

    if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $caminho_completo)) {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            $sql = "INSERT INTO arquivos_pdf (product_id_ymp, nome, caminho) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$product_id_ymp, $nome_arquivo, $caminho_relativo]);

            echo "Arquivo cadastrado com sucesso!";
        } catch (Exception $e) {
            echo "Erro ao cadastrar arquivo: " . $e->getMessage();
        }
    } else {
        echo "Erro ao enviar arquivo.";
    }
} else {
?>
    <form method="POST" enctype="multipart/form-data">
        <label>Product ID Yampi:<br>
            <input type="text" name="product_id_ymp" required>
        </label><br><br>
        <label>Arquivo PDF:<br>
            <input type="file" name="pdf_file" accept="application/pdf" required>
        </label><br><br>
        <button type="submit">Cadastrar</button>
    </form>
<?php
}
