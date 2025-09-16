<?php
session_start();
require_once 'db.php';

// Redireciona para o login se o usuário não estiver logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'db.php';

// Lógica para lidar com o envio do formulário (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laboratorio_id = intval($_POST['laboratorio_id']);
    $turno = $_POST['turno'];
    $nome = $_POST['nome'];
    $matricula = $_POST['matricula'];

    // 1. Verificação de duplicatas usando o banco de dados
    $sql_check = "SELECT COUNT(*) FROM monitores WHERE laboratorio_id = ? AND turno = ?";
    $stmt_check = $conexao->prepare($sql_check);
    $stmt_check->execute([$laboratorio_id, $turno]);
    $existe = $stmt_check->fetchColumn() > 0;

    if (!$existe) {
        // 2. Comando SQL para inserir um novo monitor
        $sql_insert = "INSERT INTO monitores (nome, matricula, turno, laboratorio_id) VALUES (?, ?, ?, ?)";

        try {
            $stmt_insert = $conexao->prepare($sql_insert);
            $stmt_insert->execute([$nome, $matricula, $turno, $laboratorio_id]);
        } catch (PDOException $e) {
            echo "Erro ao cadastrar monitor: " . $e->getMessage();
        }
    }

    header('Location: monitores.php');
    exit;
}

// Lógica para carregar os dados das tabelas para exibição
$sql_monitores = "SELECT * FROM monitores ORDER BY nome";
$stmt_monitores = $conexao->prepare($sql_monitores);
$stmt_monitores->execute();
$monitores = $stmt_monitores->fetchAll(PDO::FETCH_ASSOC);

$sql_labs = "SELECT * FROM laboratorios ORDER BY nome";
$stmt_labs = $conexao->prepare($sql_labs);
$stmt_labs->execute();
$labs = $stmt_labs->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Monitores</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <i class="fas fa-user-astronaut"></i> Monitores de Laboratório
    </div>
    <nav>
        <a href="index.php"><i class="fas fa-chart-bar"></i> Dashboard</a>
        <a href="laboratorios.php"><i class="fas fa-flask"></i> Laboratórios</a>
        <a href="monitores.php"><i class="fas fa-user-astronaut"></i> Monitores</a>
        <a href="professores.php"><i class="fas fa-chalkboard-teacher"></i> Professores</a>
        <a href="aulas.php"><i class="fas fa-book"></i> Aulas</a>
        <a href="horarios.php"><i class="fas fa-calendar-alt"></i> Horários</a>
    </nav>
    <div class="container">
        <h2>Cadastrar Monitor</h2>
        <form method="POST">
            <p>Nome: <input type="text" name="nome" required></p>
            <p>Matrícula: <input type="text" name="matricula" required></p>
            <p>Turno:
                <select name="turno" required>
                    <option value="manhã">Manhã</option>
                    <option value="tarde">Tarde</option>
                    <option value="noite">Noite</option>
                </select>
            </p>
            <p>Laboratório:
                <select name="laboratorio_id" required>
                    <?php foreach ($labs as $lab): ?>
                        <option value="<?= $lab['id'] ?>"><?= htmlspecialchars($lab['nome']) ?>
                            (<?= htmlspecialchars($lab['numero']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><input type="submit" value="Cadastrar"></p>
        </form>
        <h3>Monitores Cadastrados</h3>
        <div class="dashboard-cards">
            <?php foreach ($monitores as $monitor): ?>
                <?php $lab = null;
                foreach ($labs as $l) {
                    if ($l['id'] == $monitor['laboratorio_id']) {
                        $lab = $l;
                        break;
                    }
                } ?>
                <div class="card">
                    <div class="lab-title"><i class="fas fa-user-astronaut"></i> <?= htmlspecialchars($monitor['nome']) ?>
                    </div>
                    <div class="lab-info"><i class="fas fa-id-badge"></i> Matrícula:
                        <?= htmlspecialchars($monitor['matricula']) ?></div>
                    <div class="lab-info"><i class="fas fa-clock"></i> Turno: <?= htmlspecialchars($monitor['turno']) ?>
                    </div>
                    <div class="lab-info"><i class="fas fa-flask"></i> Laboratório:
                        <?= $lab ? htmlspecialchars($lab['nome']) : '' ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>