<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Gerar token CSRF se não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obter a enquete
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM polls WHERE id = ?");
$stmt->execute([$id]);
$poll = $stmt->fetch();

if (!$poll) {
    die("Enquete não encontrada.");
}

// Obter opções da enquete
$stmt = $pdo->prepare("SELECT * FROM options WHERE poll_id = ? ORDER BY id");
$stmt->execute([$id]);
$options = $stmt->fetchAll();

// Verificar se a enquete está ativa
$now = date('Y-m-d H:i:s');
$is_active = $poll['start_datetime'] <= $now && $poll['end_datetime'] >= $now;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($poll['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($poll['title']) ?></h2>

        <?php if ($is_active): ?>
            <form id="vote-form">
                <?php foreach ($options as $opt): ?>
                    <label class="option-label">
                        <input type="radio" name="option_id" value="<?= $opt['id'] ?>">
                        <?= htmlspecialchars($opt['option_text']) ?>
                        (<span class="vote-count" data-id="<?= $opt['id'] ?>"><?= $opt['votes'] ?></span> votos)
                    </label><br>
                <?php endforeach; ?>

                <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <button type="submit" class="btn">Votar</button>
            </form>

            <p id="error-msg" class="error" style="display:none;"></p>
            <p id="success-msg" class="success" style="display:none;">✅ Voto registrado com sucesso!</p>
        <?php else: ?>
            <p class="error">⚠️ Esta enquete está fora do período de votação.</p>
        <?php endif; ?>

        <br>
        <a href="index.php" class="btn btn-secondary">← Voltar</a>
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
            error.textContent = "Selecione uma opção.";
            error.style.display = 'block';
            return;
        }

        const option_id = selected.value;
        const poll_id = document.querySelector('input[name="poll_id"]').value;
        const csrf = document.querySelector('input[name="csrf_token"]').value;

        fetch('../controllers/vote.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `poll_id=${poll_id}&option_id=${option_id}&csrf_token=${csrf}`
        })
        .then(res => res.json())
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
            error.textContent = "Erro de comunicação com o servidor.";
            error.style.display = 'block';
        });
    });
    </script>
</body>
</html>
