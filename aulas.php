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
    $professor_id = intval($_POST['professor_id']);
    $turno = $_POST['turno'];
    $dia_semana = $_POST['dia_semana'];
    $disciplina = $_POST['disciplina'];

    // 1. Verificação de conflito usando o banco de dados
    // A query verifica se já existe uma aula no mesmo lab, turno e dia.
    $sql_conflito = "SELECT COUNT(*) FROM aulas WHERE laboratorio_id = ? AND turno = ? AND dia_semana = ?";
    $stmt_conflito = $conexao->prepare($sql_conflito);
    $stmt_conflito->execute([$laboratorio_id, $turno, $dia_semana]);
    $conflito = $stmt_conflito->fetchColumn() > 0;

    if (!$conflito) {
        // 2. Comando SQL para inserir a nova aula
        $sql_insert = "INSERT INTO aulas (disciplina, professor_id, laboratorio_id, turno, dia_semana) VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt_insert = $conexao->prepare($sql_insert);
            $stmt_insert->execute([$disciplina, $professor_id, $laboratorio_id, $turno, $dia_semana]);
        } catch (PDOException $e) {
            echo "Erro ao cadastrar aula: " . $e->getMessage();
        }
    }

    // Redireciona para evitar reenvio do formulário
    //ader('Location: aulas.php');
    //it;
}

// Lógica para carregar os dados das tabelas para exibição
try {
    // Carrega dados da tabela laboratorios para o formulário
    $sql_labs = "SELECT * FROM laboratorios ORDER BY nome";
    $stmt_labs = $conexao->prepare($sql_labs);
    $stmt_labs->execute();
    $labs = $stmt_labs->fetchAll(PDO::FETCH_ASSOC);

    // Carrega dados da tabela professores para o formulário
    $sql_professores = "SELECT * FROM professores ORDER BY nome";
    $stmt_professores = $conexao->prepare($sql_professores);
    $stmt_professores->execute();
    $professores = $stmt_professores->fetchAll(PDO::FETCH_ASSOC);

    // Carrega todas as aulas para exibição
    $sql_aulas = "SELECT * FROM aulas ORDER BY dia_semana, turno";
    $stmt_aulas = $conexao->prepare($sql_aulas);
    $stmt_aulas->execute();
    $aulas = $stmt_aulas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Aulas</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <i class="fas fa-book"></i> Aulas Agendadas
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
        <h2>Agendar Aula</h2>
        <form method="POST">
            <p>Disciplina: <input type="text" name="disciplina" required></p>
            <p>Professor:
                <select name="professor_id" required>
                    <?php foreach ($professores as $prof): ?>
                        <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['nome']) ?> -
                            <?= htmlspecialchars($prof['disciplina']) ?>
                        </option>
                    <?php endforeach; ?>
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
            <p>Turno:
                <select name="turno" required>
                    <option value="manhã">Manhã</option>
                    <option value="tarde">Tarde</option>
                    <option value="noite">Noite</option>
                </select>
            </p>
            <p>Dia da Semana:
                <select name="dia_semana" required>
                    <option value="segunda">Segunda</option>
                    <option value="terça">Terça</option>
                    <option value="quarta">Quarta</option>
                    <option value="quinta">Quinta</option>
                    <option value="sexta">Sexta</option>
                </select>
            </p>
            <p><input type="submit" value="Agendar"></p>
        </form>
        <h3>Aulas Agendadas</h3>
        <div class="dashboard-cards">
            <?php foreach ($aulas as $aula): ?>
                <?php $prof = null;
                foreach ($professores as $p) {
                    if ($p['id'] == $aula['professor_id']) {
                        $prof = $p;
                        break;
                    }
                } ?>
                <?php $lab = null;
                foreach ($labs as $l) {
                    if ($l['id'] == $aula['laboratorio_id']) {
                        $lab = $l;
                        break;
                    }
                } ?>
                <div class="card">
                    <div class="lab-title"><i class="fas fa-book"></i> <?= htmlspecialchars($aula['disciplina']) ?></div>
                    <div class="lab-info"><i class="fas fa-chalkboard-teacher"></i> Professor:
                        <?= $prof ? htmlspecialchars($prof['nome']) : '' ?>
                    </div>
                    <div class="lab-info"><i class="fas fa-flask"></i> Laboratório:
                        <?= $lab ? htmlspecialchars($lab['nome']) : '' ?>
                    </div>
                    <div class="lab-info"><i class="fas fa-clock"></i> Turno: <?= htmlspecialchars($aula['turno']) ?></div>
                    <div class="lab-info"><i class="fas fa-calendar-alt"></i> Dia:
                        <?= htmlspecialchars($aula['dia_semana']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>