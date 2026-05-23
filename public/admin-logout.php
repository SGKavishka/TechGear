<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

unset($_SESSION['admin_user_id']);
flash('success', 'Admin session ended.');
redirect('admin-login.php');
