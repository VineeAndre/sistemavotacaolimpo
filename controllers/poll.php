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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($poll['title']) ?></h2>
        <p>Início: <?= $poll['start_datetime'] ?> | Fim: <?= $poll['end_datetime'] ?></p>

        <?php if ($is_active): ?>
            <form id="vote-form" action="../controllers/vote.php" method="post">
                <?php foreach ($options as $opt): ?>
                    <label>
                        <input type="radio" name="option_id" value="<?= $opt['id'] ?>">
                        <?= htmlspecialchars($opt['option_text']) ?> - <?= (int)$opt['votes'] ?> votos
                    </label><br>
                <?php endforeach; ?>

                <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
                <button type="submit">Votar</button>
            </form>

            <p id="error-msg" style="color: red; display: none;">Por favor, selecione uma opção para votar.</p>
        <?php else: ?>
            <p style="color: red; font-weight: bold; margin-top: 20px;">
                Esta enquete está fora do período de votação.
            </p>
        <?php endif; ?>

        <a class="back-button" href="../views/index.php">← Voltar para Enquetes</a>
    </div>

    <script>
        document.getElementById('vote-form')?.addEventListener('submit', function(event) {
            const checked = document.querySelector('input[name="option_id"]:checked');
            const errorMsg = document.getElementById('error-msg');

            if (!checked) {
                event.preventDefault();
                errorMsg.style.display = 'block';
            } else {
                errorMsg.style.display = 'none';
            }
        });
    </script>
</body>
</html>
