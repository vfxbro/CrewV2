<?php
// Простой скрипт для создания таблиц БД
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ЗАМЕНИТЕ эти данные на ПРАВИЛЬНЫЕ!
$host = 'localhost';
$dbname = 'dbdelfhoorjeuh';      // <<<--- ЗАМЕНИТЕ
$username = 'chusette_ZjA0N';  // <<<--- ЗАМЕНИТЕ  
$password = '9A7sR#0VP6(D8#l(';    // <<<--- ЗАМЕНИТЕ

echo "<h2>Database Setup</h2>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<p style='color: green;'>✓ Подключение к БД успешно!</p>";
    
    // SQL для создания таблиц
    $tables = [
        "CREATE TABLE IF NOT EXISTS `admins` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL,
            `email` varchar(100) NOT NULL,
            `password` varchar(255) NOT NULL,
            `language` enum('de','en') DEFAULT 'de',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `username` (`username`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS `pages` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `slug` varchar(100) NOT NULL,
            `title` varchar(255) NOT NULL,
            `content` longtext,
            `meta_title` varchar(255) DEFAULT NULL,
            `meta_description` text,
            `meta_keywords` text,
            `is_active` tinyint(1) DEFAULT '1',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS `jobs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `description` longtext,
            `short_description` text,
            `company` varchar(255) DEFAULT NULL,
            `location` varchar(255) DEFAULT NULL,
            `job_type` enum('full_time','part_time','contract','internship') DEFAULT 'full_time',
            `salary_range` varchar(100) DEFAULT NULL,
            `requirements` longtext,
            `benefits` longtext,
            `contact_email` varchar(100) DEFAULT NULL,
            `is_active` tinyint(1) DEFAULT '1',
            `featured` tinyint(1) DEFAULT '0',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS `bookings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(100) NOT NULL,
            `phone` varchar(50) DEFAULT NULL,
            `service_type` varchar(100) DEFAULT NULL,
            `booking_date` date NOT NULL,
            `booking_time` time NOT NULL,
            `message` text,
            `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS `slides` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) DEFAULT NULL,
            `subtitle` varchar(255) DEFAULT NULL,
            `description` text,
            `image_url` varchar(500) DEFAULT NULL,
            `button_text` varchar(100) DEFAULT NULL,
            `button_url` varchar(500) DEFAULT NULL,
            `sort_order` int(11) DEFAULT '0',
            `is_active` tinyint(1) DEFAULT '1',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS `settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `setting_key` varchar(100) NOT NULL,
            `setting_value` longtext,
            `setting_type` enum('text','textarea','image','json') DEFAULT 'text',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `setting_key` (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        "CREATE TABLE IF NOT EXISTS `contacts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(100) NOT NULL,
            `phone` varchar(50) DEFAULT NULL,
            `subject` varchar(255) DEFAULT NULL,
            `message` longtext NOT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `user_agent` text,
            `is_read` tinyint(1) DEFAULT '0',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];
    
    // Создаем таблицы
    foreach ($tables as $sql) {
        $pdo->exec($sql);
        echo "<p style='color: green;'>✓ Таблица создана</p>";
    }
    
    // Создаем админа
    $admin_username = 'admin';
    $admin_email = 'admin@crew-experts.com';
    $admin_password = 'admin123';  // Смените после входа!
    
    // Проверяем, есть ли уже админ
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
    $stmt->execute([$admin_username]);
    
    if ($stmt->fetchColumn() == 0) {
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$admin_username, $admin_email, $hashed_password]);
        echo "<p style='color: green;'>✓ Админ создан!</p>";
        echo "<div style='background: #ffffcc; padding: 10px; border: 1px solid #ffcc00; margin: 10px 0;'>";
        echo "<strong>Данные для входа в админку:</strong><br>";
        echo "Логин: <strong>admin</strong><br>";
        echo "Пароль: <strong>admin123</strong>";
        echo "</div>";
    } else {
        echo "<p style='color: orange;'>! Админ уже существует</p>";
    }
    
    // Создаем базовые настройки
    $settings = [
        ['site_title', 'Crew of Experts GmbH'],
        ['site_description', 'Wir schaffen Verbindungen, die Zukunft gestalten.'],
        ['contact_email', 'info@crew-experts.com'],
        ['contact_phone', '+49 123 456 7890'],
        ['contact_address', 'Musterstraße 123, 12345 Berlin'],
        ['footer_text', '© 2025 Crew of Experts GmbH. Alle Rechte vorbehalten.']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $setting) {
        $stmt->execute($setting);
    }
    echo "<p style='color: green;'>✓ Базовые настройки созданы</p>";
    
    echo "<hr>";
    echo "<h3 style='color: green;'>Установка завершена успешно!</h3>";
    echo "<p><a href='admin/' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Войти в админку</a></p>";
    echo "<p><a href='index.php'>Посмотреть сайт</a></p>";
    
    echo "<div style='background: #ffeeee; padding: 10px; border: 1px solid #ff0000; margin: 10px 0;'>";
    echo "<strong>ВАЖНО:</strong> Удалите файл setup_db.php после завершения!";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Ошибка: " . $e->getMessage() . "</p>";
}
?>
