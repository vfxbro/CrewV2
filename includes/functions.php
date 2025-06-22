<?php
require_once 'database.php';

// Функции для работы с настройками
function getSetting($key, $default = '') {
    $db = Database::getInstance();
    $setting = $db->selectOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
    return $setting ? $setting['setting_value'] : $default;
}

function updateSetting($key, $value, $type = 'text') {
    $db = Database::getInstance();
    $existing = $db->selectOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
    
    if ($existing) {
        return $db->execute(
            "UPDATE settings SET setting_value = ?, setting_type = ?, updated_at = NOW() WHERE setting_key = ?",
            [$value, $type, $key]
        );
    } else {
        return $db->execute(
            "INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?)",
            [$key, $value, $type]
        );
    }
}

// Функции для работы со страницами
function getPage($slug) {
    $db = Database::getInstance();
    return $db->selectOne(
        "SELECT * FROM pages WHERE slug = ? AND is_active = 1",
        [$slug]
    );
}

function getAllPages() {
    $db = Database::getInstance();
    return $db->select("SELECT * FROM pages ORDER BY title");
}

function getPageById($id) {
    $db = Database::getInstance();
    return $db->selectOne("SELECT * FROM pages WHERE id = ?", [$id]);
}

function deletePage($id) {
    $db = Database::getInstance();
    return $db->execute("DELETE FROM pages WHERE id = ?", [$id]);
}

function savePage($data) {
    $db = Database::getInstance();
    
    if (isset($data['id']) && $data['id']) {
        return $db->execute(
            "UPDATE pages SET title = ?, content = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, is_active = ?, updated_at = NOW() WHERE id = ?",
            [
                $data['title'], $data['content'], $data['meta_title'],
                $data['meta_description'], $data['meta_keywords'], $data['is_active'], $data['id']
            ]
        );
    } else {
        return $db->execute(
            "INSERT INTO pages (slug, title, content, meta_title, meta_description, meta_keywords, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['slug'], $data['title'], $data['content'],
                $data['meta_title'], $data['meta_description'], $data['meta_keywords'], $data['is_active']
            ]
        );
    }
}

// Функции для работы с вакансиями
function getAllJobs($active_only = false) {
    $db = Database::getInstance();
    $where = $active_only ? "WHERE is_active = 1" : "";
    return $db->select("SELECT * FROM jobs $where ORDER BY featured DESC, created_at DESC");
}

function getJob($id) {
    $db = Database::getInstance();
    return $db->selectOne("SELECT * FROM jobs WHERE id = ?", [$id]);
}

function getFeaturedJobs($limit = 3) {
    $db = Database::getInstance();
    return $db->select(
        "SELECT * FROM jobs WHERE is_active = 1 AND featured = 1 ORDER BY created_at DESC LIMIT ?",
        [$limit]
    );
}

function getActiveJobsCount() {
    $db = Database::getInstance();
    $result = $db->selectOne("SELECT COUNT(*) as count FROM jobs WHERE is_active = 1");
    return $result ? $result['count'] : 0;
}

function saveJob($data) {
    $db = Database::getInstance();
    
    if (isset($data['id']) && $data['id']) {
        return $db->execute(
            "UPDATE jobs SET title = ?, description = ?, short_description = ?, company = ?, location = ?, job_type = ?, salary_range = ?, requirements = ?, benefits = ?, contact_email = ?, is_active = ?, featured = ?, updated_at = NOW() WHERE id = ?",
            [
                $data['title'], $data['description'], $data['short_description'], $data['company'],
                $data['location'], $data['job_type'], $data['salary_range'], $data['requirements'],
                $data['benefits'], $data['contact_email'], $data['is_active'], $data['featured'], $data['id']
            ]
        );
    } else {
        return $db->execute(
            "INSERT INTO jobs (title, description, short_description, company, location, job_type, salary_range, requirements, benefits, contact_email, is_active, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['title'], $data['description'], $data['short_description'], $data['company'],
                $data['location'], $data['job_type'], $data['salary_range'], $data['requirements'],
                $data['benefits'], $data['contact_email'], $data['is_active'], $data['featured']
            ]
        );
    }
}

