<?php
session_start();

// Redireciona para o login se o usuário não for admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Gera um token CSRF se não existir um na sessão
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Gerador de Hash de Senha</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <i class="fas fa-key"></i> Gerador de Hash de Senha
    </div>
    <nav>
        <a href="index.php"><i class="fas fa-chart-bar"></i> Dashboard</a>
        <a href="laboratorios.php"><i class="fas fa-flask"></i> Laboratórios</a>
        <a href="monitores.php"><i class="fas fa-user-astronaut"></i> Monitores</a>
        <a href="professores.php"><i class="fas fa-chalkboard-teacher"></i> Professores</a>
        <a href="aulas.php"><i class="fas fa-book"></i> Aulas</a>
        <a href="horarios.php"><i class="fas fa-calendar-alt"></i> Horários</a>
        <a href="cadastrar_usuario.php"><i class="fas fa-user-plus"></i> Novo Usuário</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </nav>
    <div class="container">
        <h2>Gerar Hash de Senha</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <p>Digite a senha que você quer usar: <input type="text" name="password_text" required></p>
            <p><input type="submit" value="Gerar Hash"></p>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']))) {
            echo "<p style='color:red;'>Ação não autorizada: Token CSRF inválido.</p>";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password_text'])) {
            $password = $_POST['password_text'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            echo "<p><strong>Seu Hash:</strong></p>";
            echo "<textarea rows='4' cols='50' readonly>" . htmlspecialchars($hashed_password) . "</textarea>";
            echo "<p>Copie o hash acima e cole no campo 'password_hash' do seu usuário no phpMyAdmin.</p>";
        }
        ?>
    </div>
</body>

</html>