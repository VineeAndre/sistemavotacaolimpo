<?php
require_once __DIR__ . '/../includes/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['option_id'], $_POST['poll_id'])) {
    $option_id = (int)$_POST['option_id'];
    $poll_id = (int)$_POST['poll_id'];

    $stmt = $pdo->prepare("UPDATE options SET votes = votes + 1 WHERE id = ? AND poll_id = ?");
    $stmt->execute([$option_id, $poll_id]);

    header("Location: poll.php?id=$poll_id");
    exit;
} else {
    die("Requisição inválida.");
}