function deleteJob($id) {
    $db = Database::getInstance();
    return $db->execute("DELETE FROM jobs WHERE id = ?", [$id]);
}

// Функции для работы с бронированиями
function getAllBookings() {
    $db = Database::getInstance();
    return $db->select("SELECT * FROM bookings ORDER BY booking_date DESC, booking_time DESC");
}

function getBooking($id) {
    $db = Database::getInstance();
    return $db->selectOne("SELECT * FROM bookings WHERE id = ?", [$id]);
}

function saveBooking($data) {
    $db = Database::getInstance();
    return $db->execute(
        "INSERT INTO bookings (name, email, phone, service_type, booking_date, booking_time, message) VALUES (?, ?, ?, ?, ?, ?, ?)",
        [$data['name'], $data['email'], $data['phone'], $data['service_type'], $data['booking_date'], $data['booking_time'], $data['message']]
    );
}

function updateBookingStatus($id, $status) {
    $db = Database::getInstance();
    return $db->execute("UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?", [$status, $id]);
}

function deleteBooking($id) {
    $db = Database::getInstance();
    return $db->execute("DELETE FROM bookings WHERE id = ?", [$id]);
}

function isTimeSlotAvailable($date, $time) {
    $db = Database::getInstance();
    $existing = $db->selectOne(
        "SELECT id FROM bookings WHERE booking_date = ? AND booking_time = ? AND status != 'cancelled'",
        [$date, $time]
    );
    return !$existing;
}

function getAvailableTimeSlots($date) {
    $slots = [];
    $start_hour = BOOKING_START_HOUR;
    $end_hour = BOOKING_END_HOUR;
    $slot_duration = BOOKING_SLOT_DURATION;
    
    // Проверяем, что это не выходной (суббота или воскресенье)
    $dayOfWeek = date('N', strtotime($date));
    if ($dayOfWeek >= 6) { // 6 = суббота, 7 = воскресенье
        return [];
    }
    
    for ($hour = $start_hour; $hour < $end_hour; $hour++) {
        for ($minute = 0; $minute < 60; $minute += $slot_duration) {
            $time = sprintf('%02d:%02d:00', $hour, $minute);
            if (isTimeSlotAvailable($date, $time)) {
                $slots[] = $time;
            }
        }
    }
    
    return $slots;
}

// Функции для работы со слайдами
function getAllSlides($active_only = false) {
    $db = Database::getInstance();
    $where = $active_only ? "WHERE is_active = 1" : "";
    return $db->select("SELECT * FROM slides $where ORDER BY sort_order ASC, id ASC");
}

function getSlide($id) {
    $db = Database::getInstance();
    return $db->selectOne("SELECT * FROM slides WHERE id = ?", [$id]);
}

function saveSlide($data) {
    $db = Database::getInstance();
    
    if (isset($data['id']) && $data['id']) {
        return $db->execute(
            "UPDATE slides SET title = ?, subtitle = ?, description = ?, image_url = ?, button_text = ?, button_url = ?, sort_order = ?, is_active = ?, updated_at = NOW() WHERE id = ?",
            [
                $data['title'], $data['subtitle'], $data['description'], $data['image_url'],
                $data['button_text'], $data['button_url'], $data['sort_order'], $data['is_active'], $data['id']
            ]
        );
    } else {
        return $db->execute(
            "INSERT INTO slides (title, subtitle, description, image_url, button_text, button_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['title'], $data['subtitle'], $data['description'], $data['image_url'],
                $data['button_text'], $data['button_url'], $data['sort_order'], $data['is_active']
            ]
        );
    }
}

function deleteSlide($id) {
    $db = Database::getInstance();
    return $db->execute("DELETE FROM slides WHERE id = ?", [$id]);
}

