<?php
require_once __DIR__ . '/../includes/db.php';


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id = (int)$_GET['id'];

// Apaga as opções da enquete
$pdo->prepare("DELETE FROM options WHERE poll_id = ?")->execute([$id]);

// Apaga a enquete
$pdo->prepare("DELETE FROM polls WHERE id = ?")->execute([$id]);

header("Location: index.php");
exit;
