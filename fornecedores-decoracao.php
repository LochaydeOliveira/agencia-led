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

<style>
    body {
      background: #ebebeb;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    .box {
      max-width: 500px;
      margin: 5rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 30px;
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
      text-align: center;
      display: <?= $liberado ? 'block' : 'none' ?>;
    }

    .btn-dwnld {
      background: #38b97e;
      color: #fff;
      padding: 10px 20px;
      border: none;
      text-decoration: none;
      border-radius: 5px;
      display: inline-block;
      margin-top: 15px;
    }

    .popup {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.7);
      display: <?= $liberado ? 'none' : 'flex' ?>;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .popup-content {
      background: #fff;
      padding: 2rem;
      border-radius: 20px;
      max-width: 400px;
      text-align: center;
    }

    input {
      padding: 10px;
      width: 100%;
      margin-top: 10px;
      box-sizing: border-box;
    }

    button {
      margin-top: 15px;
      padding: 10px 20px;
      background: #503296;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .error {
      color: red;
      margin-top: 10px;
    }

    #timer {
      margin-top: 20px;
      font-weight: bold;
      color: #ff0000;
    }

</style>

</head>
<body>

<div class="popup" id="popup">
  <div class="popup-content">
    <h2>Informe o Código do Pedido</h2>
    <input type="text" id="codigo" placeholder="Digite seu código de pedido" />
    <button onclick="verificarCodigo()">Verificar</button>
    <div class="error" id="mensagem-erro"></div>

    <div id="codigo-expirado" style="display:none; margin-top: 20px;">
      <p style="color:#b00020; font-weight:bold;">⏱️ Código expirado! Seu tempo de download terminou.</p>
      <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
        <a href="https://seguro.agencialed.com/r/NEOYZECNBO"
           target="_blank"
           style="padding:10px 15px; background:#38b97e; color:white; text-decoration:none; border-radius:5px;">
          Comprar Novamente
        </a>
        <a href="https://wa.me/558599671024"
           target="_blank"
           style="padding:10px 15px; background:#25d366; color:white; text-decoration:none; border-radius:5px;">
          Falar com Suporte
        </a>
      </div>
    </div>
  </div>
</div>

<div class="box" id="conteudo">
  <h1>Seu eBook está pronto para download!</h1>
  <img width="150" height="250" src="capa-lista-decoração-slim.jpg" alt="Capa da lista de fornecedores" />
  <p><strong>Fornecedores Nacionais de Decoração</strong></p>
  <a href="fornecedores-nacionais-decoracao.pdf" class="btn-dwnld" download>Baixar eBook Agora</a>
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
    erro.textContent = 'Erro na comunicação. Tente novamente.';
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