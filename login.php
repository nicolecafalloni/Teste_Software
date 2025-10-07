<?php
// Exemplo de inclusão para mostrar o SweetAlert via backend
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
        // Lógica para disparar o SweetAlert quando houver um parâmetro na URL
        const erro = '<?php echo $erro; ?>';
        const sucesso = '<?php echo $sucesso; ?>';

        if (erro === 'invalido') {
             // Credenciais inválidas (falha de login)
            showSweetAlert('Erro de Login', 'Credenciais inválidas. Verifique seu e-mail e senha.', 'error');
        } else if (sucesso === 'cadastrado') {
            // Sucesso após o cadastro (exemplo de redirecionamento do cadastro.php)
            showSweetAlert('Sucesso!', 'Cadastro realizado com sucesso. Faça login!', 'success');
        }
    </script>
</body>
</html>