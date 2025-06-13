<?php
require_once __DIR__ . '/../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $options = $_POST['options'] ?? [];

    // Validação dos campos obrigatórios
    if (empty($title) || empty($start_datetime) || empty($end_datetime)) {
        $error = 'Preencha todos os campos obrigatórios.';
    } elseif (
        empty(trim($options[0])) ||
        empty(trim($options[1])) ||
        empty(trim($options[2]))
    ) {
        $error = 'As três primeiras opções são obrigatórias.';
    } else {
        try {
            $pdo->beginTransaction();

            // Inserir a enquete
            $stmt = $pdo->prepare("INSERT INTO polls (title, start_datetime, end_datetime) VALUES (?, ?, ?)");
            $stmt->execute([$title, $start_datetime, $end_datetime]);
            $poll_id = $pdo->lastInsertId();

            // Inserir as opções válidas
            $stmtOpt = $pdo->prepare("INSERT INTO options (poll_id, option_text) VALUES (?, ?)");

            foreach ($options as $opt) {
                $opt = trim($opt);
                if (!empty($opt)) {
                    $stmtOpt->execute([$poll_id, $opt]);
                }
            }

            $pdo->commit();

            // Redirecionar após criar
            header("Location: ../views/index.php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Erro ao criar enquete: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Enquete</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Criar Nova Enquete</h2>

        <?php if (!empty($error)): ?>
            <div style="color: red; background-color: #ffe6e6; padding: 10px; border-radius: 5px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>Título da Enquete:</label><br>
            <input type="text" name="title" required><br><br>

            <label>Data/Hora de Início:</label><br>
            <input type="datetime-local" name="start_datetime" required><br><br>

            <label>Data/Hora de Fim:</label><br>
            <input type="datetime-local" name="end_datetime" required><br><br>

            <label>Opções:</label><br><br>

            <?php
            for ($i = 0; $i < 5; $i++):
                $placeholder = ($i < 3) ? "Opção " . ($i + 1) . " (obrigatória)" : "Opção " . ($i + 1) . " (opcional)";
            ?>
                <input 
                    type="text" 
                    name="options[]" 
                    placeholder="<?= $placeholder ?>" 
                    <?= $i < 3 ? 'required' : '' ?>
                ><br>
            <?php endfor; ?>

            <br>
            <button type="submit">Criar Enquete</button>
        </form>

        <br>
        <a href="../views/index.php">← Voltar</a>
    </div>
</body>
</html>
