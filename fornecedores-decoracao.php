<?php
session_start();
$liberado = false;

if (isset($_COOKIE['ebook_liberado']) && $_COOKIE['ebook_liberado'] == '1') {
  $liberado = true;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Download do eBook</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f5f7fa, #e4ebf5);
      color: #333;
    }

    .container {
      max-width: 600px;
      margin: 6vh auto;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      padding: 3rem 2rem;
      text-align: center;
      display: <?= $liberado ? 'block' : 'none' ?>;
      animation: fadeIn 0.6s ease;
    }

    .container h1 {
      font-size: 1.8rem;
      margin-bottom: 0.8rem;
    }

    .container p {
      font-size: 1rem;
      margin-bottom: 1.5rem;
    }

    .btn-dwnld {
      background: #503296;
      color: white;
      padding: 14px 26px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      display: inline-block;
    }

    .btn-dwnld:hover {
      background: #3f2479;
      transform: scale(1.03);
    }

    .img-ebook {
      width: 180px;
      margin: 1.5rem auto;
      border-radius: 10px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    #timer {
      margin-top: 20px;
      font-weight: bold;
      color: #d63031;
    }

    /* POPUP */
    .popup {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.55);
      display: <?= $liberado ? 'none' : 'flex' ?>;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      padding: 1rem;
    }

    .popup-content {
      background: #fff;
      padding: 2rem;
      border-radius: 20px;
      width: 100%;
      max-width: 420px;
      text-align: center;
      box-shadow: 0 12px 30px rgba(0,0,0,0.15);
      animation: fadeIn 0.6s ease;
    }

    .popup-content h2 {
      margin-bottom: 1rem;
      font-size: 1.4rem;
    }

    input {
      padding: 12px;
      width: 100%;
      margin-top: 15px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    button {
      margin-top: 18px;
      padding: 12px 20px;
      background: #38b97e;
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #2c9b68;
    }

    .error {
      color: red;
      margin-top: 12px;
      font-size: 0.9rem;
    }

    #codigo-expirado {
      display: none;
      margin-top: 20px;
    }

    .expirado-links {
      margin-top: 15px;
      display: flex;
      gap: 10px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .expirado-links a {
      padding: 10px 16px;
      border-radius: 8px;
      text-decoration: none;
      color: white;
      font-weight: bold;
    }

    .expirado-links a:first-child {
      background: #503296;
    }

    .expirado-links a:last-child {
      background: #25d366;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 500px) {
      .container {
        padding: 2rem 1.5rem;
        margin: 3vh 1rem;
      }

      .img-ebook {
        width: 140px;
      }
    }
  </style>
</head>
<body>

<div class="popup" id="popup">
  <div class="popup-content">
    <h2>🔐 Acesso exclusivo</h2>
    <p>Insira o código do seu pedido para liberar o download:</p>
    <input type="text" id="codigo" placeholder="Ex: #123456" />
    <button onclick="verificarCodigo()">Verificar</button>
    <div class="error" id="mensagem-erro"></div>

    <div id="codigo-expirado">
      <p style="color:#b00020; font-weight:bold;">⏱️ Código expirado! Tempo de download encerrado.</p>
      <div class="expirado-links">
        <a href="https://seguro.agencialed.com/r/NEOYZECNBO" target="_blank">Comprar Novamente</a>
        <a href="https://wa.me/558599671024" target="_blank">Falar com Suporte</a>
      </div>
    </div>
  </div>
</div>

<div class="container" id="conteudo">
  <h1>🎉 Seu eBook está pronto!</h1>
  <img class="img-ebook" src="capa-lista-decoração-slim.jpg" alt="Capa do eBook" />
  <p><strong>Fornecedores Nacionais de Decoração</strong></p>
  <a href="fornecedores-nacionais-decoracao.pdf" class="btn-dwnld" download>📥 Baixar eBook Agora</a>
  <div id="timer"></div>
</div>

<script>
function verificarCodigo() {
  const codigo = document.getElementById('codigo').value.trim();
  const erro = document.getElementById('mensagem-erro');
  erro.textContent = '';

  if (!codigo) {
    erro.textContent = 'Por favor, digite o código do pedido.';
    return;
  }

  fetch('verificar-codigo.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'codigo=' + encodeURIComponent(codigo)
  })
  .then(res => res.text())
  .then(res => {
    if (res === 'sucesso') {
      setTimeout(() => window.location.reload(), 1000);
    } else {
      erro.textContent = res;
    }
  })
  .catch(() => {
    erro.textContent = 'Erro na verificação. Tente novamente.';
  });
}

<?php if ($liberado): ?>
const tempoRestante = 30 * 60 * 1000;
const fim = new Date().getTime() + tempoRestante;
const timerDiv = document.getElementById('timer');

const x = setInterval(() => {
  const agora = new Date().getTime();
  const dist = fim - agora;

  if (dist <= 0) {
    clearInterval(x);
    document.cookie = "ebook_liberado=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.getElementById('popup').style.display = 'flex';
    document.getElementById('conteudo').style.display = 'none';
    document.getElementById('codigo-expirado').style.display = 'block';
    timerDiv.innerHTML = '';
  } else {
    const min = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60));
    const seg = Math.floor((dist % (1000 * 60)) / 1000);
    timerDiv.innerHTML = `⏳ Tempo restante para download: ${min}m ${seg}s`;
  }
}, 1000);
<?php endif; ?>
</script>

</body>
</html>
