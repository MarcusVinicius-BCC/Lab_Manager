<<<<<<< HEAD
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

// === EXCLUIR ===
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $sql = "DELETE FROM monitores WHERE id=?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$id]);
    header("Location: monitores.php");
    exit;
}

// === SALVAR ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $laboratorio_id = intval($_POST['laboratorio_id']);
    $turno = $_POST['turno'];

    if (!empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $stmt = $conexao->prepare("UPDATE monitores SET nome=?, email=?, telefone=?, laboratorio_id=?, turno=? WHERE id=?");
        $stmt->execute([$nome, $email, $telefone, $laboratorio_id, $turno, $id]);
    } else {
        $stmt = $conexao->prepare("INSERT INTO monitores (nome, email, telefone, laboratorio_id, turno) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $telefone, $laboratorio_id, $turno]);
    }
    header("Location: monitores.php");
    exit;
}

// === EDITAR ===
$editando = false;
$monitor_edit = null;
if (isset($_GET['editar'])) {
    $editando = true;
    $stmt = $conexao->prepare("SELECT * FROM monitores WHERE id=?");
    $stmt->execute([intval($_GET['editar'])]);
    $monitor_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// === LISTAR ===
$sql = "SELECT m.*, l.nome AS lab_nome, l.numero 
        FROM monitores m
        JOIN laboratorios l ON m.laboratorio_id = l.id
        ORDER BY m.nome";
$monitores = $conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// === CARREGAR LABS PARA SELECT ===
$labs = $conexao->query("SELECT * FROM laboratorios ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);
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
    <h1><i class="fas fa-user-astronaut"></i> Monitores</h1>
    <a href="index.php" class="btn">Voltar</a>
  </div>
  <div class="container">
    <h2><?= $editando ? "Editar Monitor" : "Cadastrar Monitor" ?></h2>
    <form method="POST" class="card">
      <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $monitor_edit['id'] ?>">
      <?php endif; ?>

      <label>Nome:</label>
      <input type="text" name="nome" required value="<?= $monitor_edit['nome'] ?? '' ?>">

      <label>Email:</label>
      <input type="email" name="email" required value="<?= $monitor_edit['email'] ?? '' ?>">

      <label>Telefone:</label>
      <input type="text" name="telefone" value="<?= $monitor_edit['telefone'] ?? '' ?>">

      <label>Laboratório:</label>
      <select name="laboratorio_id" required>
        <?php foreach ($labs as $lab): 
          $sel = ($monitor_edit && $monitor_edit['laboratorio_id'] == $lab['id']) ? "selected" : ""; ?>
          <option value="<?= $lab['id'] ?>" <?= $sel ?>>
            <?= htmlspecialchars($lab['nome']) ?> (<?= $lab['numero'] ?>)
          </option>
        <?php endforeach; ?>
      </select>

      <label>Turno:</label>
      <select name="turno" required>
        <option value="manhã" <?= (($monitor_edit['turno'] ?? '') === 'manhã') ? "selected" : "" ?>>Manhã</option>
        <option value="tarde" <?= (($monitor_edit['turno'] ?? '') === 'tarde') ? "selected" : "" ?>>Tarde</option>
        <option value="noite" <?= (($monitor_edit['turno'] ?? '') === 'noite') ? "selected" : "" ?>>Noite</option>
      </select>

      <button type="submit" class="btn"><?= $editando ? "Atualizar" : "Cadastrar" ?></button>
    </form>

    <hr>
    <h2>Lista de Monitores</h2>
    <table>
      <tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Telefone</th>
        <th>Laboratório</th>
        <th>Turno</th>
        <th>Ações</th>
      </tr>
      <?php foreach ($monitores as $m): ?>
        <tr>
          <td><?= htmlspecialchars($m['nome']) ?></td>
          <td><?= htmlspecialchars($m['email']) ?></td>
          <td><?= htmlspecialchars($m['telefone'] ?? '') ?></td>
          <td><?= htmlspecialchars($m['lab_nome']) ?> (<?= $m['numero'] ?>)</td>
          <td><?= ucfirst($m['turno']) ?></td>
          <td>
            <a href="?editar=<?= $m['id'] ?>" class="btn">Editar</a>
            <a href="?excluir=<?= $m['id'] ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este monitor?')">Excluir</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
=======
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

>>>>>>> de238c3999db8aa6153cd4776a55a0ca7edf4e99
</html>