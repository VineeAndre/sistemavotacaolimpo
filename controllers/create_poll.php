<?php
require_once __DIR__ . '/../includes/db.php';

$error = '';
$title = $_POST['title'] ?? '';
$start_datetime = $_POST['start_datetime'] ?? '';
$end_datetime = $_POST['end_datetime'] ?? '';
$options = $_POST['options'] ?? array_fill(0, 5, '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valida campos obrigatórios
    $option_required_count = 0;
    foreach (array_slice($options, 0, 3) as $opt) {
        if (trim($opt) !== '') {
            $option_required_count++;
        }
    }

    if (empty($title) || empty($start_datetime) || empty($end_datetime)) {
        $error = 'Preencha todos os campos obrigatórios.';
    } elseif ($option_required_count < 3) {
        $error = 'As 3 primeiras opções são obrigatórias.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO polls (title, start_datetime, end_datetime) VALUES (?, ?, ?)");
            $stmt->execute([$title, $start_datetime, $end_datetime]);
            $poll_id = $pdo->lastInsertId();

            $stmtOpt = $pdo->prepare("INSERT INTO options (poll_id, option_text) VALUES (?, ?)");

            foreach ($options as $opt) {
                $opt = trim($opt);
                if (!empty($opt)) {
                    $stmtOpt->execute([$poll_id, $opt]);
                }
            }

            $pdo->commit();

            // Redireciona para a página inicial
            header("Location: ../views/index.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Erro ao criar enquete: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Enquete</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Criar Nova Enquete</h2>

        <?php if (!empty($error)): ?>
            <div class="error" style="color:red; margin-bottom:10px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>Título da Enquete:</label><br>
            <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required><br><br>

            <label>Data/Hora de Início:</label><br>
            <input type="datetime-local" name="start_datetime" value="<?= htmlspecialchars($start_datetime) ?>" required><br><br>

            <label>Data/Hora de Fim:</label><br>
            <input type="datetime-local" name="end_datetime" value="<?= htmlspecialchars($end_datetime) ?>" required><br><br>

            <label>Opções:</label><br>
            <small style="color:gray;">(As 3 primeiras são obrigatórias, as 2 últimas são opcionais)</small><br><br>

            <?php for ($i = 0; $i < 5; $i++): ?>
                <input
                    type="text"
                    name="options[]"
                    placeholder="Opção <?= $i + 1 ?> <?= $i >= 3 ? '(opcional)' : '(obrigatória)' ?>"
                    value="<?= htmlspecialchars($options[$i] ?? '') ?>"
                    <?= $i < 3 ? 'required' : '' ?>
                ><br>
            <?php endfor; ?>

            <br>
            <button type="submit">Criar Enquete</button>
            <a href="../views/index.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>
</body>
</html>
