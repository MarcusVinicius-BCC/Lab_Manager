<?php
function load_data($file) {
    $path = __DIR__ . "/data/" . $file;
    if (!file_exists($path)) return [];
    return json_decode(file_get_contents($path), true) ?: [];
}
$labs = load_data('laboratorios.json');
$monitores = load_data('monitores.json');
$aulas = load_data('aulas.json');
$professores = load_data('professores.json');
$turnos = ['manhã','tarde','noite'];
$dias = ['segunda','terça','quarta','quinta','sexta'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Horários dos Laboratórios</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="header">
        <i class="fas fa-calendar-alt"></i> Horários dos Laboratórios
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
        <h2>Horários dos Laboratórios</h2>
        <?php foreach ($labs as $lab): ?>
            <h3><i class="fas fa-flask"></i> <?= htmlspecialchars($lab['nome']) ?> (<?= htmlspecialchars($lab['numero']) ?>)</h3>
            <table>
                <tr>
                    <th>Dia</th>
                    <?php foreach ($turnos as $turno): ?>
                    <th><?= ucfirst($turno) ?></th>
                    <?php endforeach; ?>
                </tr>
                <?php foreach ($dias as $dia): ?>
                <tr>
                    <td><?= ucfirst($dia) ?></td>
                    <?php foreach ($turnos as $turno): ?>
                    <td>
                        <?php
                        $aula = null;
                        foreach ($aulas as $a) {
                            if ($a['laboratorio_id'] == $lab['id'] && $a['turno'] == $turno && $a['dia_semana'] == $dia) {
                                $aula = $a;
                                break;
                            }
                        }
                        if ($aula) {
                            $prof = null;
                            foreach ($professores as $p) { if ($p['id'] == $aula['professor_id']) { $prof = $p; break; } }
                            echo "<span style='font-weight:bold'><i class='fas fa-book'></i> " . htmlspecialchars($aula['disciplina']) . "</span><br><i class='fas fa-chalkboard-teacher'></i> " . ($prof ? htmlspecialchars($prof['nome']) : "") . "<br>";
                        }
                        $monitor = null;
                        foreach ($monitores as $m) {
                            if ($m['laboratorio_id'] == $lab['id'] && $m['turno'] == $turno) {
                                $monitor = $m;
                                break;
                            }
                        }
                        if ($monitor) {
                            echo "<i class='fas fa-user-astronaut'></i> Monitor: " . htmlspecialchars($monitor['nome']);
                        }
                        ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endforeach; ?>
    </div>
</body>
</html>
        <script>
        const today = new Date();
        </script>
    <script>
    // Função para gerar o calendário do mês atual
    function renderCalendar(events) {
        const calendarEl = document.getElementById('calendar');
        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        let html = '<table class="calendar-table"><tr>';
        const weekDays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        weekDays.forEach(d => html += `<th>${d}</th>`);
        html += '</tr><tr>';
        for (let i = 0; i < firstDay.getDay(); i++) html += '<td></td>';
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            let event = events[dateStr];
            html += `<td${event ? ' class="calendar-event"' : ''}><div>${day}</div>`;
            if (event) html += `<div class="event-name">${event}</div>`;
            html += '</td>';
            if ((firstDay.getDay() + day) % 7 === 0) html += '</tr><tr>';
        }
        html += '</tr></table>';
        calendarEl.innerHTML = html;
    }
</body>
</html>



