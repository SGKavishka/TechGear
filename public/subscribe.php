<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

verify_csrf();

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Please enter a valid email address.');
    redirect('index.php#newsletter');
}

$stmt = db()->prepare('INSERT IGNORE INTO newsletter_subscribers (email) VALUES (?)');
$stmt->execute([$email]);

flash('success', 'You have been subscribed to TechGear updates.');
redirect('index.php#newsletter');
