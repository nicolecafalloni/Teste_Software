<?php
// processa_login.php

// 1. Verificar se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Se não for POST, redireciona para o login.
    header('Location: login.php');
    exit;
}

// 2. Coletar e limpar os dados
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// --- REGRAS DE SEGURANÇA E VALIDAÇÃO ---

// 3. Impedir envio com campos vazios 
if (empty($email) || empty($senha)) {
    // Redireciona de volta para o login.php com o parâmetro de erro 'vazio'
    header('Location: login.php?erro=vazio');
    exit;
}

// 4. Tratamento contra XSS e SQL Injection (Simulação) 
// A forma mais segura é usar prepared statements (PDO/mysqli) para SQL Injection.
// Para XSS, o filter_var e htmlspecialchars são cruciais.

// A função 'filter_input' é recomendada por ser mais segura.
$email_seguro = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha_segura = htmlspecialchars($senha, ENT_QUOTES, 'UTF-8');

// Simulação de detecção de tentativa de XSS/SQLI:
// Se o valor limpo (htmlspecialchars) for diferente do original, pode indicar caracteres maliciosos.
// ATENÇÃO: A validação mais robusta deve ser feita no DB (Prepared Statements) e no Frontend (evitar caracteres especiais).
if ($email !== $email_seguro || $senha !== $senha_segura) {
    // Redireciona com o erro de entrada inválida (XSS detectada) [cite: 20, 23]
    header('Location: login.php?erro=xss');
    exit;
}

// 5. Autenticação de Credenciais (Simulação) 
// Em um cenário real, você faria:
// - Conexão segura com o Banco de Dados.
// - Usaria Prepared Statements.
// - Verificaria o hash da senha: password_verify($senha_segura, $hash_do_banco);

// Credenciais de teste simuladas:
$email_valido = 'teste@acme.com';
$senha_hash_valida = '$2y$10$92e10G9gY9Kj9Kj9Kj9KjO.h6j.G80yQ0gX9g/p2g0j/k9l9k9'; // Hash de 'senha123' (exemplo)

// ATENÇÃO: Para este exercício, vamos simplificar para fins de demonstração.
// A linha abaixo DEVE ser substituída por uma verificação de hash em produção.

$login_sucesso = ($email_seguro === $email_valido && $senha_segura === 'senha123'); // Simulação simplificada (NÃO USE EM PRODUÇÃO)

// Simulação de verificação de senha REAL:
// $login_sucesso = ($email_seguro === $email_valido && password_verify($senha_segura, $senha_hash_valida));

if ($login_sucesso) {
    // Login com sucesso [cite: 18]
    // 6. Iniciar sessão e redirecionar para o painel
    session_start();
    $_SESSION['usuario_logado'] = $email_seguro;
    header('Location: painel.php');
    exit;
} else {
    // Credenciais inválidas [cite: 19]
    // Redireciona de volta para o login.php com o parâmetro de erro 'invalido'
    header('Location: login.php?erro=invalido');
    exit;
}

?>