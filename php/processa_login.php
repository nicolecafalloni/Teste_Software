<?php
// processa_login.php
// ATENÇÃO: Se o seu index.php está na raiz, o redirecionamento para ele é `../index.php`
// Se o seu index.php está na mesma pasta, o redirecionamento é `index.php`
// Vamos manter `../index.php` conforme seu código original.

// 1. Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// 2. Captura os dados do formulário
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// 3. Validação de campos obrigatórios
if (empty($email) || empty($senha)) {
    header('Location: ../index.php?erro=vazio');
    exit;
}

// 4. Sanitização contra XSS e SQL Injection
$email_seguro = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha_segura = htmlspecialchars($senha, ENT_QUOTES, 'UTF-8');

if ($email !== $email_seguro || $senha !== $senha_segura) {
    header('Location: ../index.php?erro=xss');
    exit;
}

// 5. Conexão com banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=acme', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Erro crítico de conexão
    header('Location: ../index.php?erro=conexao'); 
    exit;
}

// 6. Verifica se o e-mail existe
$stmt = $pdo->prepare("SELECT id, senha FROM usuarios WHERE email = ?");
$stmt->execute([$email_seguro]);
$usuario = $stmt->fetch();

// 7. Verifica senha
if (!$usuario || !password_verify($senha_segura, $usuario['senha'])) {
    // E-mail não encontrado OU Senha incorreta
    header('Location: ../index.php?erro=invalido');
    exit;
}

// 8. Login bem-sucedido: inicia sessão e redireciona para painel COM parâmetro de sucesso
session_start();
$_SESSION['usuario_logado'] = $email_seguro;
// Assumindo que a página restrita é `painel.php` ou o próprio `index.php` (se ele for o painel).
// Vamos redirecionar para um painel.php após mostrar o alerta de sucesso.
header('Location: ../index.php?sucesso=login'); // Redireciona para o index para mostrar o alerta.
exit;
?>