// Функции для работы с контактами
function saveContact($data) {
    $db = Database::getInstance();
    return $db->execute(
        "INSERT INTO contacts (name, email, phone, subject, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)",
        [
            $data['name'], $data['email'], $data['phone'], $data['subject'],
            $data['message'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']
        ]
    );
}

function getAllContacts() {
    $db = Database::getInstance();
    return $db->select("SELECT * FROM contacts ORDER BY created_at DESC");
}

function markContactAsRead($id) {
    $db = Database::getInstance();
    return $db->execute("UPDATE contacts SET is_read = 1 WHERE id = ?", [$id]);
}

// Функция для отправки email
function sendEmail($to, $subject, $message, $from = null) {
    if (!$from) {
        $from = getSetting('contact_email', ADMIN_EMAIL);
    }
    
    $headers = [
        'From: ' . $from,
        'Reply-To: ' . $from,
        'Content-Type: text/html; charset=UTF-8',
        'MIME-Version: 1.0'
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

// Функция для загрузки файлов
function uploadFile($file, $directory = 'uploads/') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed_mimes = [
        'image/jpeg', 'image/png', 'image/gif',
        'image/webp', 'image/svg+xml', 'image/x-icon'
    ];
    if (!in_array($mime, $allowed_mimes)) {
        return false;
    }

    $upload_dir = UPLOAD_PATH . basename($directory);
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safe_exts = ['jpg','jpeg','png','gif','webp','svg','ico'];
    if (!in_array($extension, $safe_exts)) {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/x-icon' => 'ico'
        ];
        $extension = $map[$mime] ?? 'dat';
    }

    $file_name = uniqid('', true) . '.' . $extension;
    $file_path = $upload_dir . '/' . $file_name;

    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return basename($directory) . '/' . $file_name;
    }

    return false;
}

// Функция для форматирования даты
function formatDate($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

// Функция для форматирования времени
function formatTime($time, $format = 'H:i') {
    return date($format, strtotime($time));
}

// Функция для получения названий дней недели на немецком
function getGermanDayName($date) {
    $days = [
        1 => 'Montag',
        2 => 'Dienstag',
        3 => 'Mittwoch',
        4 => 'Donnerstag',
        5 => 'Freitag',
        6 => 'Samstag',
        7 => 'Sonntag'
    ];
    
    $dayNumber = date('N', strtotime($date));
    return $days[$dayNumber];
}

// Функция для получения названий месяцев на немецком
function getGermanMonthName($date) {
    $months = [
        1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April',
        5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember'
    ];
    
    $monthNumber = date('n', strtotime($date));
    return $months[$monthNumber];
}

// Функция для проверки валидности email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Функция для очистки HTML
function cleanHtml($html) {
    $allowed_tags = '<p><br><strong><b><em><i><u><ul><ol><li><a><h1><h2><h3><h4><h5><h6>';
    return strip_tags($html, $allowed_tags);
}

// Функция для создания превью текста
function createExcerpt($text, $length = 150) {
    $text = strip_tags($text);
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $last_space = strrpos($text, ' ');
    if ($last_space !== false) {
        $text = substr($text, 0, $last_space);
    }
    
    return $text . '...';
}

// Функция для генерации slug из текста
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[äöüß]/u', ['ae', 'oe', 'ue', 'ss'], $text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// Функция для автоматической генерации ключевых слов из текста
function generateKeywords($text, $limit = 10) {
    $text = mb_strtolower(strip_tags($text));
    $text = preg_replace('/[^\p{L}\p{Nd}\s]+/u', ' ', $text);
    $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    $stopwords = [
        'und','oder','der','die','das','ein','eine','mit','auf','im','in','den',
        'zu','für','von','ist','sie','er','wir','ich'
    ];
    $freq = [];
    foreach ($words as $word) {
        if (in_array($word, $stopwords) || mb_strlen($word) < 3) {
            continue;
        }
        $freq[$word] = ($freq[$word] ?? 0) + 1;
    }
    arsort($freq);
    $keywords = array_slice(array_keys($freq), 0, $limit);
    return implode(', ', $keywords);
}

// Функция для пагинации
function paginate($total_items, $items_per_page, $current_page = 1) {
    $total_pages = ceil($total_items / $items_per_page);
    $offset = ($current_page - 1) * $items_per_page;
    
    return [
        'total_items' => $total_items,
        'items_per_page' => $items_per_page,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages,
        'prev_page' => $current_page - 1,
        'next_page' => $current_page + 1
    ];
}
?>
