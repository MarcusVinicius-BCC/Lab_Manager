<?php
require_once "db.php";

// --- LÓGICA DO CALENDÁRIO ---

// Pega o ID do laboratório da URL
$lab_id = isset($_GET['lab_id']) ? intval($_GET['lab_id']) : null;
$lab_selecionado = null;

// Pega o mês e ano da URL, ou usa o mês e ano atuais
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('m');
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : date('Y');

$eventos_mes = [];

if ($lab_id) {
    // Carrega os dados do laboratório selecionado
    $stmt = $conexao->prepare("SELECT * FROM laboratorios WHERE id = ?");
    $stmt->execute([$lab_id]);
    $lab_selecionado = $stmt->fetch(PDO::FETCH_ASSOC);

    // --- BUSCA DE EVENTOS ---
    $primeiro_dia_mes = new DateTime("$ano-$mes-01");
    $ultimo_dia_mes = new DateTime($primeiro_dia_mes->format('Y-m-t'));

    // 1. Buscar agendamentos aprovados para o mês
    $sql_agendamentos = "SELECT dia, turno, motivo FROM agendamentos WHERE laboratorio_id = ? AND status = 'aprovado' AND dia BETWEEN ? AND ?";
    $stmt_agendamentos = $conexao->prepare($sql_agendamentos);
    $stmt_agendamentos->execute([$lab_id, $primeiro_dia_mes->format('Y-m-d'), $ultimo_dia_mes->format('Y-m-d')]);
    $agendamentos = $stmt_agendamentos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($agendamentos as $ag) {
        $dia = (new DateTime($ag['dia']))->format('j'); // Dia do mês (1-31)
        $eventos_mes[$dia][$ag['turno']] = "<span class='evento agendamento'>" . htmlspecialchars($ag['motivo']) . "</span>";
    }

    // 2. Buscar aulas ativas que ocorrem no mês
    $dias_semana_map = ['segunda' => 1, 'terça' => 2, 'quarta' => 3, 'quinta' => 4, 'sexta' => 5, 'sábado' => 6, 'domingo' => 7];
    $sql_aulas = "SELECT dia_semana, turno, disciplina FROM aulas WHERE laboratorio_id = ? AND status = 'ativa'";
    $stmt_aulas = $conexao->prepare($sql_aulas);
    $stmt_aulas->execute([$lab_id]);
    $aulas = $stmt_aulas->fetchAll(PDO::FETCH_ASSOC);

    // Itera por cada dia do mês para verificar se há uma aula recorrente
    $dia_corrente = clone $primeiro_dia_mes;
    while ($dia_corrente <= $ultimo_dia_mes) {
        $dia_semana_num = $dia_corrente->format('N'); // 1 (Segunda) a 7 (Domingo)
        $dia_do_mes = $dia_corrente->format('j');

        foreach ($aulas as $aula) {
            if ($dias_semana_map[$aula['dia_semana']] == $dia_semana_num) {
                // Adiciona o evento de aula se não houver um agendamento no mesmo turno
                if (!isset($eventos_mes[$dia_do_mes][$aula['turno']])) {
                    $eventos_mes[$dia_do_mes][$aula['turno']] = "<span class='evento aula'>" . htmlspecialchars($aula['disciplina']) . "</span>";
                }
            }
        }
        $dia_corrente->modify('+1 day');
    }
}

