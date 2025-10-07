<?php
// processa_cadastro.php

// 1. Verificar se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

// 2. Coletar os dados
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirma_senha = $_POST['confirma_senha'] ?? '';


// --- REGRAS DE SEGURANÇA E VALIDAÇÃO ---

// 3. Impedir envio com campos vazios
if (empty($nome) || empty($email) || empty($senha) || empty($confirma_senha)) {
    header('Location: cadastro.php?erro=vazio');
    exit;
}

// 4. Checar se as senhas conferem
if ($senha !== $confirma_senha) {
    header('Location: cadastro.php?erro=senhas_nao_conferem');
    exit;
}

// 5. Tratamento contra XSS e SQL Injection (Sanitização)

// Sanitize/Limpar o nome (removendo tags HTML, mas permitindo letras e espaços)
$nome_seguro = filter_var($nome, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH); 
// Sanitize o email
$email_seguro = filter_var($email, FILTER_SANITIZE_EMAIL);
// Proteja as senhas (apenas para exibição se necessário, mas o foco é no hash)
$senha_segura = htmlspecialchars($senha, ENT_QUOTES, 'UTF-8');


// Simulação de detecção de XSS: se algum campo sanitizado for drasticamente diferente do original
if ($nome !== $nome_seguro || $email !== $email_seguro || $senha !== $senha_segura) {
    header('Location: cadastro.php?erro=xss');
    exit;
}

// 6. Verificar se o E-mail já existe (Simulação de consulta ao DB)
// EM PRODUÇÃO: Usar Prepared Statements para evitar SQL Injection.
$email_simulado_existente = 'existente@acme.com'; // Exemplo de um email já cadastrado

if ($email_seguro === $email_simulado_existente) {
    header('Location: cadastro.php?erro=email_existe');
    exit;
}

// 7. Hash da Senha e Inserção no Banco de Dados

// Criptografia segura da senha (Obrigatório por segurança)
$senha_hash = password_hash($senha_segura, PASSWORD_DEFAULT);

// AQUI entraria o código real de conexão e inserção no banco de dados.
/*
$pdo = new PDO('...');
$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
if ($stmt->execute([$nome_seguro, $email_seguro, $senha_hash])) {
    // Sucesso
} else {
    // Falha de DB
    header('Location: cadastro.php?erro=falha_db');
    exit;
}
*/

// SIMULAÇÃO DE SUCESSO DE CADASTRO
$cadastro_sucesso = true;

if ($cadastro_sucesso) {
    // 8. Redirecionar para a tela de Login com a notificação de sucesso
    header('Location: login.php?sucesso=cadastrado');
    exit;
} else {
    // 9. Se a simulação falhar (ou se o DB falhar no futuro)
    header('Location: cadastro.php?erro=falha_db');
    exit;
}
?>