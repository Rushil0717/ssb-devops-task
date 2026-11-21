<?php
$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    die("Configuration file missing. Copy config.example.php to config.php.");
}
$config = require $configFile;

try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    error_log("DB Connection Error: " . $e->getMessage());
    http_response_code(500);
    die("Database connection failed. Check server logs.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
    $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (:title)");
    $stmt->execute(['title' => trim($_POST['title'])]);
    header("Location: index.php");
    exit;
}

if (isset($_GET['complete'])) {
    $stmt = $pdo->prepare("UPDATE tasks SET status = 'completed' WHERE id = :id");
    $stmt->execute(['id' => (int)$_GET['complete']]);
    header("Location: index.php");
    exit;
}

$tasks = $pdo->query("SELECT * FROM tasks ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>SSB Internal Ops Tracker</title></head>
<body style="font-family: sans-serif; max-width: 650px; margin: 40px auto;">
    <h2>SSB Digital - Internal Ops Tasks</h2>
    <form method="POST" style="margin-bottom: 20px;">
        <input type="text" name="title" placeholder="New task title..." required style="padding: 8px; width: 70%;">
        <button type="submit" style="padding: 8px 14px;">Add Task</button>
    </form>
    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr><th>ID</th><th>Task</th><th>Status</th><th>Action</th></tr>
        <?php foreach ($tasks as $t): ?>
        <tr>
            <td><?= htmlspecialchars($t['id']) ?></td>
            <td><?= htmlspecialchars($t['title']) ?></td>
            <td><?= htmlspecialchars($t['status']) ?></td>
            <td>
                <?php if ($t['status'] === 'pending'): ?>
                    <a href="?complete=<?= $t['id'] ?>">Mark Complete</a>
                <?php else: ?>
                    Done
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
