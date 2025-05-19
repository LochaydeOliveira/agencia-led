<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        button:hover {
            background-color: #1565c0;
        }
        #mensagem {
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
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

    <form id="form-verificacao" novalidate autocomplete="off">
        <label for="codigo">Código do Pedido (15 dígitos):</label>
        <input type="text" id="codigo" name="codigo" maxlength="15" pattern="\d{15}" required autocomplete="off">
        <button type="submit" id="botao">Verificar</button>
    </form>

    <div id="mensagem"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('form-verificacao');
            const mensagem = document.getElementById('mensagem');
            const botao = document.getElementById('botao');

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const codigo = document.getElementById('codigo').value.trim();

                if (!/^\d{15}$/.test(codigo)) {
                    mensagem.textContent = "O código deve conter exatamente 15 dígitos numéricos.";
                    mensagem.className = "erro";
                    return;
                }

                botao.disabled = true;

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
                    console.error('Erro na requisição:', error);
                    mensagem.textContent = "Erro na comunicação com o servidor. Tente novamente mais tarde.";
                    mensagem.className = "erro";
                } finally {
                    botao.disabled = false;
                }
            });
        });
    </script>

</body>
</html>
