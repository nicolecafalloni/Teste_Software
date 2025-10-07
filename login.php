<?php
// login.php

// Pega os parâmetros da URL para exibir o SweetAlert
// 'erro' pode ser: invalido, vazio, xss
$erro = $_GET['erro'] ?? null;
// 'sucesso' pode ser: cadastrado
$sucesso = $_GET['sucesso'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACME Login | Seguro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <form class="auth-form" id="loginForm" action="processa_login.php" method="POST">
            <h1>Acesso Seguro</h1>
            <p>Entre com suas credenciais.</p>
            
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required placeholder="seu@email.com">
            </div>
            
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required placeholder="**********">
            </div>
            
            <button type="submit" class="purple-button">Entrar</button>
            
            <span class="link-cadastro">Não tem conta? <a href="cadastro.php">Cadastre-se aqui</a></span>
        </form>
    </div>

    <script src="assets/js/validation.js"></script>
    
    <script>
        // Função para mostrar o SweetAlert
        function showSweetAlert(title, text, icon) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                confirmButtonText: 'OK'
            });
        }

        // Lógica para disparar o SweetAlert baseado nos parâmetros da URL (do PHP)
        const erro = '<?php echo $erro; ?>';
        const sucesso = '<?php echo $sucesso; ?>';

        if (erro === 'invalido') {
            // Credenciais inválidas (falha de login) [cite: 19]
            showSweetAlert('Erro de Login', 'Credenciais inválidas. Verifique seu e-mail e senha.', 'error');
        } else if (erro === 'vazio') {
            // Campos vazios [cite: 19]
            showSweetAlert('Preenchimento Obrigatório', 'Por favor, preencha todos os campos.', 'warning');
        } else if (erro === 'xss') {
            // Tentativa de XSS/SQL Injection detectada [cite: 20]
            showSweetAlert('Entrada Inválida', 'Tentativa de entrada inválida detectada. O servidor bloqueou a requisição.', 'error');
        } else if (sucesso === 'cadastrado') {
            // Sucesso após o cadastro [cite: 18]
            showSweetAlert('Sucesso!', 'Cadastro realizado com sucesso. Faça login!', 'success');
        }
    </script>
</body>
</html>