<?php
$host = 'localhost';
$usuario = 'root';
$senha = '123456';
$banco = 'lab_manager';

try {
   $conexao = new PDO("mysql:host=$host;dbname=$banco", $usuario, $senha);
   $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   // Em caso de erro, interrompe o script e exibe a mensagem
   die("Erro na conexão: " . $e->getMessage());
}
?>