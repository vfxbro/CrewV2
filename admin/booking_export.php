<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Check authentication
if (!check_admin_auth()) {
    redirect('login.php');
}

$admin_lang = $_SESSION['admin_language'] ?? 'de';
$db = Database::getInstance();

// Get filters from URL
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if ($date_filter) {
    $where_conditions[] = "booking_date = ?";
    $params[] = $date_filter;
}

if ($search) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR service_type LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" AND ", $where_conditions);
$bookings = $db->select("SELECT * FROM bookings $where_clause ORDER BY booking_date DESC, booking_time DESC", $params);

// Set CSV headers
$filename = 'bookings_export_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for proper UTF-8 encoding in Excel
fputs($output, "\xEF\xBB\xBF");

// Column headers based on language
$headers = [
    'de' => [
        'ID',
        'Name',
        'E-Mail',
        'Telefon',
        'Service-Art',
        'Datum',
        'Uhrzeit',
        'Status',
        'Nachricht',
        'Erstellt',
        'Aktualisiert'
    ],
    'en' => [
        'ID',
        'Name',
        'Email',
        'Phone',
        'Service Type',
        'Date',
        'Time',
        'Status',
        'Message',
        'Created',
        'Updated'
    ]
];

// Write headers
fputcsv($output, $headers[$admin_lang], ';');

// Status translations
$status_translations = [
    'de' => [
        'pending' => 'Ausstehend',
        'confirmed' => 'Bestätigt',
        'cancelled' => 'Storniert'
    ],
    'en' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled'
    ]
];

// Write data
foreach ($bookings as $booking) {
    $row = [
        $booking['id'],
        $booking['name'],
        $booking['email'],
        $booking['phone'] ?: '',
        $booking['service_type'],
        formatDate($booking['booking_date'], 'd.m.Y'),
        formatTime($booking['booking_time']),
        $status_translations[$admin_lang][$booking['status']] ?? $booking['status'],
        $booking['message'] ?: '',
        formatDate($booking['created_at'], 'd.m.Y H:i'),
        formatDate($booking['updated_at'], 'd.m.Y H:i')
    ];
    
    fputcsv($output, $row, ';');
}

fclose($output);
exit;
?>