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

if ($status_filter === 'read') {
    $where_conditions[] = "is_read = 1";
} elseif ($status_filter === 'unread') {
    $where_conditions[] = "is_read = 0";
}

if ($date_filter) {
    $where_conditions[] = "DATE(created_at) = ?";
    $params[] = $date_filter;
}

if ($search) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" AND ", $where_conditions);
$contacts = $db->select("SELECT * FROM contacts $where_clause ORDER BY created_at DESC", $params);

// Set CSV headers
$filename = 'contacts_export_' . date('Y-m-d_H-i-s') . '.csv';
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
        'Betreff',
        'Nachricht',
        'Status',
        'IP-Adresse',
        'Browser',
        'Erstellt'
    ],
    'en' => [
        'ID',
        'Name',
        'Email',
        'Phone',
        'Subject',
        'Message',
        'Status',
        'IP Address',
        'User Agent',
        'Created'
    ]
];

// Write headers
fputcsv($output, $headers[$admin_lang], ';');

// Status translations
$status_translations = [
    'de' => [
        '0' => 'Ungelesen',
        '1' => 'Gelesen'
    ],
    'en' => [
        '0' => 'Unread',
        '1' => 'Read'
    ]
];

// Write data
foreach ($contacts as $contact) {
    $row = [
        $contact['id'],
        $contact['name'],
        $contact['email'],
        $contact['phone'] ?: '',
        $contact['subject'] ?: ($admin_lang === 'de' ? 'Kein Betreff' : 'No Subject'),
        $contact['message'],
        $status_translations[$admin_lang][$contact['is_read']],
        $contact['ip_address'] ?: '',
        $contact['user_agent'] ?: '',
        formatDate($contact['created_at'], 'd.m.Y H:i')
    ];
    
    fputcsv($output, $row, ';');
}

fclose($output);
exit;
?>