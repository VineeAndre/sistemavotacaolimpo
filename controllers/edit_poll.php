<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$poll_id = (int)$_GET['id'];

// Busca a enquete
$stmt = $pdo->prepare("SELECT * FROM polls WHERE id = ?");
$stmt->execute([$poll_id]);
$poll = $stmt->fetch();

if (!$poll) {
    die("Enquete não encontrada.");
}

// Busca as opções
$stmt = $pdo->prepare("SELECT * FROM options WHERE poll_id = ?");
$stmt->execute([$poll_id]);
$options = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $start = $_POST['start_datetime'];
    $end = $_POST['end_datetime'];
    $updated_options = $_POST['options'];

    if (count(array_filter($updated_options)) < 3) {
        die("É necessário no mínimo 3 opções.");
    }

    // Atualiza a enquete
    $stmt = $pdo->prepare("UPDATE polls SET title = ?, start_datetime = ?, end_datetime = ? WHERE id = ?");
    $stmt->execute([$title, $start, $end, $poll_id]);

    // Atualiza as opções existentes
    foreach ($options as $index => $opt) {
        if (isset($updated_options[$index])) {
            $stmt = $pdo->prepare("UPDATE options SET option_text = ? WHERE id = ?");
            $stmt->execute([$updated_options[$index], $opt['id']]);
        }
    }

    header("Location: ../views/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Enquete</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Editar Enquete</h2>
        <form method="post">
            <label for="title">Título</label>
            <input type="text" name="title" value="<?= htmlspecialchars($poll['title']) ?>" required>

            <label for="start_datetime">Data e hora de início</label>
            <input type="datetime-local" name="start_datetime" value="<?= date('Y-m-d\TH:i', strtotime($poll['start_datetime'])) ?>" required>

            <label for="end_datetime">Data e hora de término</label>
            <input type="datetime-local" name="end_datetime" value="<?= date('Y-m-d\TH:i', strtotime($poll['end_datetime'])) ?>" required>

            <label>Opções de resposta</label>
            <?php if (!empty($options)): ?>
                <?php foreach ($options as $index => $opt): ?>
                    <input type="text" name="options[]" value="<?= htmlspecialchars($opt['option_text']) ?>" required>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="error">Nenhuma opção disponível.</p>
            <?php endif; ?>

            <button type="submit" class="btn">Salvar alterações</button>
        </form>

        <a href="../views/index.php" class="back">← Voltar</a>
    </div>
</body>
</html>
