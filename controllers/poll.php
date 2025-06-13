<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Enquete inválida.");
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM polls WHERE id = ?");
$stmt->execute([$id]);
$poll = $stmt->fetch();

if (!$poll) {
    die("Enquete não encontrada.");
}

$stmt = $pdo->prepare("SELECT * FROM options WHERE poll_id = ?");
$stmt->execute([$id]);
$options = $stmt->fetchAll();

date_default_timezone_set('America/Sao_Paulo');
$now = date('Y-m-d H:i:s');
$is_active = $now >= $poll['start_datetime'] && $now <= $poll['end_datetime'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($poll['title']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h2 {
            color: #333;
            text-align: center;
        }

        p {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin-bottom: 10px;
            padding: 8px;
            background: #f9f9f9;
            border-radius: 5px;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .back-button {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .back-button:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($poll['title']) ?></h2>
        <p>Início: <?= $poll['start_datetime'] ?> | Fim: <?= $poll['end_datetime'] ?></p>

        <form action="../controllers/vote.php" method="post">
            <?php foreach ($options as $opt): ?>
                <label>
                    <input type="radio" name="option_id" value="<?= $opt['id'] ?>" <?= !$is_active ? 'disabled' : '' ?>>
                    <?= htmlspecialchars($opt['option_text']) ?> - <?= (int)$opt['votes'] ?> votos
                </label>
            <?php endforeach; ?>

            <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
            <button type="submit" <?= !$is_active ? 'disabled' : '' ?>>Votar</button>
        </form>

        <a class="back-button" href="../views/index.php">← Voltar para Enquetes</a>
    </div>
</body>
</html>