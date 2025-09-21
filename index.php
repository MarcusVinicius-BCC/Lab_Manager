<?php
session_start();
require_once "db.php";

date_default_timezone_set("America/Sao_Paulo");

// ARQUIVAMENTO AUTOMÁTICO DE AULAS
// Roda uma vez para arquivar aulas cujo `data_fim` já passou.
try {
    $conexao->query("UPDATE aulas SET status = 'arquivada' WHERE data_fim < CURDATE() AND status = 'ativa'");
} catch (PDOException $e) {
    // Silenciosamente ignora o erro. Em um ambiente de produção, isso seria logado.
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = $is_logged_in ? $_SESSION['user_role'] : null;

// Carregar dados do banco de dados para o calendário
$labs = $conexao->query("SELECT * FROM laboratorios ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);
$monitores = $conexao->query("SELECT * FROM monitores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$professores = $conexao->query("SELECT * FROM professores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$aulas = $conexao->query("SELECT * FROM aulas WHERE status = 'ativa' ORDER BY dia_semana, turno")->fetchAll(PDO::FETCH_ASSOC);

$turnos = ['manhã','tarde','noite'];
$dias_semana_map = [
    "segunda" => 1, "terça" => 2, "quarta" => 3, "quinta" => 4, "sexta" => 5, "sábado" => 6, "domingo" => 0
];
$dias_semana_nomes = [
    0 => "Domingo", 1 => "Segunda", 2 => "Terça", 3 => "Quarta", 4 => "Quinta", 5 => "Sexta", 6 => "Sábado"
];

// Preparar dados das aulas e monitores para o calendário
$calendar_events = [];

// Adicionar eventos de aulas
foreach ($aulas as $aula) {
    $lab_info = null;
    foreach ($labs as $lab) {
        if ($lab['id'] == $aula['laboratorio_id']) {
            $lab_info = $lab;
            break;
        }
    }
    $prof_info = null;
    foreach ($professores as $prof) {
        if ($prof['id'] == $aula['professor_id']) {
            $prof_info = $prof;
            break;
        }
    }

    $event_title = htmlspecialchars($aula['disciplina']);
    $event_description = "";
    if ($prof_info) {
        $event_description .= "Prof: " . htmlspecialchars($prof_info['nome']) . "<br>";
    }
    if ($lab_info) {
        $event_description .= "Lab: " . htmlspecialchars($lab_info['nome']) . " (" . htmlspecialchars($lab_info['numero']) . ")<br>";
    }
    $event_description .= "Turno: " . ucfirst($aula['turno']);

    if (!empty($aula['data_inicio']) && !empty($aula['data_fim'])) {
        $start_date = new DateTime($aula['data_inicio']);
        $end_date = new DateTime($aula['data_fim']);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start_date, $interval, $end_date->modify('+1 day'));

        foreach ($period as $date) {
            $day_of_week_num = (int)$date->format('w');
            $dia_semana_aula_num = $dias_semana_map[$aula['dia_semana']];

            if ($day_of_week_num == $dia_semana_aula_num) {
                $calendar_events[] = [
                    'title' => $event_title,
                    'description' => $event_description,
                    'date' => $date->format('Y-m-d'),
                    'type' => 'aula'
                ];
            }
        }
    }
}

// Adicionar eventos de monitores
foreach ($monitores as $monitor) {
    $lab_info = null;
    foreach ($labs as $lab) {
        if ($lab['id'] == $monitor['laboratorio_id']) {
            $lab_info = $lab;
            break;
        }
    }

    $event_title = "Monitor: " . htmlspecialchars($monitor['nome']);
    $event_description = "";
    if ($lab_info) {
        $event_description .= "Lab: " . htmlspecialchars($lab_info['nome']) . " (" . htmlspecialchars($lab_info['numero']) . ")<br>";
    }
    $event_description .= "Turno: " . ucfirst($monitor['turno']);

    // Monitores são considerados presentes durante todo o período de aulas ativas
    // Para simplificar, vamos adicioná-los a todos os dias da semana para o turno deles
    // Isso pode ser ajustado se houver datas específicas para monitores
    foreach ($dias_semana_map as $dia_semana_str => $dia_semana_num) {
        // Para cada dia do mês atual e próximo, adicione o monitor
        // Isso é uma simplificação, idealmente monitores teriam datas de início/fim
        $current_month_date = new DateTime(date('Y-m-01'));
        $next_month_date = new DateTime(date('Y-m-01', strtotime('+1 month')));

        $interval = new DateInterval('P1D');
        $period_current_month = new DatePeriod($current_month_date, $interval, $current_month_date->modify('+1 month'));
        $period_next_month = new DatePeriod($next_month_date, $interval, $next_month_date->modify('+1 month'));

        foreach (array_merge(iterator_to_array($period_current_month), iterator_to_array($period_next_month)) as $date) {
            if ((int)$date->format('w') == $dia_semana_num && $monitor['turno'] == $monitor['turno']) { // Check if day of week matches and turn matches
                $calendar_events[] = [
                    'title' => $event_title,
                    'description' => $event_description,
                    'date' => $date->format('Y-m-d'),
                    'type' => 'monitor'
                ];
            }
        }
    }
}

$calendar_events_json = json_encode($calendar_events);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Lab_Manager - Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
        /* Estilos para o calendário */
        .calendar-container {
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .calendar-header h2 {
            margin: 0;
            font-size: 1.8em;
            color: #333;
        }
        .calendar-header button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        .calendar-header button:hover {
            background-color: #0056b3;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        .calendar-day-header {
            font-weight: bold;
            text-align: center;
            padding: 10px 5px;
            background-color: #f0f0f0;
            border-radius: 4px;
        }
        .calendar-day {
            border: 1px solid #eee;
            padding: 10px;
            min-height: 100px;
            border-radius: 4px;
            background-color: #fdfdfd;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .calendar-day.today {
            background-color: #e6f7ff;
            border-color: #007bff;
        }
        .calendar-day.empty {
            background-color: #f9f9f9;
        }
        .day-number {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
            color: #555;
        }
        .event {
            background-color: #d4edda;
            border: 1px solid #28a745;
            color: #155724;
            padding: 5px;
            border-radius: 3px;
            margin-bottom: 5px;
            width: 100%;
            box-sizing: border-box;
            font-size: 0.85em;
            cursor: pointer;
            position: relative;
        }
        .event-details {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            z-index: 10;
            width: 200px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            left: 0;
            top: 100%; /* Position below the event */
            margin-top: 5px;
        }
        .event:hover .event-details {
            display: block;
        }
        .event-details p {
            margin: 0 0 5px 0;
            font-size: 0.9em;
            color: #333;
        }
    </style>
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
          <a href="horarios.php" class="menu-card"><i class="fas fa-calendar-alt"></i><h3>Horários</h3></a>
        <?php endif; ?>
        <a href="logout.php" class="menu-card"><i class="fas fa-sign-out-alt"></i><h3>Sair</h3></a>
      <?php else: ?>
        <a href="login.php" class="menu-card"><i class="fas fa-sign-in-alt"></i><h3>Login</h3></a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Dashboard - Calendário -->
  <div class="section-dashboard container">
    <h2>Calendário de Aulas e Monitores</h2>
    <div class="calendar-container">
        <div class="calendar-header">
            <button id="prevMonth">Mês Anterior</button>
            <h2 id="currentMonthYear"></h2>
            <button id="nextMonth">Próximo Mês</button>
        </div>
        <div class="calendar-grid" id="calendarGrid">
            <!-- Headers for days of the week -->
            <div class="calendar-day-header">Dom</div>
            <div class="calendar-day-header">Seg</div>
            <div class="calendar-day-header">Ter</div>
            <div class="calendar-day-header">Qua</div>
            <div class="calendar-day-header">Qui</div>
            <div class="calendar-day-header">Sex</div>
            <div class="calendar-day-header">Sáb</div>
            <!-- Calendar days will be inserted here by JavaScript -->
        </div>
    </div>
  </div>

    <script>
        const allEvents = <?= $calendar_events_json ?>;
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();

        function renderCalendar() {
            const calendarGrid = document.getElementById('calendarGrid');
            const currentMonthYear = document.getElementById('currentMonthYear');
            calendarGrid.innerHTML = ''; // Clear previous days

            // Add day headers again (they were cleared)
            const dayNames = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
            dayNames.forEach(day => {
                const header = document.createElement('div');
                header.classList.add('calendar-day-header');
                header.textContent = day;
                calendarGrid.appendChild(header);
            });

            const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const startingDay = firstDayOfMonth.getDay(); // 0 for Sunday, 1 for Monday, etc.

            currentMonthYear.textContent = new Date(currentYear, currentMonth).toLocaleString('pt-br', { month: 'long', year: 'numeric' });

            // Fill leading empty days
            for (let i = 0; i < startingDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.classList.add('calendar-day', 'empty');
                calendarGrid.appendChild(emptyDay);
            }

            // Fill days with events
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(currentYear, currentMonth, day);
                const dateString = date.toISOString().split('T')[0]; // YYYY-MM-DD

                const dayElement = document.createElement('div');
                dayElement.classList.add('calendar-day');
                if (date.toDateString() === new Date().toDateString()) {
                    dayElement.classList.add('today');
                }

                const dayNumber = document.createElement('div');
                dayNumber.classList.add('day-number');
                dayNumber.textContent = day;
                dayElement.appendChild(dayNumber);

                // Add events for this day
                allEvents.filter(event => event.date === dateString).forEach(event => {
                    const eventElement = document.createElement('div');
                    eventElement.classList.add('event');
                    if (event.type === 'aula') {
                        eventElement.classList.add('event-aula');
                    } else if (event.type === 'monitor') {
                        eventElement.classList.add('event-monitor');
                    }
                    eventElement.innerHTML = `<strong>${event.title}</strong>`;

                    const eventDetails = document.createElement('div');
                    eventDetails.classList.add('event-details');
                    eventDetails.innerHTML = `<p>${event.description}</p>`;
                    eventElement.appendChild(eventDetails);

                    dayElement.appendChild(eventElement);
                });

                calendarGrid.appendChild(dayElement);
            }
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });

        // Initial render
        renderCalendar();
    </script>
</body>
</html>