<?php
require 'db.php';
session_start();

// Obter IP do usuário
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['option_id'], $_POST['poll_id'])) {
    $option_id = (int)$_POST['option_id'];
    $poll_id = (int)$_POST['poll_id'];
    $ip = getUserIP();

    // Verificar se já votou nesta enquete
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE poll_id = ? AND ip_address = ?");
    $stmt->execute([$poll_id, $ip]);
    if ($stmt->fetchColumn() > 0) {
        die("Você já votou nesta enquete.");
    }

    // Verificar se a enquete está ativa
    $stmt = $pdo->prepare("SELECT * FROM polls WHERE id = ? AND start_datetime <= NOW() AND end_datetime >= NOW()");
    $stmt->execute([$poll_id]);
    if ($stmt->rowCount() === 0) {
        die("Esta enquete não está ativa.");
    }

    // Atualizar votos
    $stmt = $pdo->prepare("UPDATE options SET votes = votes + 1 WHERE id = ? AND poll_id = ?");
    $stmt->execute([$option_id, $poll_id]);

    // Registrar IP do voto
    $stmt = $pdo->prepare("INSERT INTO votes (poll_id, ip_address, voted_at) VALUES (?, ?, NOW())");
    $stmt->execute([$poll_id, $ip]);

    header("Location: poll.php?id=$poll_id");
    exit;
} else {
    die("Requisição inválida.");
}
