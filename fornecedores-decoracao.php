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
            margin: auto;
        }
        label, input {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="text"]:invalid {
            border-color: red;
        }
        button {
            padding: 12px;
            font-size: 16px;
            background-color: #1e88e5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:disabled {
            background-color: #90caf9;
            cursor: not-allowed;
        }
        button:hover:not(:disabled) {
            background-color: #1565c0;
        }
        #mensagem {
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
            min-height: 24px;
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

    <h1>Verificação de Pedido</h1>

    <form id="form-verificacao" novalidate autocomplete="off">
        <label for="codigo">Número do Pedido:</label>
        <input type="text" id="codigo" name="codigo" required autocomplete="off" inputmode="numeric" />
        <button type="submit" id="botao">Verificar</button>
    </form>

    <div id="mensagem" role="alert" aria-live="polite"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('form-verificacao');
            const mensagem = document.getElementById('mensagem');
            const botao = document.getElementById('botao');
            const inputCodigo = document.getElementById('codigo');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const codigo = inputCodigo.value.trim();

                if (!/^\d+$/.test(codigo)) {
                    mensagem.textContent = "Digite apenas números.";
                    mensagem.className = "erro";
                    inputCodigo.focus();
                    return;
                }

                botao.disabled = true;
                mensagem.textContent = "Verificando número do pedido...";
                mensagem.className = "";

                try {
                    const resposta = await fetch('verificar-codigo.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `codigo=${encodeURIComponent(codigo)}`
                    });

                    const resultado = await resposta.json();

                    mensagem.textContent = resultado.mensagem;
                    mensagem.className = resultado.status === 'sucesso' ? 'sucesso' : 'erro';

                    if (resultado.status === 'sucesso' && resultado.link) {
                        setTimeout(() => {
                            window.location.href = resultado.link;
                        }, 2000);
                    }
                } catch (error) {
                    mensagem.textContent = "Erro de comunicação. Tente novamente.";
                    mensagem.className = "erro";
                } finally {
                    botao.disabled = false;
                }
            });
        });
    </script>

</body>
</html>
