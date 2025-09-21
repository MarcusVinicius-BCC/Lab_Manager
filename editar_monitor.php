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
    header('Location: monitores.php');
    exit;
}

// Lógica para ATUALIZAR o monitor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificação do token CSRF para o método POST (atualização)
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Ação não autorizada: Token CSRF inválido.');
    }

    $nome = $_POST['nome'];
    $matricula = $_POST['matricula'];
    $email = $_POST['email'];
    $turno = $_POST['turno'];
    $laboratorio_id = intval($_POST['laboratorio_id']);

    $sql = "UPDATE monitores SET nome = ?, matricula = ?, email = ?, turno = ?, laboratorio_id = ? WHERE id = ?";
    try {
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$nome, $matricula, $email, $turno, $laboratorio_id, $id]);
        header('Location: monitores.php');
        exit;
    } catch (PDOException $e) {
        die("Erro ao atualizar monitor: " . $e->getMessage());
    }
}

// Lógica para CARREGAR os dados para o formulário
$sql_select_monitor = "SELECT * FROM monitores WHERE id = ?";
$stmt_select_monitor = $conexao->prepare($sql_select_monitor);
$stmt_select_monitor->execute([$id]);
$monitor = $stmt_select_monitor->fetch(PDO::FETCH_ASSOC);

if (!$monitor) {
    header('Location: monitores.php');
    exit;
}

// Carrega a lista de laboratórios para o select
$sql_labs = "SELECT * FROM laboratorios ORDER BY nome";
$stmt_labs = $conexao->prepare($sql_labs);
$stmt_labs->execute();
$labs = $stmt_labs->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Monitor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <i class="fas fa-edit"></i> Editar Monitor
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
        <h2>Editar Monitor: <?= htmlspecialchars($monitor['nome']) ?></h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <p>Nome: <input type="text" name="nome" value="<?= htmlspecialchars($monitor['nome']) ?>" required></p>
            <p>Matrícula: <input type="text" name="matricula" value="<?= htmlspecialchars($monitor['matricula']) ?>" required></p>
            <p>Email: <input type="email" name="email" value="<?= htmlspecialchars($monitor['email']) ?>" required></p>
            <p>Turno:
                <select name="turno" required>
                    <option value="manhã" <?= $monitor['turno'] == 'manhã' ? 'selected' : '' ?>>Manhã</option>
                    <option value="tarde" <?= $monitor['turno'] == 'tarde' ? 'selected' : '' ?>>Tarde</option>
                    <option value="noite" <?= $monitor['turno'] == 'noite' ? 'selected' : '' ?>>Noite</option>
                </select>
            </p>
            <p>Laboratório:
                <select name="laboratorio_id" required>
                    <?php foreach ($labs as $lab): ?>
                        <option value="<?= $lab['id'] ?>" <?= $monitor['laboratorio_id'] == $lab['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lab['nome']) ?> (<?= htmlspecialchars($lab['numero']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <input type="submit" value="Salvar Alterações">
                <a href="monitores.php" style="text-decoration: none; margin-left: 10px;">Cancelar</a>
            </p>
        </form>
    </div>
</body>

</html>