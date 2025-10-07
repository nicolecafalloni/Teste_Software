<?php
// login.php

// Captura parâmetros da URL para exibir alertas SweetAlert
$erro = $_GET['erro'] ?? null;
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="login-container">
        <form class="auth-form" id="loginForm" action="php/processa_login.php" method="POST">
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

            <button type="submit" id="btn-login" class="purple-button">Entrar</button>

            <span class="link-cadastro">Não tem conta? <a href="php/cadastrar.php">Cadastre-se aqui</a></span>
        </form>
    </div>

    <script src="assets/js/validation.js"></script>

<script>
    function showSweetAlert(title, text, icon, timer = 0) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            timer: timer, // Se for 0, o usuário tem que fechar. Se for > 0, fecha automaticamente.
            showConfirmButton: (timer === 0), // Mostra o botão apenas se não tiver timer
            confirmButtonColor: '#8A2BE2',
            confirmButtonText: 'OK'
        });
    }

    const erro = '<?php echo $erro; ?>';
    const sucesso = '<?php echo $sucesso; ?>';

    // --- Lógica de Erro ---
    if (erro === 'invalido') {
        showSweetAlert('Erro de Login', 'Credenciais inválidas. Verifique seu e-mail e senha.', 'error');
    } else if (erro === 'vazio') {
        showSweetAlert('Preenchimento Obrigatório', 'Por favor, preencha todos os campos.', 'warning');
    } else if (erro === 'xss') {
        // Alerta de erro de segurança
        showSweetAlert('Acesso Negado', 'Tentativa de entrada inválida detectada. A requisição foi bloqueada.', 'error');
    } else if (erro === 'conexao') {
        // Novo erro de conexão com o BD
        showSweetAlert('Erro de Servidor', 'Não foi possível conectar ao banco de dados. Tente novamente mais tarde.', 'error');
    } 

    // --- Lógica de Sucesso (Login e Cadastro) ---
    else if (sucesso === 'login') {
        Swal.fire({
            title: 'Login Bem-Sucedido!',
            icon: 'success',
            timer: 2000, // Pop-up some em 2 segundos
            showConfirmButton: false
        }).then(() => {
            // Após o alerta sumir, redireciona o usuário para o painel restrito
            // Ajuste 'painel.php' para o nome do seu arquivo de painel restrito
            window.location.href = 'index.php'; 
        });
    } else if (sucesso === 'cadastrado') {
        showSweetAlert('Sucesso!', 'Cadastro realizado com sucesso. Faça login!', 'success');
    }
    
    // Opcional: Se houver parâmetros de erro/sucesso, limpa a URL para evitar que o alerta reapareça em F5.
    if (erro || sucesso) {
        // Remove os parâmetros de erro/sucesso da URL
        history.replaceState(null, null, window.location.pathname);
    }
</script>

</body>

</html>