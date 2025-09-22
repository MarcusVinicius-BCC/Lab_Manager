<?php
session_start();
require_once "db.php";

date_default_timezone_set("America/Sao_Paulo");

// Identificar usuário
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $is_logged_in ? $_SESSION['user_role'] : null;

// === PROCESSAR SOLICITAÇÃO DE AGENDAMENTO (público) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendar']) && $user_role !== 'admin') {
    $nome       = trim($_POST['nome']);
    $matricula  = trim($_POST['matricula']);
    $email      = trim($_POST['email']);
    $telefone   = trim($_POST['telefone']);
    $dia_raw    = $_POST['dia'];
    $date_obj   = DateTime::createFromFormat('d/m/Y', $dia_raw);
    if ($date_obj) {
        $dia = $date_obj->format('Y-m-d');
    } else {
        $dia = null; // Or handle error appropriately
        $mensagem = ["tipo" => "error", "texto" => "Formato de data inválido. Use DD/MM/AAAA."];
    }
    $turno      = $_POST['turno'];
    $lab_id     = intval($_POST['laboratorio_id']);
    $motivo     = trim($_POST['motivo']);

    // If date is invalid, set error and stop processing
    if ($dia === null) {
        // $mensagem is already set in the date parsing block
        // No further processing needed for this request
    } else {
        // Check for existing approved appointments for this lab, day, and turno
        $sql_check_conflict = "SELECT COUNT(*) FROM agendamentos WHERE laboratorio_id = ? AND dia = ? AND turno = ? AND status IN ('aprovado', 'pendente')";
        $stmt_check_conflict = $conexao->prepare($sql_check_conflict);
        $stmt_check_conflict->execute([$lab_id, $dia, $turno]);
        $conflict_count = $stmt_check_conflict->fetchColumn();

        if ($conflict_count > 0) {
            $mensagem = ["tipo" => "error", "texto" => "Laboratório ocupado com evento para o dia e turno selecionados."];
        } else {
            // Original try-catch block for INSERT
            try {
                $sql = "INSERT INTO agendamentos (nome, matricula, email, telefone, laboratorio_id, dia, turno, motivo, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendente')";
                $stmt = $conexao->prepare($sql);
                $stmt->execute([$nome, $matricula, $email, $telefone, $lab_id, $dia, $turno, $motivo]);

                $mensagem = ["tipo" => "success", "texto" => "Solicitação enviada com sucesso!"];
            } catch (PDOException $e) {
                $mensagem = ["tipo" => "error", "texto" => "Erro ao solicitar: " . $e->getMessage()];
            }
        }
    }
}

// === ADMIN: APROVAR OU REJEITAR ===
if ($user_role === 'admin' && isset($_GET['acao'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $acao = $_GET['acao'] === 'aprovar' ? 'aprovado' : 'rejeitado';

    // Obter detalhes do agendamento antes de atualizar o status
    $sql_select_agendamento = "SELECT a.*, l.nome AS lab_nome, l.numero FROM agendamentos a JOIN laboratorios l ON a.laboratorio_id = l.id WHERE a.id = ?";
    $stmt_select_agendamento = $conexao->prepare($sql_select_agendamento);
    $stmt_select_agendamento->execute([$id]);
    $agendamento = $stmt_select_agendamento->fetch(PDO::FETCH_ASSOC);

    if ($agendamento) {
        $sql = "UPDATE agendamentos SET status=? WHERE id=?";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$acao, $id]);
    }

    header("Location: agendamentos.php");
    exit;
}

// === LISTAR SOLICITAÇÕES ===
$sql_list = "SELECT a.*, l.nome AS lab_nome, l.numero 
             FROM agendamentos a
             JOIN laboratorios l ON a.laboratorio_id = l.id
             ORDER BY a.dia, a.turno";
