<?php

// Function to load .env file manually
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load the .env file
loadEnv(__DIR__ . '/.env');

$host = getenv('DB_HOST');
$usuario = getenv('DB_USER');
$senha = getenv('DB_PASS');
$banco = getenv('DB_NAME');

try {
   $conexao = new PDO("mysql:host=$host;dbname=$banco", $usuario, $senha);
   $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   // Em caso de erro, interrompe o script e exibe a mensagem
   die("Erro na conexão: " . $e->getMessage());
}
