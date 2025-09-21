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
</html>

>>>>>>> de238c3999db8aa6153cd4776a55a0ca7edf4e99
</html>