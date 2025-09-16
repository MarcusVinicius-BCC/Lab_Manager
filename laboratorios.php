<?php
session_start();
require_once 'db.php';

// Redireciona para o login se o usuário não estiver logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'db.php';

// Lógica para lidar com o envio do formulário (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $numero = $_POST['numero'];
    $capacidade = intval($_POST['capacidade']);
    // Converte o checkbox para 1 (marcado) ou 0 (não marcado)
    $projetor = isset($_POST['projetor']) ? 1 : 0;

    // Comando SQL para inserir um novo laboratório
    $sql = "INSERT INTO laboratorios (nome, numero, capacidade, projetor) VALUES (?, ?, ?, ?)";
    
    try {
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$nome, $numero, $capacidade, $projetor]);
        
        // Redireciona para recarregar a página e mostrar o novo laboratório
        header('Location: laboratorios.php');
        exit;

    } catch(PDOException $e) {
        echo "Erro ao cadastrar laboratório: " . $e->getMessage();
    }
}

// Lógica para exibir os laboratórios já cadastrados
// Comando SQL para selecionar todos os laboratórios do banco de dados
$sql = "SELECT * FROM laboratorios ORDER BY nome";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$labs = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <i class="fas fa-flask"></i> Laboratórios de Informática
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
        <h2>Cadastrar Laboratório</h2>
        <form method="POST">
            <p>Nome: <input type="text" name="nome" required></p>
            <p>Número: <input type="text" name="numero" required></p>
            <p>Capacidade: <input type="number" name="capacidade" required></p>
            <p><label><input type="checkbox" name="projetor"> Projetor</label></p>
            <p><input type="submit" value="Cadastrar"></p>
        </form>
        <h3>Laboratórios Cadastrados</h3>
        <div class="dashboard-cards">
            <?php foreach ($labs as $lab): ?>
            <div class="card">
                <div class="lab-title"><i class="fas fa-flask"></i> <?= htmlspecialchars($lab['nome']) ?> (<?= htmlspecialchars($lab['numero']) ?>)</div>
                <div class="lab-info"><i class="fas fa-users"></i> Capacidade: <?= htmlspecialchars($lab['capacidade']) ?></div>
                <div class="lab-info"><i class="fas fa-video"></i> Projetor: <?= !empty($lab['projetor']) ? 'Sim' : 'Não' ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
