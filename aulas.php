<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

// === EXCLUIR ===
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $sql = "DELETE FROM aulas WHERE id=?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$id]);
    header("Location: aulas.php");
    exit;
}

// === SALVAR ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $disciplina = trim($_POST['disciplina']);
    $professor_id = intval($_POST['professor_id']);
    $laboratorio_id = intval($_POST['laboratorio_id']);
    $dia_semana = $_POST['dia_semana'];
    $turno = $_POST['turno'];
    $semestre = trim($_POST['semestre']);
    $data_inicio = !empty($_POST['data_inicio']) ? $_POST['data_inicio'] : null;
    $data_fim = !empty($_POST['data_fim']) ? $_POST['data_fim'] : null;
    $status = $_POST['status'];

    $params = [$disciplina, $professor_id, $laboratorio_id, $dia_semana, $turno, $semestre, $data_inicio, $data_fim, $status];

    if (!empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $sql = "UPDATE aulas SET disciplina=?, professor_id=?, laboratorio_id=?, dia_semana=?, turno=?, semestre=?, data_inicio=?, data_fim=?, status=? WHERE id=?";
        $params[] = $id;
        $stmt = $conexao->prepare($sql);
        $stmt->execute($params);
    } else {
        $sql = "INSERT INTO aulas (disciplina, professor_id, laboratorio_id, dia_semana, turno, semestre, data_inicio, data_fim, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute($params);
    }
    header("Location: aulas.php");
    exit;
}

// === EDITAR ===
$editando = false;
$aula_edit = null;
if (isset($_GET['editar'])) {
    $editando = true;
    $stmt = $conexao->prepare("SELECT * FROM aulas WHERE id=?");
    $stmt->execute([intval($_GET['editar'])]);
    $aula_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// === LISTAR ===
$sql = "SELECT a.*, p.nome AS professor_nome, l.nome AS lab_nome, l.numero
        FROM aulas a
        LEFT JOIN professores p ON a.professor_id = p.id
        LEFT JOIN laboratorios l ON a.laboratorio_id = l.id
        WHERE a.status = 'ativa'
        ORDER BY a.dia_semana, a.turno";
$aulas = $conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// === CARREGAR PROFESSORES E LABS ===
$profs = $conexao->query("SELECT * FROM professores WHERE status = 'ativo' ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$labs = $conexao->query("SELECT * FROM laboratorios ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);
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
    <div style="display: flex; align-items: center; gap: 20px;">
        <a href="index.php"><img src="img/logo.png" alt="Logo" style="height: 50px;"></a>
        <h1 style="margin: 0;">Aulas Ativas</h1>
    </div>
    <a href="index.php" class="btn">Voltar</a>
  </div>
  <div class="container">
    <h2><?= $editando ? "Editar Aula" : "Cadastrar Aula" ?></h2>
    <form method="POST" class="card">
      <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $aula_edit['id'] ?>">
      <?php endif; ?>

      <label>Disciplina:</label>
      <input type="text" name="disciplina" required value="<?= $aula_edit['disciplina'] ?? '' ?>">

      <label>Semestre (ex: 2025.1):</label>
      <input type="text" name="semestre" value="<?= $aula_edit['semestre'] ?? '' ?>">

      <label>Data de Início:</label>
      <input type="date" name="data_inicio" value="<?= $aula_edit['data_inicio'] ?? '' ?>">

      <label>Data de Fim:</label>
      <input type="date" name="data_fim" value="<?= $aula_edit['data_fim'] ?? '' ?>">

      <label>Professor:</label>
      <select name="professor_id" required>
        <option value="">Selecione...</option>
        <?php foreach ($profs as $p): 
          $sel = ($aula_edit && $aula_edit['professor_id'] == $p['id']) ? "selected" : ""; ?>
          <option value="<?= $p['id'] ?>" <?= $sel ?>><?= htmlspecialchars($p['nome']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Laboratório:</label>
      <select name="laboratorio_id" required>
        <option value="">Selecione...</option>
        <?php foreach ($labs as $lab): 
          $sel = ($aula_edit && $aula_edit['laboratorio_id'] == $lab['id']) ? "selected" : ""; ?>
          <option value="<?= $lab['id'] ?>" <?= $sel ?>><?= htmlspecialchars($lab['nome']) ?> (<?= $lab['numero'] ?>)</option>
        <?php endforeach; ?>
      </select>

      <label>Dia da Semana:</label>
      <select name="dia_semana" required>
        <option value="">Selecione...</option>
        <?php 
        $dias = ["segunda","terça","quarta","quinta","sexta","sábado"];
        foreach ($dias as $d): 
          $sel = (($aula_edit['dia_semana'] ?? '') === $d) ? "selected" : ""; ?>
          <option value="<?= $d ?>" <?= $sel ?>><?= ucfirst($d) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Turno:</label>
      <select name="turno" required>
        <option value="">Selecione...</option>
        <option value="manhã" <?= (($aula_edit['turno'] ?? '') === 'manhã') ? "selected" : "" ?>>Manhã</option>
        <option value="tarde" <?= (($aula_edit['turno'] ?? '') === 'tarde') ? "selected" : "" ?>>Tarde</option>
        <option value="noite" <?= (($aula_edit['turno'] ?? '') === 'noite') ? "selected" : "" ?>>Noite</option>
      </select>

      <label>Status:</label>
      <select name="status" required>
        <option value="ativa" <?= (($aula_edit['status'] ?? 'ativa') === 'ativa') ? "selected" : "" ?>>Ativa</option>
        <option value="arquivada" <?= (($aula_edit['status'] ?? '') === 'arquivada') ? "selected" : "" ?>>Arquivada</option>
      </select>

      <button type="submit" class="btn"><?= $editando ? "Atualizar" : "Cadastrar" ?></button>
    </form>

    <hr>
    <h2>Lista de Aulas Ativas</h2>
    <div class="table-container">
        <table>
          <tr>
            <th>Disciplina</th>
            <th>Semestre</th>
            <th>Professor</th>
            <th>Laboratório</th>
            <th>Dia</th>
            <th>Turno</th>
            <th>Fim</th>
            <th>Ações</th>
          </tr>
          <?php foreach ($aulas as $a): ?>
            <tr>
              <td><?= htmlspecialchars($a['disciplina']) ?></td>
              <td><?= htmlspecialchars($a['semestre']) ?></td>
              <td><?= htmlspecialchars($a['professor_nome'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($a['lab_nome'] ?? 'N/A') ?> (<?= $a['numero'] ?? '' ?>)</td>
              <td><?= ucfirst($a['dia_semana']) ?></td>
              <td><?= ucfirst($a['turno']) ?></td>
              <td><?= !empty($a['data_fim']) ? date('d/m/Y', strtotime($a['data_fim'])) : '' ?></td>
              <td>
                <a href="?editar=<?= $a['id'] ?>" class="btn">Editar</a> 
                <a href="?excluir=<?= $a['id'] ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja EXCLUIR PERMANENTEMENTE esta aula? Esta ação não pode ser desfeita.')">Excluir</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
    </div>
  </div>
</body>
</html>
