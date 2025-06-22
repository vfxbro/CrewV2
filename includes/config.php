<?php
// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'dbdelfhoorjeuh');
define('DB_USER', 'chusette_ZjA0N');
define('DB_PASS', '9A7sR#0VP6(D8#l(');

// Настройки сайта
define('SITE_URL', 'https://old.softmed.com.au');
define('SITE_NAME', 'Crew of Experts GmbH');
define('ADMIN_EMAIL', 'admin@crew-experts.com');

// Настройки сессии
define('SESSION_TIMEOUT', 3600);
define('ADMIN_SESSION_NAME', 'crew_admin_session');
define('CSRF_TOKEN_NAME', 'csrf_token');

// Языки
define('DEFAULT_LANGUAGE', 'de');
define('ADMIN_LANGUAGES', ['de', 'en']);

// Настройки файлов
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5242880);

// Настройки бронирования
define('BOOKING_TIME_ZONE', 'Europe/Berlin');
define('BOOKING_START_HOUR', 9);
define('BOOKING_END_HOUR', 17);
define('BOOKING_SLOT_DURATION', 30);

// Отладка
define('DEBUG_MODE', false);
define('LOG_ERRORS', true);

// Временная зона
date_default_timezone_set(BOOKING_TIME_ZONE);

// Функции безопасности
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function check_admin_auth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function log_error($message) {
    if (LOG_ERRORS) {
        if (!file_exists('logs')) {
            mkdir('logs', 0755, true);
        }
        error_log(date('Y-m-d H:i:s') . " - " . $message . "\n", 3, "logs/error.log");
    }
}

// Запуск сессии только если она не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>