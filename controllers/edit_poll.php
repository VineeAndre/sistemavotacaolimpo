<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$poll_id = (int)$_GET['id'];

// Obtém dados da enquete
$stmt = $pdo->prepare("SELECT * FROM polls WHERE id = ?");
$stmt->execute([$poll_id]);
$poll = $stmt->fetch();

if (!$poll) {
    die("Enquete não encontrada.");
}

// Obtém opções existentes
$stmt = $pdo->prepare("SELECT * FROM options WHERE poll_id = ?");
$stmt->execute([$poll_id]);
$options = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $start = $_POST['start_datetime'];
    $end = $_POST['end_datetime'];
    $updated_options = $_POST['options'];

    // Validação
    if (count($updated_options) < 3) {
        die("É necessário no mínimo 3 opções.");
    }

    // Atualiza enquete
    $stmt = $pdo->prepare("UPDATE polls SET title = ?, start_datetime = ?, end_datetime = ? WHERE id = ?");
    $stmt->execute([$title, $start, $end, $poll_id]);

    // Atualiza cada opção existente
    foreach ($options as $index => $opt) {
        $stmt = $pdo->prepare("UPDATE options SET option_text = ? WHERE id = ?");
        $stmt->execute([$updated_options[$index], $opt['id']]);
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
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #007BFF;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"],
        input[type="datetime-local"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn {
            padding: 10px 18px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
        }
    </style>
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
            <?php foreach ($options as $index => $opt): ?>
                <input type="text" name="options[]" value="<?= htmlspecialchars($opt['option_text']) ?>" required>
            <?php endforeach; ?>

            <button type="submit" class="btn">Salvar alterações</button>
        </form>
    </div>
</body>
</html>
