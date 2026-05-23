<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$sessionAdmin = current_admin();
if ($sessionAdmin) {
    redirect('admin.php');
}

$errors = [];
$email = trim($_POST['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid admin email address.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    if (!$errors) {
        $stmt = db()->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_user_id'] = (int) $admin['id'];
            flash('success', 'Admin session started.');
            redirect('admin.php');
        }

        $errors['general'] = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TechGear admin portal login.">
    <title>Admin Login | TechGear</title>
    <link rel="stylesheet" href="<?= h(asset_url('css/styles.css')) ?>">
    <link rel="stylesheet" href="<?= h(asset_url('css/components.css')) ?>">
    <link rel="stylesheet" href="<?= h(asset_url('css/admin.css')) ?>">
</head>

<body class="admin-login-body">
    <?php if ($message = flash('success')): ?>
        <div class="flash-message flash-success"><?= h($message) ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="flash-message flash-error"><?= h($message) ?></div>
    <?php endif; ?>

    <main class="admin-login-shell">
        <section class="admin-login-panel">
            <div class="admin-login-brand">
                <span class="admin-brand-mark">TG</span>
                <div>
                    <span class="eyebrow">Restricted Portal</span>
                    <h1>TechGear Operations</h1>
                    <p>Admin access for inventory, orders, users, and support messages.</p>
                </div>
            </div>

            <?php if (isset($errors['general'])): ?>
                <div class="notice danger"><?= h($errors['general']) ?></div>
            <?php endif; ?>

            <form method="post" novalidate>
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email" class="form-label">Admin Email</label>
                    <input type="email" id="email" name="email" class="form-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= h($email) ?>" placeholder="admin@techgear.local">
                    <?php if (isset($errors['email'])): ?><span class="error-message visible"><?= h($errors['email']) ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="Enter admin password">
                    <?php if (isset($errors['password'])): ?><span class="error-message visible"><?= h($errors['password']) ?></span><?php endif; ?>
                </div>

                <button class="btn btn-primary admin-login-submit" type="submit">Enter Admin Portal</button>
            </form>

            <div class="admin-login-meta">
                <span>Demo admin: admin@techgear.local / admin123</span>
                <a href="index.php">Return to storefront</a>
            </div>
        </section>
    </main>
</body>

</html>
