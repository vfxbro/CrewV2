<?php
// Проверка авторизации и редирект
require_once '../includes/config.php';

// Если не авторизован - на страницу логина
if (!check_admin_auth()) {
    redirect('login.php');
}

// Если авторизован - на dashboard
redirect('dashboard.php');
?>