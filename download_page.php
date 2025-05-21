<?php

error_reporting(E_ALL);

ini_set('display_errors', 1);



require_once __DIR__ . '/config/config.php';

require_once __DIR__ . '/src/Database.php';



// Função para log personalizado

function app_log($message) {

    $date = date('Y-m-d H:i:s');

    $logMessage = "[$date] $message" . PHP_EOL;

    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);

}



$error = '';

$success = false;

$token = '';

$alreadyDownloaded = false;



// Se o token foi fornecido via GET, preenche o campo

if (isset($_GET['token'])) {

    $token = htmlspecialchars($_GET['token']);

}



// Processa o formulário

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['token'] ?? '';

    

    if (empty($token)) {

        $error = 'Por favor, insira o token de download.';

    } else {

        try {

            $db = Database::getInstance();

            $conn = $db->getConnection();

            

            // Verifica se o token é válido e não expirou

            $sql = "SELECT o.id, o.order_number, o.customer_name, dt.expires_at, dt.downloaded 

                    FROM orders o 

                    JOIN download_tokens dt ON o.id = dt.order_id 

                    WHERE dt.token = ? AND dt.expires_at > NOW()";

            

            $stmt = $conn->prepare($sql);

            $stmt->execute([$token]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            

            if ($result) {

                $success = true;

                //$alreadyDownloaded = $result['downloaded'];

                app_log("Token válido encontrado para o pedido #" . $result['order_number']);

            } 
            
            else {

                $error = 'Token inválido ou expirado.';

                app_log("Token inválido ou expirado: $token");

            }

        } catch (Exception $e) {

            $error = 'Erro ao processar o token.';

            app_log("Erro ao processar token: " . $e->getMessage());

        }

    }

}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Vaidação de Token - Download</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" href="assets-agencia-led/img/icone-favorito-agencia-led.png" type="image/png">
    <link rel="apple-touch-icon" href="assets-agencia-led/img/icone-favorito-agencia-led.png">

<style>
    body{background:linear-gradient(135deg,#f5f7fa 0%,#c3cfe2 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}.download-container{background:#fff;border-radius:15px;box-shadow:0 10px 30px rgb(0 0 0 / .1);padding:2rem;max-width:500px;width:90%;margin:2rem;transition:all 0.3s ease}.download-container:hover{transform:translateY(-5px);box-shadow:0 15px 35px rgb(0 0 0 / .15)}.form-control{border-radius:10px;padding:.8rem;border:2px solid #e9ecef;transition:all 0.3s ease}.form-control:focus{border-color:#6c757d;box-shadow:0 0 0 .2rem rgb(108 117 125 / .25)}.btn-primary{border-radius:10px;padding:.8rem 2rem;background:linear-gradient(135deg,#6c757d 0%,#495057 100%);border:none;transition:all 0.3s ease}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgb(0 0 0 / .2)}.alert{border-radius:10px;margin-bottom:1rem}.download-icon{font-size:3rem;color:#6c757d;margin-bottom:1rem}.download-button{background:#28a745;color:#fff;padding:1rem 2rem;border-radius:3px;text-decoration:none;display:inline-block;margin-top:1rem;transition:all 0.3s ease;width:100%;text-align:center}.download-button:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgb(0 0 0 / .2);color:#fff}
</style>

</head>

<body>

    <div class="download-container animate__animated animate__fadeIn">

        <div class="text-center mb-4">

            <div class="download-icon">📄</div>

            <h2 class="mb-3">Download do Material</h2>

            <p class="text-muted">Valide seu token de acesso para baixar o material</p>

        </div>



        <?php if ($error): ?>

            <div class="alert alert-danger animate__animated animate__shakeX">

                <?php echo $error; ?>

            </div>

        <?php endif; ?>



        <?php if ($success): ?>

            <?php if ($alreadyDownloaded): ?>

                <div class="alert alert-warning animate__animated animate__fadeIn">

                    <h4 class="alert-heading">Arquivo Já Baixado!</h4>

                    <p>Este arquivo já foi baixado anteriormente. Se você precisar de uma nova cópia, entre em contato conosco pelo email <a href="mailto:contato@agencialed.com">contato@agencialed.com</a>.</p>

                </div>

            <?php else: ?>

                <div class="alert alert-success animate__animated animate__fadeIn">

                    <h4 class="alert-heading">Token Válido!</h4>

                    <p>Clique no botão abaixo e faça o download.</p>

                    <a id="botao-download"
                        href="#"
                        class="download-button animate__animated animate__pulse animate__infinite"
                        onclick="iniciarDownload('<?php echo urlencode($token); ?>'); return false;">
                            Baixar Agora
                    </a>

                    <div id="download-loading" class="text-center mt-4" style="display: none;">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Preparando o seu download...</p>
                    </div>

                    <p class="mt-3 text-muted small">

                        <i class="fas fa-info-circle"></i> Se não conseguiu baixar o arquivo, entre em contato pelo email 

                        <a href="mailto:contato@agencialed.com">contato@agencialed.com</a>

                    </p>

                </div>



<script>
function iniciarDownload(token) {
    document.getElementById('botao-download').style.display = 'none';
    const spinner = document.getElementById('download-loading');
    spinner.style.display = 'block';

    setTimeout(function () {
        // Esconde o spinner se quiser
        spinner.style.display = 'none';
        // Redireciona para o download
        window.location.href = 'download.php?token=' + encodeURIComponent(token);
    }, 2000);
}

</script>



            <?php endif; ?>

        <?php else: ?>

            <form method="POST" class="animate__animated animate__fadeIn">

                <div class="mb-3">

                    <input type="text" 

                           class="form-control" 

                           name="token" 

                           value="<?php echo $token; ?>"

                           placeholder="Digite seu token de acesso"

                           required>

                </div>

                <div class="d-grid">

                    <button type="submit" class="btn btn-primary">

                        Validar Token

                    </button>

                </div>

            </form>

        <?php endif; ?>

    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html> 