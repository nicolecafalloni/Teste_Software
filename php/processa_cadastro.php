<?php
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastrar.php');
    exit;
}

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirma_senha = $_POST['confirma_senha'] ?? '';

// Validação
if (empty($nome) || empty($email) || empty($senha) || empty($confirma_senha)) {
    header('Location: cadastrar.php?erro=vazio');
    exit;
}

if ($senha !== $confirma_senha) {
    header('Location: cadastrar.php?erro=senhas_nao_conferem');
    exit;
}

// Sanitização
$nome_seguro = filter_var($nome, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$email_seguro = filter_var($email, FILTER_SANITIZE_EMAIL);
$senha_segura = htmlspecialchars($senha, ENT_QUOTES, 'UTF-8');

if ($nome !== $nome_seguro || $email !== $email_seguro || $senha !== $senha_segura) {
    header('Location: cadastrar.php?erro=xss');
    exit;
}

// Criptografar senha
$senha_hash = password_hash($senha_segura, PASSWORD_DEFAULT);

try {
    // Verifica se o e-mail já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email_seguro]);
    if ($stmt->fetch()) {
        header('Location: cadastrar.php?erro=email_existe');
        exit;
    }

    // Inserir usuário
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->execute([$nome_seguro, $email_seguro, $senha_hash]);

    header('Location: cadastrar.php?sucesso=ok');
    exit;

} catch (PDOException $e) {
    header('Location: cadastrar.php?erro=falha_db');
    exit;
}
?>
