<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
require_once "db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

// === EXCLUIR ===
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $sql = "DELETE FROM laboratorios WHERE id=?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$id]);
    header("Location: laboratorios.php");
    exit;
}

// === SALVAR NOVO OU EDITAR ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $numero = trim($_POST['numero']);
    $localizacao = trim($_POST['localizacao']);
    $capacidade = intval($_POST['capacidade']);
    $projetor = isset($_POST['projetor']) ? 1 : 0;

    if (!empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $sql = "UPDATE laboratorios SET nome=?, numero=?, localizacao=?, capacidade=?, projetor=? WHERE id=?";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$nome, $numero, $localizacao, $capacidade, $projetor, $id]);
    } else {
        $sql = "INSERT INTO laboratorios (nome, numero, localizacao, capacidade, projetor) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$nome, $numero, $localizacao, $capacidade, $projetor]);
    }
    header("Location: laboratorios.php");
    exit;
}

// === CARREGAR PARA EDIÇÃO ===
$editando = false;
$lab_edit = null;
if (isset($_GET['editar'])) {
    $editando = true;
    $id = intval($_GET['editar']);
    $stmt = $conexao->prepare("SELECT * FROM laboratorios WHERE id=?");
    $stmt->execute([$id]);
    $lab_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get current date and time for status check
$current_date = date('Y-m-d');
$current_hour = date('H');

$current_turno = '';
if ($current_hour >= 6 && $current_hour < 12) {
    $current_turno = 'manhã';
} elseif ($current_hour >= 12 && $current_hour < 18) {
    $current_turno = 'tarde';
} elseif (($current_hour >= 18 && $current_hour <= 23) || ($current_hour >= 0 && $current_hour < 6)) {
    $current_turno = 'noite';
}

// === LISTAR ===
$labs = $conexao->query("SELECT * FROM laboratorios ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Laboratórios</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="header">
    <h1><i class="fas fa-flask"></i> Laboratórios</h1>
    <a href="index.php" class="btn">Voltar</a>
  </div>
  <div class="container">
    <h2><?= $editando ? "Editar Laboratório" : "Cadastrar Laboratório" ?></h2>
    <form method="POST" class="card">
      <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $lab_edit['id'] ?>">
      <?php endif; ?>

      <label>Nome:</label>
      <input type="text" name="nome" required value="<?= $lab_edit['nome'] ?? '' ?>">

      <label>Número:</label>
      <input type="text" name="numero" required value="<?= $lab_edit['numero'] ?? '' ?>">

      <label>Localização:</label>
      <input type="text" name="localizacao" value="<?= $lab_edit['localizacao'] ?? '' ?>">

      <label>Capacidade:</label>
      <input type="number" name="capacidade" required value="<?= $lab_edit['capacidade'] ?? '' ?>">

      <label class="form-group-checkbox"><input type="checkbox" name="projetor" <?= !empty($lab_edit['projetor']) ? 'checked' : '' ?>> Possui Projetor</label>

      <button type="submit" class="btn"><?= $editando ? "Atualizar" : "Cadastrar" ?></button>
    </form>

    <hr>
    <h2>Lista de Laboratórios</h2>
    <p><strong>Data Atual:</strong> <?= $current_date ?></p>
    <p><strong>Hora Atual:</strong> <?= $current_hour ?>h</p>
    <p><strong>Turno Atual:</strong> <?= $current_turno ?></p>
    <div class="table-container">
      <table>
        <tr>
          <th>Nome</th>
          <th>Número</th>
          <th>Localização</th>
          <th>Capacidade</th>
          <th>Projetor</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
        <?php foreach ($labs as $lab):
            $is_occupied = false;
            if (!empty($current_turno)) {
                $stmt_check_agendamento = $conexao->prepare(
                    "SELECT COUNT(*) FROM agendamentos WHERE laboratorio_id = ? AND dia = ? AND turno = ? AND status = 'aprovado'"
                );
                $stmt_check_agendamento->execute([$lab['id'], $current_date, $current_turno]);
                $is_occupied = ($stmt_check_agendamento->fetchColumn() > 0);
            }
        ?>
          <tr>
            <td><?= htmlspecialchars($lab['nome']) ?></td>
            <td><?= htmlspecialchars($lab['numero']) ?></td>
            <td><?= htmlspecialchars($lab['localizacao'] ?? '') ?></td>
            <td><?= htmlspecialchars($lab['capacidade']) ?></td>
            <td><?= $lab['projetor'] ? "Sim" : "Não" ?></td>
            <td>
                <?php if ($is_occupied): ?>
                    <span style="color: red; font-weight: bold;">Ocupado com Evento</span>
                <?php else: ?>
                    <span style="color: green;">Disponível</span>
                <?php endif; ?>
            </td>
            <td>
              <a href="?editar=<?= $lab['id'] ?>" class="btn">Editar</a>
              <a href="?excluir=<?= $lab['id'] ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este laboratório?')">Excluir</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</body>
</html>