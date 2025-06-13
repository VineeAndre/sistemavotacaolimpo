<?php
require_once __DIR__ . '/../includes/db.php';

$stmt = $pdo->query("SELECT * FROM polls ORDER BY start_datetime DESC");
$polls = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Enquetes Disponíveis</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Enquetes Disponíveis</h1>
      <a class="btn" href="../controllers/create_poll.php">+ Nova Enquete</a>
    </div>

    <?php if (count($polls) === 0): ?>
      <p>Nenhuma enquete cadastrada.</p>
    <?php else: ?>
      <?php foreach ($polls as $poll): ?>
        <div class="poll">
          <h3><?= htmlspecialchars($poll['title']) ?></h3>
          <p>De <?= date('d/m/Y H:i', strtotime($poll['start_datetime'])) ?>
             até <?= date('d/m/Y H:i', strtotime($poll['end_datetime'])) ?></p>

          <div class="poll-actions">
            <a class="btn" href="../views/poll.php?id=<?= $poll['id'] ?>">Ver</a>
            <a class="btn btn-secondary" href="../controllers/edit_poll.php?id=<?= $poll['id'] ?>">Editar</a>
            <a class="btn btn-danger" href="../controllers/delete_poll.php?id=<?= $poll['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta enquete?')">Excluir</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
