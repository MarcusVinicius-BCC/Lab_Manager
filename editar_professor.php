<<<<<<< HEAD
<?php
session_start();
require_once 'db.php';

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

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: professores.php');
    exit;
}

// Lógica para ATUALIZAR o professor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificação do token CSRF para o método POST (atualização)
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Ação não autorizada: Token CSRF inválido.');
    }

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $matricula = $_POST['matricula'];

    $sql = "UPDATE professores SET nome = ?, email = ?, telefone = ?, matricula = ? WHERE id = ?";
    try {
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$nome, $email, $telefone, $matricula, $id]);
        header('Location: professores.php');
        exit;
    } catch (PDOException $e) {
        die("Erro ao atualizar professor: " . $e->getMessage());
    }
}

// Lógica para CARREGAR os dados do professor para o formulário
$sql_select = "SELECT * FROM professores WHERE id = ?";
$stmt_select = $conexao->prepare($sql_select);
$stmt_select->execute([$id]);
$prof = $stmt_select->fetch(PDO::FETCH_ASSOC);

if (!$prof) {
    header('Location: professores.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Professor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <i class="fas fa-edit"></i> Editar Professor
    </div>
    <nav>
        <a href="index.php"><i class="fas fa-chart-bar"></i> Dashboard</a>
        <a href="laboratorios.php"><i class="fas fa-flask"></i> Laboratórios</a>
        <a href="monitores.php"><i class="fas fa-user-astronaut"></i> Monitores</a>
        <a href="professores.php"><i class="fas fa-chalkboard-teacher"></i> Professores</a>
        <a href="aulas.php"><i class="fas fa-book"></i> Aulas</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </nav>
    <div class="container">
        <h2>Editar Professor: <?= htmlspecialchars($prof['nome']) ?></h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <p>Nome: <input type="text" name="nome" value="<?= htmlspecialchars($prof['nome']) ?>" required></p>
            <p>Email: <input type="email" name="email" value="<?= htmlspecialchars($prof['email']) ?>" required></p>
            <p>Telefone: <input type="text" name="telefone" value="<?= htmlspecialchars($prof['telefone']) ?>" required></p>
            <p>Matrícula: <input type="text" name="matricula" value="<?= htmlspecialchars($prof['matricula']) ?>" required></p>
            <p>
                <input type="submit" value="Salvar Alterações">
                <a href="professores.php" style="text-decoration: none; margin-left: 10px;">Cancelar</a>
            </p>
        </form>
    </div>
</body>

=======
<?php
session_start();
require_once 'db.php';

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

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: professores.php');
    exit;
}

// Lógica para ATUALIZAR o professor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificação do token CSRF para o método POST (atualização)
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Ação não autorizada: Token CSRF inválido.');
    }

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $matricula = $_POST['matricula'];

    $sql = "UPDATE professores SET nome = ?, email = ?, telefone = ?, matricula = ? WHERE id = ?";
    try {
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$nome, $email, $telefone, $matricula, $id]);
        header('Location: professores.php');
        exit;
    } catch (PDOException $e) {
        die("Erro ao atualizar professor: " . $e->getMessage());
    }
}

// Lógica para CARREGAR os dados do professor para o formulário
$sql_select = "SELECT * FROM professores WHERE id = ?";
$stmt_select = $conexao->prepare($sql_select);
$stmt_select->execute([$id]);
$prof = $stmt_select->fetch(PDO::FETCH_ASSOC);

if (!$prof) {
    header('Location: professores.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Professor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <i class="fas fa-edit"></i> Editar Professor
    </div>
    <nav>
        <a href="index.php"><i class="fas fa-chart-bar"></i> Dashboard</a>
        <a href="laboratorios.php"><i class="fas fa-flask"></i> Laboratórios</a>
        <a href="monitores.php"><i class="fas fa-user-astronaut"></i> Monitores</a>
        <a href="professores.php"><i class="fas fa-chalkboard-teacher"></i> Professores</a>
        <a href="aulas.php"><i class="fas fa-book"></i> Aulas</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </nav>
    <div class="container">
        <h2>Editar Professor: <?= htmlspecialchars($prof['nome']) ?></h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <p>Nome: <input type="text" name="nome" value="<?= htmlspecialchars($prof['nome']) ?>" required></p>
            <p>Email: <input type="email" name="email" value="<?= htmlspecialchars($prof['email']) ?>" required></p>
            <p>Telefone: <input type="text" name="telefone" value="<?= htmlspecialchars($prof['telefone']) ?>" required></p>
            <p>Matrícula: <input type="text" name="matricula" value="<?= htmlspecialchars($prof['matricula']) ?>" required></p>
            <p>
                <input type="submit" value="Salvar Alterações">
                <a href="professores.php" style="text-decoration: none; margin-left: 10px;">Cancelar</a>
            </p>
        </form>
    </div>
</body>

>>>>>>> a2a08d6b544b9fcd45055b686d5fac820a3fad02
</html>