// Carrega todos os laboratórios para os cards
$labs = $conexao->query("SELECT * FROM laboratorios ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);

// --- FUNÇÃO PARA GERAR O CALENDÁRIO ---
function gerar_calendario($mes, $ano, $eventos, $lab_id) {
    $primeiro_dia = new DateTime("$ano-$mes-01");
    $dias_no_mes = (int)$primeiro_dia->format('t');
    $dia_semana_inicio = (int)$primeiro_dia->format('N'); // 1 (Segunda) a 7 (Domingo)

    $nome_mes = utf8_encode(strftime('%B', $primeiro_dia->getTimestamp()));

    // Navegação
    $mes_anterior = $mes == 1 ? 12 : $mes - 1;
    $ano_anterior = $mes == 1 ? $ano - 1 : $ano;
    $mes_seguinte = $mes == 12 ? 1 : $mes + 1;
    $ano_seguinte = $mes == 12 ? $ano + 1 : $ano;

    $html = "<div class='calendario-header'>";
    $html .= "<a href='?lab_id=$lab_id&mes=$mes_anterior&ano=$ano_anterior' class='btn'>&laquo; Anterior</a>";
    $html .= "<h2>" . ucfirst($nome_mes) . " de $ano</h2>";
    $html .= "<a href='?lab_id=$lab_id&mes=$mes_seguinte&ano=$ano_seguinte' class='btn'>Próximo &raquo;</a>";
    $html .= "</div>";

    $html .= "<table class='calendario-table'>";
    $html .= "<tr><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th><th>Dom</th></tr>";
    $html .= "<tr>";

    // Células vazias até o primeiro dia
    $html .= str_repeat('<td></td>', $dia_semana_inicio - 1);

    for ($dia = 1; $dia <= $dias_no_mes; $dia++) {
        $dia_semana_atual = ($dia_semana_inicio + $dia - 2) % 7 + 1;

        $html .= "<td class='dia'>";
        $html .= "<div class='dia-numero'>$dia</div>";
        $html .= "<div class='eventos'>";
        
        // Manhã
        $html .= "<div class='turno manhã'>";
        $html .= "<small>Manhã:</small> ";
        $html .= $eventos[$dia]['manhã'] ?? '<span class="livre">Livre</span>';
        $html .= "</div>";
        
        // Tarde
        $html .= "<div class='turno tarde'>";
        $html .= "<small>Tarde:</small> ";
        $html .= $eventos[$dia]['tarde'] ?? '<span class="livre">Livre</span>';
        $html .= "</div>";

        // Noite
        $html .= "<div class='turno noite'>";
        $html .= "<small>Noite:</small> ";
        $html .= $eventos[$dia]['noite'] ?? '<span class="livre">Livre</span>';
        $html .= "</div>";

        $html .= "</div></td>";

        if ($dia_semana_atual == 7 && $dia < $dias_no_mes) {
            $html .= "</tr><tr>";
        }
    }

    // Células vazias no final
    if ($dia_semana_atual != 7) {
        $html .= str_repeat('<td></td>', 7 - $dia_semana_atual);
    }

    $html .= "</tr></table>";
    return $html;
}

// Configura o locale para português
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Agenda dos Laboratórios</title>
  <link rel="stylesheet" href="css/style.css?v=1.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="header">
    <div style="display: flex; align-items: center; gap: 20px;">
        <a href="index.php"><img src="img/logo.png" alt="Logo" style="height: 50px;"></a>
        <h1 style="margin: 0;">Agenda</h1>
    </div>
    <a href="index.php" class="btn">Voltar</a>
  </div>

  <div class="container">
    <?php if (!$lab_selecionado): ?>
      <!-- Visualização Principal: Texto e Cards dos Laboratórios -->
      <div style="text-align: center; margin-bottom: 2rem;">
        <h2>Consulte a Agenda dos Laboratórios</h2>
        <p>A agenda do Laboratório tem a função de informar a disponibilidade do Laboratório durante o semestre. É muito importante que verifique a agenda no dia e horários que deseja antes de solicitar a sua reserva.
Após verificar a disponibilidade clique no Agendamento para realizar a sua reserva.</p>
        <p><em>Aqui você pode visualizar a ocupação de cada laboratório. Clique em um laboratório para ver sua agenda mensal detalhada.</em></p>
      </div>

      <div class="dashboard-cards">
        <?php foreach ($labs as $lab): ?>
          <a href="agenda.php?lab_id=<?= $lab['id'] ?>" class="card card-livre">
            <h3><i class="fas fa-desktop"></i> <?= htmlspecialchars($lab['nome']) ?> (<?= $lab['numero'] ?>)</h3>
          </a>
        <?php endforeach; ?>
      </div>

    <?php else: ?>
      <!-- Visualização da Agenda Mensal de um Laboratório -->
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 2rem;">
        <h2>Agenda Mensal - <?= htmlspecialchars($lab_selecionado['nome']) ?></h2>
        <a href="agenda.php" class="btn"><i class="fas fa-arrow-left"></i> Ver todos os laboratórios</a>
      </div>
      
      <div id="calendario">
        <?= gerar_calendario($mes, $ano, $eventos_mes, $lab_id) ?>
      </div>

    <?php endif; ?>
  </div>
</body>
</html>