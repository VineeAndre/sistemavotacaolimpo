<?php
require_once '../includes/db.php';

function getUserIP() {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['option_id'], $_POST['poll_id'])) {
    $poll_id = (int)$_POST['poll_id'];
    $option_id = (int)$_POST['option_id'];
    $ip = getUserIP();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes_log WHERE poll_id = ? AND ip_address = ?");
    $stmt->execute([$poll_id, $ip]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Você já votou.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT option_text FROM options WHERE id = ? AND poll_id = ?");
    $stmt->execute([$option_id, $poll_id]);
    $option = $stmt->fetch();

    if (!$option) {
        echo json_encode(['success' => false, 'message' => 'Opção inválida.']);
        exit;
    }

    $pdo->beginTransaction();

    try {
        $pdo->prepare("UPDATE options SET votes = votes + 1 WHERE id = ?")->execute([$option_id]);

        $pdo->prepare("INSERT INTO votes_log (poll_id, option_id, option_text, ip_address) VALUES (?, ?, ?, ?)")
            ->execute([$poll_id, $option_id, $option['option_text'], $ip]);

        $pdo->commit();

        // Obter nova contagem
        $stmt = $pdo->prepare("SELECT votes FROM options WHERE id = ?");
        $stmt->execute([$option_id]);
        $updatedVotes = $stmt->fetchColumn();

        echo json_encode(['success' => true, 'updated_votes' => $updatedVotes]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erro ao votar.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}
