<?php
session_start();
require_once "db.php";

// ARQUIVAMENTO AUTOMÁTICO DE AULAS
// Roda uma vez para arquivar aulas cujo `data_fim` já passou.
try {
    $conexao->query("UPDATE aulas SET status = 'arquivada' WHERE data_fim < CURDATE() AND status = 'ativa'");
} catch (PDOException $e) {
    // Silenciosamente ignora o erro. Em um ambiente de produção, isso seria logado.
}

date_default_timezone_set("America/Sao_Paulo");
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $is_logged_in ? $_SESSION['user_role'] : null;

// Dia e turno atuais
$dia_atual = strtolower(date("l"));
$dia_semana_map = [
    "monday" => "segunda","tuesday" => "terça","wednesday" => "quarta",
    "thursday" => "quinta","friday" => "sexta","saturday" => "sábado","sunday" => "domingo"
];
$dia_semana_atual = $dia_semana_map[$dia_atual];
$hora_atual = date("H");
if ($hora_atual >= 7 && $hora_atual < 12) {
    $turno_atual = "manhã";
} elseif ($hora_atual >= 12 && $hora_atual < 18) {
    $turno_atual = "tarde";
} elseif (($hora_atual >= 18 && $hora_atual <= 23) || ($hora_atual >= 0 && $hora_atual < 7)) {
    $turno_atual = "noite";
} else {
    $turno_atual = "indefinido"; // Should not happen with correct ranges
}

// Carregar dados
$labs = $conexao->query("SELECT * FROM laboratorios ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conexao->prepare("SELECT a.laboratorio_id, a.disciplina, p.nome AS professor_nome
                           FROM aulas a JOIN professores p ON a.professor_id = p.id
                           WHERE a.dia_semana = ? AND a.turno = ? AND a.status = 'ativa'");
$stmt->execute([$dia_semana_atual, $turno_atual]);
$aulas_hoje = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch current approved appointments
$stmt_agendamentos = $conexao->prepare(
    "SELECT laboratorio_id, motivo FROM agendamentos WHERE dia = CURDATE() AND turno = ? AND status = 'aprovado'"
);
$stmt_agendamentos->execute([$turno_atual]);
$agendamentos_hoje = $stmt_agendamentos->fetchAll(PDO::FETCH_ASSOC);

$labs_ocupados = [];
foreach ($aulas_hoje as $aula) {
    $labs_ocupados[$aula["laboratorio_id"]] = [
        "tipo" => "aula",
        "disciplina" => $aula["disciplina"], 
        "professor_nome" => $aula["professor_nome"]
    ];
}

foreach ($agendamentos_hoje as $agendamento) {
    // Agendamentos take precedence or add to existing info
    $labs_ocupados[$agendamento["laboratorio_id"]] = [
        "tipo" => "agendamento",
        "motivo" => $agendamento["motivo"]
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Lab_Manager - Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <!-- Topo -->
  <div class="home-wrapper">
    <div class="logo-center">
      <i class="fas fa-microchip"></i> Lab_Manager
    </div>
    <div class="menu-cards">
      <a href="index.php" class="menu-card"><i class="fas fa-chart-bar"></i><h3>Dashboard</h3></a>
      <a href="agendamentos.php" class="menu-card"><i class="fas fa-id-card"></i><h3>Agendamento</h3></a>
      <?php if ($is_logged_in): ?>
        <?php if ($user_role === 'admin'): ?>
          <a href="laboratorios.php" class="menu-card"><i class="fas fa-flask"></i><h3>Laboratórios</h3></a>
          <a href="monitores.php" class="menu-card"><i class="fas fa-user-astronaut"></i><h3>Monitores</h3></a>
          <a href="professores.php" class="menu-card"><i class="fas fa-chalkboard-teacher"></i><h3>Professores</h3></a>
          <a href="aulas.php" class="menu-card"><i class="fas fa-book"></i><h3>Aulas</h3></a>
        <?php endif; ?>
        <a href="logout.php" class="menu-card"><i class="fas fa-sign-out-alt"></i><h3>Sair</h3></a>
      <?php else: ?>
        <a href="login.php" class="menu-card"><i class="fas fa-sign-in-alt"></i><h3>Login</h3></a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Dashboard -->
  <div class="section-dashboard container">
    <h2>Status dos Laboratórios (<?= ucfirst($dia_semana_atual) ?> - <?= ucfirst($turno_atual) ?>)</h2>
    <div class="dashboard-cards">
      <?php foreach ($labs as $lab):
        $status = isset($labs_ocupados[$lab['id']]) ? "ocupado" : "livre";
        $info = $labs_ocupados[$lab['id']] ?? null;
        $status_class = $status === "ocupado" ? "card-ocupado" : "card-livre";
      ?>
      <div class="card <?= $status_class ?>">
        <h3><i class="fas fa-flask"></i> <?= htmlspecialchars($lab['nome']) ?> (<?= $lab['numero'] ?>)</h3>
        <div class="lab-info"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($lab['localizacao']) ?></div>
        <div class="lab-info"><strong>Status:</strong>
            <?php if ($status === "ocupado"): ?>
                <span style="color: red; font-weight: bold;">Ocupado com Evento</span>
            <?php else: ?>
                <span style="color: green;">Livre</span>
            <?php endif; ?>
        </div>
        <?php if ($status === "ocupado"): ?>
            <?php if ($info['tipo'] === 'aula'): ?>
                <div class="lab-info"><i class="fas fa-book"></i> <?= htmlspecialchars($info['disciplina']) ?></div>
                <div class="lab-info"><i class="fas fa-chalkboard-teacher"></i> <?= htmlspecialchars($info['professor_nome']) ?></div>
            <?php elseif ($info['tipo'] === 'agendamento'): ?>
                <div class="lab-info"><i class="fas fa-calendar-alt"></i> <?= htmlspecialchars($info['motivo']) ?></div>
            <?php endif; ?>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</body>
</html>
