<?php
session_start();
require_once 'db.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe e se a senha está correta
    if ($user && password_verify($password, $user['password_hash'])) {
        // Login bem-sucedido: armazena informações na sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: index.php'); // Redireciona para a página principal
        exit;
    } else {
        $erro = "Usuário ou senha incorretos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <i class="fas fa-lock"></i> Acesso Restrito
    </div>
    <div class="container">
        <h2>Acessar o Sistema</h2>
        <form method="POST">
            <p>Usuário: <input type="text" name="username" required></p>
            <p>Senha: <input type="password" name="password" required></p>
            <p><input type="submit" value="Entrar"></p>
        </form>
        <p style="text-align: center; margin-top: 20px;">
    <a href="index.php">
        <i class="fas fa-arrow-left"></i> Voltar para o status dos laboratórios
    </a>
</p>
        <?php if ($erro): ?>
            <p style="color:red; text-align:center;"><?= $erro ?></p>
        <?php endif; ?>
    </div>
</body>

</html>