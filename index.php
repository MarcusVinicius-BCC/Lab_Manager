<?php
session_start();
require_once 'db.php';

// Define o fuso horário para garantir que o horário esteja correto
date_default_timezone_set('America/Sao_Paulo');

// Lógica para determinar o dia e turno atuais
$dia_atual = strtolower(date('l')); // 'l' retorna o dia da semana em inglês
// Traduzindo para português
$dia_semana_map = [
    'monday' => 'segunda',
    'tuesday' => 'terça',
    'wednesday' => 'quarta',
    'thursday' => 'quinta',
    'friday' => 'sexta',
    'saturday' => 'sábado',
    'sunday' => 'domingo'
];
$dia_semana_atual = $dia_semana_map[$dia_atual];

$hora_atual = date('H');
$turno_atual = '';
if ($hora_atual >= 7 && $hora_atual < 12) {
    $turno_atual = 'manhã';
} elseif ($hora_atual >= 12 && $hora_atual < 18) {
    $turno_atual = 'tarde';
} elseif ($hora_atual >= 18 && $hora_atual < 22) {
    $turno_atual = 'noite';
} else {
    $turno_atual = 'livre'; // Fora do horário de aula
}

// Verifica se o usuário está logado e pega o papel
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $is_logged_in ? $_SESSION['user_role'] : null;

// Lógica para carregar os dados
try {
    // Carrega a lista de laboratórios
    $sql_labs = "SELECT * FROM laboratorios ORDER BY numero";
    $stmt_labs = $conexao->prepare($sql_labs);
    $stmt_labs->execute();
    $labs = $stmt_labs->fetchAll(PDO::FETCH_ASSOC);

    // Carrega as aulas agendadas para o dia e turno atuais
    $sql_aulas_hoje = "
        SELECT
            a.laboratorio_id,
            a.disciplina,
            p.nome AS professor_nome
        FROM
            aulas a
        JOIN
            professores p ON a.professor_id = p.id
        WHERE
            a.dia_semana = ? AND a.turno = ?
    ";
    $stmt_aulas_hoje = $conexao->prepare($sql_aulas_hoje);
    $stmt_aulas_hoje->execute([$dia_semana_atual, $turno_atual]);
    $aulas_hoje = $stmt_aulas_hoje->fetchAll(PDO::FETCH_ASSOC);

    // Mapeia os laboratórios ocupados
    $labs_ocupados = [];
    foreach ($aulas_hoje as $aula) {
        $labs_ocupados[$aula['laboratorio_id']] = [
            'disciplina' => $aula['disciplina'],
            'professor_nome' => $aula['professor_nome']
        ];
    }

    // --- Lógica para o dashboard de administrador (só é executada se o usuário for admin) ---
    if ($user_role === 'admin') {
        // SQL para contar as aulas por laboratório (visível apenas para admins)
        $sql = "
            SELECT
                l.id,
                COUNT(a.laboratorio_id) AS total_aulas
            FROM
                laboratorios l
            LEFT JOIN
                aulas a ON l.id = a.laboratorio_id
            GROUP BY
                l.id;
        ";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        $aulas_por_lab_query = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $aulas_por_lab = [];
        foreach ($aulas_por_lab_query as $row) {
            $aulas_por_lab[$row['id']] = $row['total_aulas'];
        }
    }

} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Laboratórios</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <i class="fas fa-microchip"></i> Gerenciamento de Laboratórios
    </div>

    <nav>
        <a href="index.php"><i class="fas fa-chart-bar"></i> Dashboard</a>
        <?php if ($user_role === 'admin'): ?>
            <a href="laboratorios.php"><i class="fas fa-flask"></i> Laboratórios</a>
            <a href="monitores.php"><i class="fas fa-user-astronaut"></i> Monitores</a>
            <a href="professores.php"><i class="fas fa-chalkboard-teacher"></i> Professores</a>
            <a href="aulas.php"><i class="fas fa-book"></i> Aulas</a>
            <a href="horarios.php"><i class="fas fa-calendar-alt"></i> Horários</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
        <?php else: ?>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Acesso Admin</a>
        <?php endif; ?>
    </nav>

    <div class="container">
        <h1>Status dos Laboratórios (<?= ucfirst($dia_semana_atual) ?> - <?= ucfirst($turno_atual) ?>)</h1>
        <div class="dashboard-cards">
            <?php foreach ($labs as $lab): ?>
                <?php
                $status = isset($labs_ocupados[$lab['id']]) ? 'ocupado' : 'livre';
                $info = isset($labs_ocupados[$lab['id']]) ? $labs_ocupados[$lab['id']] : null;
                $status_class = $status === 'ocupado' ? 'card-ocupado' : 'card-livre';
                ?>
                <div class="card <?= $status_class ?>">
                    <div class="lab-title"><i class="fas fa-flask"></i> <?= htmlspecialchars($lab['nome']) ?>
                        (<?= htmlspecialchars($lab['numero']) ?>)</div>
                    <div class="lab-info">
                        <i class="fas fa-circle status-icon"></i>
                        Status: <span class="status-text"><?= $status === 'livre' ? 'Livre' : 'Ocupado' ?></span>
                    </div>
                    <?php if ($status === 'ocupado'): ?>
                        <div class="lab-info"><i class="fas fa-book"></i> Disciplina:
                            <?= htmlspecialchars($info['disciplina']) ?></div>
                        <div class="lab-info"><i class="fas fa-chalkboard-teacher"></i> Professor:
                            <?= htmlspecialchars($info['professor_nome']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($user_role === 'admin'): ?>
            <hr>
            <h2>Dashboard Administrativo</h2>
            <div class="dashboard-cards">
                <?php foreach ($labs as $lab): ?>
                    <div class="card">
                        <div class="lab-title"><i class="fas fa-flask"></i> <?= htmlspecialchars($lab['nome']) ?>
                            (<?= htmlspecialchars($lab['numero']) ?>)</div>
                        <div class="lab-info"><i class="fas fa-users"></i> Capacidade:
                            <?= htmlspecialchars($lab['capacidade']) ?></div>
                        <div class="lab-info"><i class="fas fa-video"></i> Projetor: <input type="checkbox" disabled
                                <?= !empty($lab['projetor']) ? 'checked' : '' ?>></div>
                        <div class="lab-aulas"><i class="fas fa-book"></i> Aulas na Semana: <?= $aulas_por_lab[$lab['id']] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>