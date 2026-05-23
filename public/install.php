<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$status = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = file_get_contents(DATABASE_PATH . '/schema.sql');
        if ($sql === false) {
            throw new RuntimeException('Unable to read database/schema.sql');
        }

        $pdo = db_server();
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if ($statement !== '') {
                $pdo->exec($statement);
            }
        }

        $status = 'Database installed successfully. You can now open the store.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install TechGear</title>
    <link rel="stylesheet" href="<?= h(asset_url('css/styles.css')) ?>">
    <link rel="stylesheet" href="<?= h(asset_url('css/components.css')) ?>">
</head>

<body>
    <main class="container section-padding installer-page">
        <div class="glass-panel installer-card">
            <span class="eyebrow">XAMPP Setup</span>
            <h1>Install TechGear Database</h1>
            <p>This creates the <strong>techgear</strong> MySQL database, tables, sample products, demo orders, and two login accounts.</p>

            <?php if ($status): ?>
                <div class="notice success"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="notice danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="post">
                <button class="btn btn-primary" type="submit">Run Installer</button>
                <a class="btn btn-secondary" href="index.php">Open Store</a>
            </form>

            <div class="credential-list">
                <div>
                    <strong>Admin</strong>
                    <span>admin@techgear.local / admin123</span>
                </div>
                <div>
                    <strong>Customer</strong>
                    <span>user@techgear.local / user12345</span>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
