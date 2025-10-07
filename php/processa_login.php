<?php
// processa_login.php

// 1. Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// 2. Captura os dados do formulário
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// 3. Validação de campos obrigatórios
if (empty($email) || empty($senha)) {
    header('Location: login.php?erro=vazio');
    exit;
}

// 4. Sanitização contra XSS e SQL Injection
$email_seguro = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha_segura = htmlspecialchars($senha, ENT_QUOTES, 'UTF-8');

if ($email !== $email_seguro || $senha !== $senha_segura) {
    header('Location: login.php?erro=xss');
    exit;
}

// 5. Conexão com banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=acme', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    header('Location: login.php?erro=invalido');
    exit;
}

// 6. Verifica se o e-mail existe
$stmt = $pdo->prepare("SELECT id, senha FROM usuarios WHERE email = ?");
$stmt->execute([$email_seguro]);
$usuario = $stmt->fetch();

if (!$usuario) {
    // E-mail não encontrado
    header('Location: login.php?erro=invalido');
    exit;
}

// 7. Verifica senha
if (!password_verify($senha_segura, $usuario['senha'])) {
    // Senha incorreta
    header('Location: login.php?erro=invalido');
    exit;
}

// 8. Login bem-sucedido: inicia sessão e redireciona para painel
session_start();
$_SESSION['usuario_logado'] = $email_seguro;
header('Location: painel.php');
exit;
?>
