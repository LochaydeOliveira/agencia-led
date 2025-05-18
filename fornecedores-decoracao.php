<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Validação de Pedido</title>
<style>
    body {
        font-family: Arial, sans-serif;
        padding: 30px;
        background-color: #f5f5f5;
    }
    h1 {
        color: #333;
        text-align: center;
    }
    form {
        background-color: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        max-width: 400px;
        margin: 0 auto;
    }
    label, input {
        display: block;
        width: 100%;
        margin-bottom: 15px;
        font-size: 1rem;
    }
    input[type="text"] {
        padding: 10px;
        font-size: 1rem;
        box-sizing: border-box;
    }
    button {
        padding: 12px;
        font-size: 1rem;
        background-color: #1e88e5;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s ease;
    }
    button:hover, button:focus {
        background-color: #1565c0;
        outline: none;
    }
    #mensagem {
        margin-top: 20px;
        font-weight: bold;
        text-align: center;
        min-height: 1.5em;
    }
    #mensagem.sucesso {
        color: green;
    }
    #mensagem.erro {
        color: red;
    }
</style>
</head>
<body>

<h1>Verificação de Código</h1>

<form id="form-verificacao" aria-describedby="mensagem" novalidate>
    <label for="codigo">Código do Pedido (15 dígitos):</label>
    <input type="text" id="codigo" name="codigo" maxlength="15" pattern="\d{15}" required aria-required="true" aria-describedby="mensagem" inputmode="numeric" autocomplete="off" />
    <button type="submit">Verificar</button>
</form>

<div id="mensagem" role="alert" aria-live="polite"></div>

<script>
    const form = document.getElementById('form-verificacao');
    const mensagem = document.getElementById('mensagem');
    const inputCodigo = document.getElementById('codigo');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const codigo = inputCodigo.value.trim();

        // Validação cliente
        if (!/^\d{15}$/.test(codigo)) {
            mensagem.textContent = "O código deve conter exatamente 15 dígitos numéricos.";
            mensagem.className = "erro";
            return;
        }

        mensagem.textContent = "Verificando código...";
        mensagem.className = "";

        try {
            const response = await fetch('verificar-codigo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `codigo=${encodeURIComponent(codigo)}`
            });

            if (!response.ok) throw new Error('Erro na resposta do servidor.');

            const result = await response.json();

            mensagem.textContent = result.mensagem;
            mensagem.className = result.status === 'sucesso' ? 'sucesso' : 'erro';

            if (result.status === 'sucesso' && result.link) {
                setTimeout(() => {
                    window.location.href = result.link;
                }, 2000);
            }
        } catch (err) {
            mensagem.textContent = "Erro na comunicação com o servidor.";
            mensagem.className = "erro";
            console.error(err);
        }
    });
</script>

</body>
</html>
