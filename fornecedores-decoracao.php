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

    <h1>Verificação de Código</h1>

    <form id="form-verificacao" novalidate autocomplete="off">
        <label for="codigo">Código do Pedido (15 dígitos):</label>
        <input type="text" id="codigo" name="codigo" maxlength="15" pattern="\d{15}" required autocomplete="off" inputmode="numeric" />
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

                // Validação client-side extra
                if (!/^\d{15}$/.test(codigo)) {
                    mensagem.textContent = "O código deve conter exatamente 15 dígitos numéricos.";
                    mensagem.className = "erro";
                    inputCodigo.focus();
                    return;
                }

                botao.disabled = true;
                mensagem.textContent = "Verificando código...";
                mensagem.className = "";

                try {
                    const resposta = await fetch('verificar-codigo.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `codigo=${encodeURIComponent(codigo)}`
                    });

                    if (!resposta.ok) {
                        throw new Error(`HTTP error! status: ${resposta.status}`);
                    }

                    const resultado = await resposta.json();

                    mensagem.textContent = resultado.mensagem;
                    mensagem.className = resultado.status === 'sucesso' ? 'sucesso' : 'erro';

                    if (resultado.status === 'sucesso' && resultado.link) {
                        // Pequena pausa para o usuário ler a mensagem, depois redireciona
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
