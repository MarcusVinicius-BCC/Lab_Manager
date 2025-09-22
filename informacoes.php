<?php
session_start();
require_once "db.php";

// Carregar dados dos laboratórios
$labs = $conexao->query("SELECT * FROM laboratorios ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC);

// Carregar dados dos monitores
$monitores = $conexao->query(
    "SELECT m.nome, m.email, m.turno, m.laboratorio_id 
     FROM monitores m"
)->fetchAll(PDO::FETCH_ASSOC);

// Agrupar monitores por laboratório
$labs_com_monitores = [];
foreach ($labs as $lab) {
    $labs_com_monitores[$lab['id']] = $lab;
    $labs_com_monitores[$lab['id']]['monitores'] = [];
}
foreach ($monitores as $monitor) {
    if (isset($labs_com_monitores[$monitor['laboratorio_id']])) {
        $labs_com_monitores[$monitor['laboratorio_id']]['monitores'][] = $monitor;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Informações - LabManager</title>
  <link rel="stylesheet" href="css/style.css?v=1.2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="header">
    <h1><i class="fas fa-info-circle"></i> Informações</h1>
    <a href="index.php" class="btn">Voltar</a>
  </div>

  <div class="container">

    <div class="card" style="text-align: center; margin-bottom: 2rem; padding: 1.5rem;">
        <h2>Bem-vindo à página dos Laboratórios de Computação!</h2>
        <p>Cursos de Bacharelado em Ciência da Computação (BCC) & Bacharelado em Sistemas de Informação (BSI)</p>
        <hr style="margin: 1rem 0;">
        <h4>Nossos Laboratórios:</h4>
        <ul style="list-style: none; padding: 0; margin: 1rem 0;">
            <li style="margin-bottom: 0.5rem;"><i class="fas fa-laptop-code"></i> Laboratório de Desenvolvimento de Software (LabDev)</li>
            <li style="margin-bottom: 0.5rem;"><i class="fas fa-lightbulb"></i> Laboratório de Inovação (LabInova)</li>
            <li><i class="fas fa-brain"></i> Laboratório de Algoritmos e Programação (LabProg)</li>
        </ul>
        <hr style="margin: 1rem 0;">
        <p>Os laboratórios atendem prioritariamente as atividades de ensino dos cursos de BCC e BSI.</p>
        <p>Para reservar um determinado laboratório, verifique a disponibilidade na <a href="agenda.php"><strong>Agenda</strong></a> e em seguida preencha o <a href="agendamentos.php"><strong>Formulário de Agendamento</strong></a>.</p>
        <div class="alert" style="background-color: #fffbe6; border-left-color: #f39c12; color: #8a6d3b; margin-top: 1.5rem;">
            <strong>Atenção:</strong> Leia o <a href="regimento.php" target="_blank"><strong>regimento</strong></a> para verificar se a sua demanda está dentro das atribuições do laboratório a ser reservado.
        </div>
    </div>

    <h2><i class="fas fa-desktop"></i> Nossos Laboratórios e Monitores</h2>

    <div class="dashboard-cards">
        <?php foreach ($labs_com_monitores as $lab): ?>
            <div class="card" style="padding: 0; overflow: hidden;">
                <?php if (!empty($lab['foto_url'])): ?>
                    <img src="img/<?= htmlspecialchars($lab['foto_url']) ?>" alt="Foto do <?= htmlspecialchars($lab['nome']) ?>" class="lab-photo">
                <?php endif; ?>
                <div style="padding: 1.5rem;">
                    <h3><?= htmlspecialchars($lab['nome']) ?> (<?= $lab['numero'] ?>)</h3>
                    <div class="lab-info"><i class="fas fa-map-marker-alt"></i> <strong>Local:</strong> <?= htmlspecialchars($lab['localizacao']) ?></div>
                    <div class="lab-info"><i class="fas fa-users"></i> <strong>Capacidade:</strong> <?= htmlspecialchars($lab['capacidade']) ?> lugares</div>
                    
                    <hr style="margin: 1.5rem 0;">

                    <h4><i class="fas fa-user-astronaut"></i> Monitores Responsáveis</h4>
                    <?php if (empty($lab['monitores'])): ?>
                        <p>Nenhum monitor cadastrado para este laboratório.</p>
                    <?php else: ?>
                        <ul style="list-style: none; padding-left: 0;">
                            <?php foreach ($lab['monitores'] as $monitor): ?>
                                <li style="margin-bottom: 0.8rem;">
                                    <strong><?= htmlspecialchars($monitor['nome']) ?></strong> (<?= ucfirst($monitor['turno']) ?>)<br>
                                    <small><i class="fas fa-envelope"></i> <?= htmlspecialchars($monitor['email']) ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <hr>

    <h2><i class="fas fa-address-book"></i> Contatos Úteis</h2>
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <p><strong>Coordenação do Programa de Computação:</strong></p>
        <ul style="list-style: none; padding-left: 0;">
            <li><i class="fas fa-envelope"></i> Email: <a href="mailto:computacao.ieg@ufopa.edu.br">computacao.ieg@ufopa.edu.br</a></li>
            <li><i class="fas fa-phone"></i> Telefone: (93) XXXX-XXXX</li>
        </ul>
        <p style="margin-top: 1.5rem;"><strong>Suporte Técnico (TI):</strong></p>
        <ul style="list-style: none; padding-left: 0;">
            <li><i class="fas fa-envelope"></i> Email: <a href="mailto:suporte.ti@ufopa.edu.br">suporte.ti@ufopa.edu.br</a></li>
        </ul>
    </div>

  </div>
</body>
</html>