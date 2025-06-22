<?php
header('Content-Type: application/json');

require_once '../../includes/config.php';
require_once '../../includes/database.php';

// Check authentication
if (!check_admin_auth()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $db = Database::getInstance();
    $result = $db->selectOne("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0");
    $count = $result ? $result['count'] : 0;
    
    echo json_encode([
        'success' => true,
        'count' => intval($count)
    ]);
} catch (Exception $e) {
    log_error("Unread count API error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching unread count'
    ]);
}
?>