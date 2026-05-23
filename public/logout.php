<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

unset($_SESSION['user_id']);
flash('success', 'You have been logged out.');
redirect('index.php');
