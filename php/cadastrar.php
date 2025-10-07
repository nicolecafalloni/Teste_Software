<?php
// cadastro.php

// Pega os parâmetros da URL para exibir o SweetAlert
// 'erro' pode ser: vazio, xss, email_existe, senhas_nao_conferem, falha_db
$erro = $_GET['erro'] ?? null;

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACME Cadastro | Novo Usuário</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <form class="auth-form" id="registerForm" action="processa_cadastro.php" method="POST">
            <h1>Crie sua Conta</h1>
            <p>Junte-se ao portal ACME Digital com segurança.</p>
            
            <div class="input-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" required placeholder="Seu Nome Aqui">
            </div>

            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required placeholder="seu@email.com">
            </div>
            
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha_cad" name="senha" required placeholder="Mínimo 8 caracteres">
            </div>
            
            <div class="input-group">
                <label for="confirma_senha">Confirme a Senha</label>
                <input type="password" id="confirma_senha" name="confirma_senha" required placeholder="Repita a senha">
            </div>
            
            <button type="submit" class="purple-button">Cadastrar Agora</button>
            
            <span class="link-cadastro">Já tem conta? <a href="login.php">Faça Login</a></span>
        </form>
    </div>

    <script src="assets/js/validation.js"></script>
    
    <script>
        // Função centralizada para mostrar o SweetAlert
        function showSweetAlert(title, text, icon) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                confirmButtonColor: '#8A2BE2',
                confirmButtonText: 'OK'
            });
        }
        
        // Lógica para disparar o SweetAlert quando houver um parâmetro de erro na URL
        const erro = '<?php echo $erro; ?>';

        if (erro === 'vazio') {
            // Campos vazios
            showSweetAlert('Preenchimento Obrigatório', 'Por favor, preencha todos os campos.', 'warning');
        } else if (erro === 'xss') {
            // Tentativa de XSS/SQL Injection detectada
            showSweetAlert('Entrada Inválida', 'Tentativa de entrada inválida detectada. O servidor bloqueou a requisição.', 'error');
        } else if (erro === 'email_existe') {
            // E-mail já existe
            showSweetAlert('Falha no Cadastro', 'Este e-mail já está em uso. Tente outro ou faça login.', 'warning');
        } else if (erro === 'senhas_nao_conferem') {
            // Senhas diferentes
            showSweetAlert('Erro de Senha', 'A senha e a confirmação de senha não conferem.', 'error');
        } else if (erro === 'falha_db') {
            // Erro geral do sistema
            showSweetAlert('Erro de Sistema', 'Não foi possível completar seu cadastro. Tente novamente mais tarde.', 'error');
        }
    </script>
</body>
</html>