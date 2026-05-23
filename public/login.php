<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

if (current_user()) {
    redirect('profile.php');
}

$errors = [];
$email = trim($_POST['email'] ?? '');
$next = safe_redirect_target($_GET['next'] ?? ($_POST['next'] ?? 'profile.php'), 'profile.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    if (!$errors) {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if (($user['role'] ?? '') === 'admin') {
                flash('error', 'Use the dedicated admin portal for admin access.');
                redirect('admin-login.php');
            }

            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            flash('success', 'Welcome back, ' . $user['name'] . '.');
            redirect($next);
        }

        $errors['general'] = 'Invalid email or password.';
    }
}

$pageTitle = 'Login | TechGear';
$pageDescription = 'Login to your TechGear account.';
$activePage = 'login';
$extraCss = ['css/login.css'];

?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main class="auth-container section-padding">
    <div class="glass-panel auth-card">
        <span class="eyebrow">Account</span>
        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Log in to manage orders, checkout faster, and access your dashboard.</p>

        <?php if (isset($errors['general'])): ?>
            <div class="notice danger"><?= h($errors['general']) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="next" value="<?= h($next) ?>">

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="you@example.com" value="<?= h($email) ?>">
                <?php if (isset($errors['email'])): ?><span class="error-message visible"><?= h($errors['email']) ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="Enter your password">
                <?php if (isset($errors['password'])): ?><span class="error-message visible"><?= h($errors['password']) ?></span><?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary auth-submit">Log In</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</main>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
