<?php
session_start();
require_once "db.php";

// Se já estiver logado, manda para index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE username=?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit;
    } else {
        $erro = "Usuário ou senha inválidos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - LabManager</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="header">
    <h1><i class="fas fa-sign-in-alt"></i> Login</h1>
    <a href="index.php" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
  </div>

  <div class="container">
    <?php if (!empty($erro)): ?>
      <div class="alert error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" class="card form-login">
      <h2><i class="fas fa-user-lock"></i> Acesso ao Sistema</h2>

      <label for="username">Usuário</label>
      <input type="text" id="username" name="username" placeholder="Digite seu usuário" required>

      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

      <button type="submit"><i class="fas fa-sign-in-alt"></i> Entrar</button>
    </form>

    <div style="text-align:center;margin-top:1rem;">
      <p>Ainda não tem conta?</p>
      <a href="registrar.php" class="btn btn-success"><i class="fas fa-user-plus"></i> Criar Conta</a>
    </div>
  </div>
</body>
</html>
