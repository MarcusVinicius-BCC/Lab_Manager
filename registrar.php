<?php
session_start();
require_once 'db.php';

// Se o usuário já estiver logado, redireciona para o index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Geração de token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$mensagem = '';
$tipo_mensagem = ''; // 'sucesso' ou 'erro'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação do token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Ação não autorizada: Token CSRF inválido.');
    }

    $matricula = $_POST['matricula'];
    $password = $_POST['password'];
    $nome = $_POST['nome'];
    $curso = $_POST['curso'];
    $ano = $_POST['ano'];
    $universidade = $_POST['universidade'];
    $role = 'admin'; // Todos os usuários cadastrados por aqui terão o papel de 'admin'

    if (empty($matricula) || empty($password) || empty($nome) || empty($curso) || empty($ano) || empty($universidade)) {
        $mensagem = 'Todos os campos são obrigatórios.';
        $tipo_mensagem = 'erro';
    } else {
        try {
            $conexao->beginTransaction();

            // 1. Verifica se a matrícula está na lista de autorizadas e se não foi usada
            $sql_check_auth = "SELECT usada FROM matriculas_autorizadas WHERE matricula = ?";
            $stmt_check_auth = $conexao->prepare($sql_check_auth);
            $stmt_check_auth->execute([$matricula]);
            $matricula_auth = $stmt_check_auth->fetch(PDO::FETCH_ASSOC);

            if (!$matricula_auth) {
                $mensagem = 'Matrícula não autorizada para cadastro.';
                $tipo_mensagem = 'erro';
            } elseif ($matricula_auth['usada']) {
                $mensagem = 'Esta matrícula já foi utilizada para criar um usuário.';
                $tipo_mensagem = 'erro';
            } else {
                // 2. Hashear a senha
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // 3. Inserir o novo usuário com todos os dados
                $sql_insert = "INSERT INTO usuarios (username, password_hash, role, nome, curso, ano, universidade) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert = $conexao->prepare($sql_insert);
                $stmt_insert->execute([$matricula, $password_hash, $role, $nome, $curso, $ano, $universidade]);

                // 4. Marcar a matrícula como usada
                $sql_update_auth = "UPDATE matriculas_autorizadas SET usada = TRUE WHERE matricula = ?";
                $stmt_update_auth = $conexao->prepare($sql_update_auth);
                $stmt_update_auth->execute([$matricula]);

                $conexao->commit();
                $mensagem = "Cadastro realizado com sucesso! Você já pode fazer o login.";
                $tipo_mensagem = 'sucesso';
            }
        } catch (PDOException $e) {
            $conexao->rollBack();
            if ($e->errorInfo[1] == 1062) {
                $mensagem = 'Esta matrícula já está cadastrada no sistema.';
            } else {
                $mensagem = 'Erro no banco de dados. Tente novamente mais tarde.';
            }
            $tipo_mensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastre-se</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <i class="fas fa-user-plus"></i> Cadastro de Usuário
    </div>
    <div class="container">
        <h2>Crie sua conta</h2>
        <p>Preencha os dados abaixo para criar seu acesso. Sua matrícula deve estar pré-autorizada.</p>

        <?php if ($mensagem): ?>
            <p
                style="text-align:center; font-weight: bold; color:<?= $tipo_mensagem === 'erro' ? '#e74c3c' : '#2ecc71' ?>;">
                <?= htmlspecialchars($mensagem) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <p>Nome Completo: <input type="text" name="nome" required></p>
            <p>Universidade: <input type="text" name="universidade" required></p>
            <p>Curso: <input type="text" name="curso" required></p>
            <p>Ano de Ingresso: <input type="number" name="ano" min="1980" max="<?= date('Y') ?>" required></p>
            <p>Matrícula (será seu usuário): <input type="text" name="matricula" required></p>
            <p>Senha: <input type="password" name="password" required></p>
            <p><input type="submit" value="Cadastrar"></p>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            <a href="login.php">
                <i class="fas fa-arrow-left"></i> Já tem uma conta? Faça o login
            </a>
        </p>
    </div>
</body>

</html>