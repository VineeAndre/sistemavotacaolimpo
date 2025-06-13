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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2><?= htmlspecialchars($poll['title']) ?></h2>
    <p>Início: <?= $poll['start_datetime'] ?> | Fim: <?= $poll['end_datetime'] ?></p>

    <form action="vote.php" method="post">
        <?php foreach ($options as $opt): ?>
            <label>
                <input type="radio" name="option_id" value="<?= $opt['id'] ?>" <?= !$is_active ? 'disabled' : '' ?>>
                <?= htmlspecialchars($opt['option_text']) ?> - <?= (int)$opt['votes'] ?> votos
            </label><br>
        <?php endforeach; ?>

        <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
        <button type="submit" <?= !$is_active ? 'disabled' : '' ?>>Votar</button>
    </form>

    <hr>

    <h3>Resultado Atual:</h3>
    <canvas id="chart" width="400" height="200"></canvas>

    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($options, 'option_text')) ?>,
                datasets: [{
                    label: 'Votos',
                    data: <?= json_encode(array_column($options, 'votes')) ?>,
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        });
    </script>
</body>
</html>