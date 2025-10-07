<?php
// conexao.php
// Arquivo de conexão com o banco de dados ACME

$host = 'localhost';    // Servidor do banco
$db   = 'acme';         // Nome do banco de dados
$user = 'root';         // Usuário do banco
$pass = '';             // Senha do banco (ajuste se necessário)
$charset = 'utf8mb4';   // Charset recomendado

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Exibir erros
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retornar arrays associativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar prepared statements reais
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Caso ocorra algum erro na conexão
    die("Falha na conexão com o banco de dados: " . $e->getMessage());
}
?>
