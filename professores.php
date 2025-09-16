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
    $disciplina = $_POST['disciplina'];

    // Comando SQL para inserir um novo professor
    $sql = "INSERT INTO professores (nome, disciplina) VALUES (?, ?)";

    try {
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$nome, $disciplina]);

        // Redireciona para recarregar a página e mostrar o novo professor
        header('Location: professores.php');
        exit;

    } catch (PDOException $e) {
        echo "Erro ao cadastrar professor: " . $e->getMessage();
    }
}

// Lógica para carregar todos os professores para exibição
$sql = "SELECT * FROM professores ORDER BY nome";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Professores</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <i class="fas fa-chalkboard-teacher"></i> Professores
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
        <h2>Cadastrar Professor</h2>
        <form method="POST">
            <p>Nome: <input type="text" name="nome" required></p>
            <p>Disciplina: <input type="text" name="disciplina" required></p>
            <p><input type="submit" value="Cadastrar"></p>
        </form>
        <h3>Professores Cadastrados</h3>
        <div class="dashboard-cards">
            <?php foreach ($professores as $prof): ?>
                <div class="card">
                    <div class="lab-title"><i class="fas fa-chalkboard-teacher"></i> <?= htmlspecialchars($prof['nome']) ?>
                    </div>
                    <div class="lab-info"><i class="fas fa-book"></i> Disciplina:
                        <?= htmlspecialchars($prof['disciplina']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>