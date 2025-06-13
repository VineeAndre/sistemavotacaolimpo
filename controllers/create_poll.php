<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $start = $_POST['start_datetime'];
    $end = $_POST['end_datetime'];
    $options = array_map(function($opt) {
    return htmlspecialchars(trim($opt));
}, $_POST['options']);

    if (count($options) < 3) {
        $error = "Você precisa inserir no mínimo 3 opções.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO polls (title, start_datetime, end_datetime) VALUES (?, ?, ?)");
        $stmt->execute([$title, $start, $end]);
        $poll_id = $pdo->lastInsertId();

        foreach ($options as $opt) {
            $stmt = $pdo->prepare("INSERT INTO options (poll_id, option_text) VALUES (?, ?)");
            $stmt->execute([$poll_id, $opt]);
        }

        header("Location: ../views/index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Enquete</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h2>Criar Nova Enquete</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Título:</label>
        <input type="text" name="title" required>

        <label>Data de Início:</label>
        <input type="datetime-local" name="start_datetime" required>

        <label>Data de Término:</label>
        <input type="datetime-local" name="end_datetime" required>

        <label>Opções (mínimo 3):</label>
        <div class="option-group">
            <input type="text" name="options[]" placeholder="Opção 1" required>
            <input type="text" name="options[]" placeholder="Opção 2" required>
            <input type="text" name="options[]" placeholder="Opção 3" required>
            <input type="text" name="options[]" placeholder="Opção 4 (opcional)">
            <input type="text" name="options[]" placeholder="Opção 5 (opcional)">
        </div>

        <button type="submit">Criar Enquete</button>
    </form>

    <a class="back" href="../views/index.php">← Voltar</a>
</div>
</body>
</html>