$stmt = $conexao->prepare($sql_list);
$stmt->execute();
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Carregar labs (para o formulário público)
$labs = $conexao->query("SELECT * FROM laboratorios ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Agendamentos - LabManager</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="header">
    <h1><i class="fas fa-id-card"></i> Agendamentos</h1>
    <a href="index.php" class="btn">Voltar</a>
  </div>

  <div class="container">
    <?php if (!empty($mensagem)): ?>
      <div class="alert <?= $mensagem['tipo'] ?>"><?= $mensagem['texto'] ?></div>
    <?php endif; ?>

    <?php if ($user_role !== 'admin'): ?>
      <!-- Formulário Público -->
      <h2>Solicitar Agendamento</h2>
<form method="POST" class="card">
  <label for="nome">Nome Completo</label>
  <input type="text" id="nome" name="nome" required>

  <label for="matricula">Matrícula</label>
  <input type="text" id="matricula" name="matricula" required>

  <label for="email">Email</label>
  <input type="email" id="email" name="email" required>

  <label for="telefone">Telefone</label>
  <input type="text" id="telefone" name="telefone">

  <label for="dia">Dia</label>
  <input type="text" id="dia" name="dia" required placeholder="DD/MM/AAAA">

  <label for="turno">Turno</label>
  <select id="turno" name="turno" required>
    <option value="">Selecione</option>
    <option value="manhã">Manhã</option>
    <option value="tarde">Tarde</option>
    <option value="noite">Noite</option>
  </select>

  <label for="laboratorio_id">Laboratório</label>
  <select id="laboratorio_id" name="laboratorio_id" required>
    <option value="">Selecione</option>
    <?php foreach ($labs as $lab): ?>
      <option value="<?= $lab['id'] ?>"><?= htmlspecialchars($lab['nome']) ?> (<?= $lab['numero'] ?>)</option>
    <?php endforeach; ?>
  </select>

  <label for="motivo">Motivo</label>
  <textarea id="motivo" name="motivo" required></textarea>

  <button type="submit" name="agendar"><i class="fas fa-paper-plane"></i> Enviar Solicitação</button>
</form>

    <?php endif; ?>

    <hr>
    <h2>Solicitações</h2>
    <div class="table-container">
    <table>
      <tr>
        <th>Nome</th>
        <th>Matrícula</th>
        <th>Email</th>
        <th>Telefone</th>
        <th>Laboratório</th>
        <th>Dia</th>
        <th>Turno</th>
        <th>Motivo</th>
        <th>Status</th>
        <?php if ($user_role === 'admin'): ?><th>Ações</th><?php endif; ?>
      </tr>
      <?php foreach ($agendamentos as $ag): ?>
        <tr>
          <td><?= htmlspecialchars($ag['nome']) ?></td>
          <td><?= htmlspecialchars($ag['matricula']) ?></td>
          <td><?= htmlspecialchars($ag['email']) ?></td>
          <td><?= htmlspecialchars($ag['telefone']) ?></td>
          <td><?= htmlspecialchars($ag['lab_nome']) ?> (<?= $ag['numero'] ?>)</td>
          <td><?= date("d/m/Y", strtotime($ag['dia'])) ?></td>
          <td><?= ucfirst($ag['turno']) ?></td>
          <td><?= htmlspecialchars($ag['motivo']) ?></td>
          <td>
            <?php if ($ag['status'] === 'pendente'): ?>
              <span class="alert error">Pendente</span>
            <?php elseif ($ag['status'] === 'aprovado'): ?>
              <span class="alert success">Aprovado</span>
            <?php else: ?>
              <span class="alert">Rejeitado</span>
            <?php endif; ?>
          </td>
          <?php if ($user_role === 'admin'): ?>
            <td>
              <a href="?acao=aprovar&id=<?= $ag['id'] ?>" class="btn btn-success">Aprovar</a>
              <a href="?acao=rejeitar&id=<?= $ag['id'] ?>" class="btn btn-danger">Rejeitar</a>
            </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </table>
    </div>
  </div>
</body>
</html>
