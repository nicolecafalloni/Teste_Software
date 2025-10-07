<?php
// processa_cadastrar.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

$nome            = $_POST['nome'] ?? '';
$email           = $_POST['email'] ?? '';
$senha           = $_POST['senha'] ?? '';
$confirma_senha  = $_POST['confirma_senha'] ?? '';

// 🔹 Validação de campos obrigatórios
if (empty($nome) || empty($email) || empty($senha) || empty($confirma_senha)) {
    header('Location: cadastro.php?erro=vazio');
    exit;
}

// 🔹 Verificação de senhas
if ($senha !== $confirma_senha) {
    header('Location: cadastro.php?erro=senhas_nao_conferem');
    exit;
}

// 🔹 Sanitização dos dados
$nome_seguro  = filter_var($nome, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$email_seguro = filter_var($email, FILTER_SANITIZE_EMAIL);
$senha_segura = htmlspecialchars($senha, ENT_QUOTES, 'UTF-8');

// 🔹 Detecção de tentativa de XSS
if ($nome !== $nome_seguro || $email !== $email_seguro || $senha !== $senha_segura) {
    header('Location: cadastro.php?erro=xss');
    exit;
}

// 🔹 Criptografar senha
$senha_hash = password_hash($senha_segura, PASSWORD_DEFAULT);

// 🔹 Conexão com o banco de dados (ajuste conforme seu ambiente)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=acme', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    header('Location: cadastro.php?erro=falha_db');
    exit;
}

// 🔹 Verifica se o e-mail já existe
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email_seguro]);
if ($stmt->fetch()) {
    header('Location: cadastro.php?erro=email_existe');
    exit;
}

// 🔹 Inserir novo usuário
$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");

try {
    if ($stmt->execute([$nome_seguro, $email_seguro, $senha_hash])) {
        header('Location: cadastro.php?sucesso=ok');
        exit;
    } else {
        header('Location: cadastro.php?erro=falha_db');
        exit;
    }
} catch (PDOException $e) {
    header('Location: cadastro.php?erro=falha_db');
    exit;
}
?>
