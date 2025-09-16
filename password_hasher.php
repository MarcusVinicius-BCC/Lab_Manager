<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerador de Hash de Senha</title>
</head>
<body>
    <h2>Gerar Hash de Senha</h2>
    <form method="POST">
        <p>Digite a senha que você quer usar: <input type="text" name="password_text" required></p>
        <p><input type="submit" value="Gerar Hash"></p>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password_text'])) {
        $password = $_POST['password_text'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        echo "<p><strong>Seu Hash:</strong></p>";
        echo "<textarea rows='4' cols='50' readonly>" . htmlspecialchars($hashed_password) . "</textarea>";
        echo "<p>Copie o hash acima e cole no campo 'password_hash' do seu usuário no phpMyAdmin.</p>";
    }
    ?>
</body>
</html>