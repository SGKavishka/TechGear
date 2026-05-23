<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

if (current_user()) {
    redirect('profile.php');
}

$errors = [];
$form = [
    'name' => trim($_POST['name'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'phone' => trim($_POST['phone'] ?? ''),
    'address' => trim($_POST['address'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($form['name']) < 3) {
        $errors['name'] = 'Full name must be at least 3 characters.';
    }

    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } else {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$form['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'An account already exists for this email.';
        }
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO users (name, email, password_hash, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $form['name'],
            $form['email'],
            password_hash($password, PASSWORD_DEFAULT),
            'customer',
            $form['phone'] ?: null,
            $form['address'] ?: null,
        ]);

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) db()->lastInsertId();
        flash('success', 'Account created successfully.');
        redirect('profile.php');
    }
}

$pageTitle = 'Register | TechGear';
$pageDescription = 'Create a TechGear account.';
$activePage = 'login';
$extraCss = ['css/register.css'];

?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main class="auth-container section-padding">
    <div class="glass-panel auth-card">
        <span class="eyebrow">New Account</span>
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Register once and use the same account for checkout and order tracking.</p>

        <form method="post" novalidate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-input <?= isset($errors['name']) ? 'is-invalid' : '' ?>" placeholder="Enter your full name" value="<?= h($form['name']) ?>">
                <?php if (isset($errors['name'])): ?><span class="error-message visible"><?= h($errors['name']) ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="you@example.com" value="<?= h($form['email']) ?>">
                <?php if (isset($errors['email'])): ?><span class="error-message visible"><?= h($errors['email']) ?></span><?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-2 auth-grid">
                <div class="form-group">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" id="phone" name="phone" class="form-input" placeholder="+94..." value="<?= h($form['phone']) ?>">
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" id="address" name="address" class="form-input" placeholder="City or full address" value="<?= h($form['address']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="Create a strong password">
                <?php if (isset($errors['password'])): ?><span class="error-message visible"><?= h($errors['password']) ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" placeholder="Repeat your password">
                <?php if (isset($errors['confirm_password'])): ?><span class="error-message visible"><?= h($errors['confirm_password']) ?></span><?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary auth-submit">Register Now</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Log in here</a>
        </div>
    </div>
</main>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
