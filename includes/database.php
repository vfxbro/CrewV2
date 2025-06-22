<?php
require_once 'config.php';

class Database {
    private $connection;
    private static $instance = null;

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            log_error("Database connection failed: " . $e->getMessage());
            die("Datenbankverbindung fehlgeschlagen");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Метод для выполнения запросов SELECT
    public function select($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            log_error("Select query failed: " . $e->getMessage());
            return false;
        }
    }

    // Метод для получения одной записи
    public function selectOne($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            log_error("SelectOne query failed: " . $e->getMessage());
            return false;
        }
    }

    // Метод для выполнения INSERT, UPDATE, DELETE
    public function execute($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            log_error("Execute query failed: " . $e->getMessage());
            return false;
        }
    }

    // Метод для получения ID последней вставленной записи
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    // Метод для начала транзакции
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    // Метод для подтверждения транзакции
    public function commit() {
        return $this->connection->commit();
    }

    // Метод для отката транзакции
    public function rollback() {
        return $this->connection->rollBack();
    }

    // Метод для создания таблиц
    public function createTables() {
        $queries = [
            // Таблица администраторов
            "CREATE TABLE IF NOT EXISTS admins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                language ENUM('de', 'en') DEFAULT 'de',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Таблица страниц
            "CREATE TABLE IF NOT EXISTS pages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                slug VARCHAR(100) UNIQUE NOT NULL,
                title VARCHAR(255) NOT NULL,
                content LONGTEXT,
                meta_title VARCHAR(255),
                meta_description TEXT,
                meta_keywords TEXT,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Таблица вакансий
            "CREATE TABLE IF NOT EXISTS jobs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description LONGTEXT,
                short_description TEXT,
                company VARCHAR(255),
                location VARCHAR(255),
                job_type ENUM('full_time', 'part_time', 'contract', 'internship') DEFAULT 'full_time',
                salary_range VARCHAR(100),
                requirements LONGTEXT,
                benefits LONGTEXT,
                contact_email VARCHAR(100),
                is_active BOOLEAN DEFAULT TRUE,
                featured BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Таблица бронирований
            "CREATE TABLE IF NOT EXISTS bookings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(50),
                service_type VARCHAR(100),
                booking_date DATE NOT NULL,
                booking_time TIME NOT NULL,
                message TEXT,
                status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Таблица настроек сайта
            "CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value LONGTEXT,
                setting_type ENUM('text', 'textarea', 'image', 'json') DEFAULT 'text',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Таблица слайдов
            "CREATE TABLE IF NOT EXISTS slides (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255),
                subtitle VARCHAR(255),
                description TEXT,
                image_url VARCHAR(500),
                button_text VARCHAR(100),
                button_url VARCHAR(500),
                sort_order INT DEFAULT 0,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Таблица контактов
            "CREATE TABLE IF NOT EXISTS contacts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(50),
                subject VARCHAR(255),
                message LONGTEXT NOT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                is_read BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];

        foreach ($queries as $query) {
            if (!$this->execute($query)) {
                return false;
            }
        }

        // Создаем админа по умолчанию
        $this->createDefaultAdmin();
        
        // Создаем базовые настройки
        $this->createDefaultSettings();

        return true;
    }

    private function createDefaultAdmin() {
        $existing = $this->selectOne("SELECT id FROM admins WHERE username = 'admin'");
        if (!$existing) {
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            $this->execute(
                "INSERT INTO admins (username, email, password) VALUES (?, ?, ?)",
                ['admin', 'admin@crew-experts.com', $password]
            );
        }
    }

    private function createDefaultSettings() {
        $defaultSettings = [
            ['site_title', 'Crew of Experts GmbH', 'text'],
            ['site_description', 'Wir schaffen Verbindungen, die Zukunft gestalten.', 'text'],
            ['contact_email', 'info@crew-experts.com', 'text'],
            ['contact_phone', '+49 123 456 7890', 'text'],
            ['contact_address', 'Musterstraße 123, 12345 Berlin', 'text'],
            ['footer_text', '© 2025 Crew of Experts GmbH. Alle Rechte vorbehalten.', 'text'],
            ['booking_enabled', '1', 'text'],
            ['jobs_per_page', '10', 'text'],
            ['cta_title', 'Bereit für den nächsten Schritt?', 'text'],
            ['cta_text', 'Vereinbaren Sie noch heute einen kostenlosen Beratungstermin und lassen Sie uns gemeinsam Ihre berufliche Zukunft gestalten.', 'text'],
            ['cta_button_text', 'Jetzt Termin buchen', 'text'],
            ['cta_button_url', SITE_URL . '/booking.php', 'text'],
            ['default_keywords', 'Personalvermittlung, Jobs, Karriere, Fachkräfte, Stellenvermittlung', 'text'],
            ['logo', 'assets/images/logo.png', 'image'],
            ['logo_white', 'assets/images/logo-white.png', 'image'],
            ['favicon', 'assets/images/favicon.ico', 'image'],
            ['apple_icon', 'assets/images/apple-touch-icon.png', 'image'],
            ['hero_image', 'assets/images/hero-image.jpg', 'image']
        ];

        foreach ($defaultSettings as $setting) {
            $existing = $this->selectOne("SELECT id FROM settings WHERE setting_key = ?", [$setting[0]]);
            if (!$existing) {
                $this->execute(
                    "INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?)",
                    $setting
                );
            }
        }
    }
}

// Инициализация базы данных
function initDatabase() {
    $db = Database::getInstance();
    return $db->createTables();
}
?>