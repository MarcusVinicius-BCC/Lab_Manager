<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

// === EXCLUIR ===
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    // A verificação de aulas foi removida, o DB agora usa ON DELETE SET NULL
    $sql = "DELETE FROM professores WHERE id=?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$id]);
    header("Location: professores.php");
    exit;
}

// === SALVAR ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $matricula = trim($_POST['matricula']);
    $telefone = trim($_POST['telefone']);
    $area_conhecimento = trim($_POST['area_conhecimento']);
    $status = $_POST['status'];

    if (!empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $sql = "UPDATE professores SET nome=?, email=?, matricula=?, telefone=?, area_conhecimento=?, status=? WHERE id=?";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$nome, $email, $matricula, $telefone, $area_conhecimento, $status, $id]);
    } else {
        $sql = "INSERT INTO professores (nome, email, matricula, telefone, area_conhecimento, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$nome, $email, $matricula, $telefone, $area_conhecimento, $status]);
    }
    header("Location: professores.php");
    exit;
}

// === EDITAR ===
$editando = false;
$prof_edit = null;
if (isset($_GET['editar'])) {
    $editando = true;
    $stmt = $conexao->prepare("SELECT * FROM professores WHERE id=?");
    $stmt->execute([intval($_GET['editar'])]);
    $prof_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// === LISTAR ===
$profs = $conexao->query("SELECT * FROM professores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Professores</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="header">
    <div style="display: flex; align-items: center; gap: 20px;">
        <a href="index.php"><img src="img/logo.png" alt="Logo" style="height: 50px;"></a>
        <h1 style="margin: 0;">Professores</h1>
    </div>
    <a href="index.php" class="btn">Voltar</a>
  </div>
  <div class="container">
    <h2><?= $editando ? "Editar Professor" : "Cadastrar Professor" ?></h2>
    <form method="POST" class="card">
      <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $prof_edit['id'] ?>">
      <?php endif; ?>

      <label>Nome:</label>
      <input type="text" name="nome" required value="<?= $prof_edit['nome'] ?? '' ?>">

      <label>Matrícula:</label>
      <input type="text" name="matricula" value="<?= $prof_edit['matricula'] ?? '' ?>">

      <label>Email:</label>
      <input type="email" name="email" required value="<?= $prof_edit['email'] ?? '' ?>">

      <label>Telefone:</label>
      <input type="text" name="telefone" value="<?= $prof_edit['telefone'] ?? '' ?>">

      <label>Área de Conhecimento:</label>
      <input type="text" name="area_conhecimento" value="<?= $prof_edit['area_conhecimento'] ?? '' ?>">

      <label>Status:</label>
      <select name="status" required>
        <option value="ativo" <?= (($prof_edit['status'] ?? 'ativo') === 'ativo') ? "selected" : "" ?>>Ativo</option>
        <option value="inativo" <?= (($prof_edit['status'] ?? '') === 'inativo') ? "selected" : "" ?>>Inativo</option>
      </select>

      <button type="submit" class="btn"><?= $editando ? "Atualizar" : "Cadastrar" ?></button>
    </form>

    <hr>
    <h2>Lista de Professores</h2>
    <div class="table-container">
        <table>
          <tr>
            <th>Nome</th>
            <th>Matrícula</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Área</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
          <?php foreach ($profs as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['nome']) ?></td>
              <td><?= htmlspecialchars($p['matricula'] ?? '') ?></td>
              <td><?= htmlspecialchars($p['email']) ?></td>
              <td><?= htmlspecialchars($p['telefone'] ?? '') ?></td>
              <td><?= htmlspecialchars($p['area_conhecimento'] ?? '') ?></td>
              <td>
                <span class="alert <?= $p['status'] === 'ativo' ? 'success' : '' ?>">
                  <?= ucfirst($p['status']) ?>
                </span>
              </td>
              <td>
                <a href="?editar=<?= $p['id'] ?>" class="btn">Editar</a>
                <a href="?excluir=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este professor?')">Excluir</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
    </div>
  </div>
</body>
</html>
