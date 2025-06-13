<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $start = $_POST['start_datetime'];
    $end = $_POST['end_datetime'];
    $options = array_filter($_POST['options']);

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
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: auto;
            padding: 25px;
            background: white;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #007BFF;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="datetime-local"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .option-group input {
            margin-bottom: 10px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            background: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .back {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            color: #007BFF;
        }

        @media (max-width: 600px) {
            button {
                font-size: 14px;
            }
        }
    </style>
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

    <a class="back" href="index.php">← Voltar</a>
</div>
</body>
</html>
