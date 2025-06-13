<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id = (int)$_GET['id'];

// Buscar dados da enquete
$stmt = $pdo->prepare("SELECT * FROM polls WHERE id = ?");
$stmt->execute([$id]);
$poll = $stmt->fetch();

if (!$poll) {
    die("Enquete não encontrada.");
}

// Buscar as opções
$stmt = $pdo->prepare("SELECT * FROM options WHERE poll_id = ? ORDER BY id");
$stmt->execute([$id]);
$options = $stmt->fetchAll();

$now = date('Y-m-d H:i:s');
$is_active = ($poll['start_datetime'] <= $now) && ($poll['end_datetime'] >= $now);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($poll['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($poll['title']) ?></h2>

        <?php if ($is_active): ?>
            <form id="vote-form">
                <?php foreach ($options as $opt): ?>
                    <label>
                        <input type="radio" name="option_id" value="<?= $opt['id'] ?>">
                        <?= htmlspecialchars($opt['option_text']) ?> - <span class="vote-count" data-id="<?= $opt['id'] ?>"><?= $opt['votes'] ?></span>
                    </label><br>
                <?php endforeach; ?>
                <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
                <button type="submit">Votar</button>
            </form>

            <p id="error-msg" style="color: red; display:none;"></p>
            <p id="success-msg" style="color: green; display:none;">Voto registrado com sucesso!</p>
        <?php else: ?>
            <p>Esta enquete está fora do período de votação.</p>
        <?php endif; ?>

        <br>
        <a href="index.php">← Voltar</a>
    </div>

    <script>
    document.getElementById('vote-form')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const selected = document.querySelector('input[name="option_id"]:checked');
        const error = document.getElementById('error-msg');
        const success = document.getElementById('success-msg');
        error.style.display = 'none';
        success.style.display = 'none';

        if (!selected) {
            error.textContent = "Selecione uma opção antes de votar.";
            error.style.display = 'block';
            return;
        }

        const option_id = selected.value;
        const poll_id = document.querySelector('input[name="poll_id"]').value;

        fetch('../controllers/vote.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `poll_id=${poll_id}&option_id=${option_id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                success.style.display = 'block';
                document.querySelector(`.vote-count[data-id="${option_id}"]`).textContent = data.updated_votes;
            } else {
                error.textContent = data.message;
                error.style.display = 'block';
            }
        })
        .catch(() => {
            error.textContent = "Erro ao enviar o voto. Tente novamente.";
            error.style.display = 'block';
        });
    });
    </script>
</body>
</html>
