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

// Handle actions
$action = $_GET['action'] ?? 'list';
$contact_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Ungültiger Sicherheits-Token.';
    } elseif (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'mark_read':
                if ($contact_id && markContactAsRead($contact_id)) {
                    $message = $admin_lang === 'de' 
                        ? 'Nachricht als gelesen markiert' 
                        : 'Message marked as read';
                } else {
                    $error = $admin_lang === 'de' 
                        ? 'Fehler beim Markieren der Nachricht' 
                        : 'Error marking message';
                }
                break;
                
            case 'delete':
                if ($contact_id) {
                    $result = $db->execute("DELETE FROM contacts WHERE id = ?", [$contact_id]);
                    if ($result) {
                        $message = $admin_lang === 'de' 
                            ? 'Nachricht wurde erfolgreich gelöscht' 
                            : 'Message deleted successfully';
                        $action = 'list';
                    } else {
                        $error = $admin_lang === 'de' 
                            ? 'Fehler beim Löschen der Nachricht' 
                            : 'Error deleting message';
                    }
                }
                break;
                
            case 'bulk_action':
                $selected_ids = $_POST['selected_contacts'] ?? [];
                $bulk_action = $_POST['bulk_action'] ?? '';
                
                if (!empty($selected_ids) && $bulk_action) {
                    $success_count = 0;
                    foreach ($selected_ids as $id) {
                        $id = intval($id);
                        if ($bulk_action === 'mark_read') {
                            if (markContactAsRead($id)) $success_count++;
                        } elseif ($bulk_action === 'mark_unread') {
                            if ($db->execute("UPDATE contacts SET is_read = 0 WHERE id = ?", [$id])) $success_count++;
                        } elseif ($bulk_action === 'delete') {
                            if ($db->execute("DELETE FROM contacts WHERE id = ?", [$id])) $success_count++;
                        }
                    }
                    
                    $message = $admin_lang === 'de' 
                        ? "$success_count Nachrichten wurden aktualisiert" 
                        : "$success_count messages updated";
                }
                break;
                
            case 'reply':
                $reply_email = trim($_POST['reply_email'] ?? '');
                $reply_subject = trim($_POST['reply_subject'] ?? '');
                $reply_message = trim($_POST['reply_message'] ?? '');
                
                if ($reply_email && $reply_subject && $reply_message) {
                    try {
                        if (sendEmail($reply_email, $reply_subject, $reply_message)) {
                            $message = $admin_lang === 'de' 
                                ? 'Antwort wurde erfolgreich gesendet' 
                                : 'Reply sent successfully';
                            
                            // Mark original message as read
                            if ($contact_id) {
                                markContactAsRead($contact_id);
                            }
                        } else {
                            $error = $admin_lang === 'de' 
                                ? 'Fehler beim Senden der Antwort' 
                                : 'Error sending reply';
                        }
                    } catch (Exception $e) {
                        log_error("Failed to send reply: " . $e->getMessage());
                        $error = $admin_lang === 'de' 
                            ? 'Fehler beim Senden der Antwort' 
                            : 'Error sending reply';
                    }
                } else {
                    $error = $admin_lang === 'de' 
                        ? 'Alle Felder sind erforderlich' 
                        : 'All fields are required';
                }
                break;
        }
    }
}

// Get data based on action
$contact = null;
$contacts = [];

if ($action === 'view' && $contact_id) {
    $contact = $db->selectOne("SELECT * FROM contacts WHERE id = ?", [$contact_id]);
    if (!$contact) {
        $action = 'list';
        $error = $admin_lang === 'de' ? 'Nachricht nicht gefunden' : 'Message not found';
    } else {
        // Mark as read when viewing
        if (!$contact['is_read']) {
            markContactAsRead($contact_id);
            $contact['is_read'] = 1;
        }
    }
} elseif ($action === 'list') {
    // Get contacts with filters
    $status_filter = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';
    $date_filter = $_GET['date'] ?? '';
    
    $where_conditions = [];
    $params = [];
    
    if ($status_filter === 'read') {
        $where_conditions[] = "is_read = 1";
    } elseif ($status_filter === 'unread') {
        $where_conditions[] = "is_read = 0";
    }
    
    if ($search) {
        $where_conditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
        $search_param = '%' . $search . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    if ($date_filter) {
        $where_conditions[] = "DATE(created_at) = ?";
        $params[] = $date_filter;
    }
    
    $where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" AND ", $where_conditions);
    $contacts = $db->select("SELECT * FROM contacts $where_clause ORDER BY created_at DESC", $params);
}

// Get statistics
$stats = [
    'total' => $db->selectOne("SELECT COUNT(*) as count FROM contacts")['count'] ?? 0,
    'unread' => $db->selectOne("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0")['count'] ?? 0,
    'read' => $db->selectOne("SELECT COUNT(*) as count FROM contacts WHERE is_read = 1")['count'] ?? 0,
    'today' => $db->selectOne("SELECT COUNT(*) as count FROM contacts WHERE DATE(created_at) = CURDATE()")['count'] ?? 0,
    'this_week' => $db->selectOne("SELECT COUNT(*) as count FROM contacts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")['count'] ?? 0
];

// Translations
$translations = [
    'de' => [
        'contacts' => 'Kontakte',
        'contact_list' => 'Kontaktliste',
        'view_contact' => 'Nachricht ansehen',
        'name' => 'Name',
        'email' => 'E-Mail',
        'phone' => 'Telefon',
        'subject' => 'Betreff',
        'message' => 'Nachricht',
        'status' => 'Status',
        'actions' => 'Aktionen',
        'edit' => 'Bearbeiten',
        'delete' => 'Löschen',
        'view' => 'Ansehen',
        'reply' => 'Antworten',
        'created' => 'Erstellt',
        'read' => 'Gelesen',
        'unread' => 'Ungelesen',
        'mark_read' => 'Als gelesen markieren',
        'mark_unread' => 'Als ungelesen markieren',
        'confirm_delete' => 'Sind Sie sicher, dass Sie diese Nachricht löschen möchten?',
        'search' => 'Suchen...',
        'no_contacts' => 'Keine Nachrichten gefunden',
        'filter_status' => 'Nach Status filtern',
        'filter_date' => 'Nach Datum filtern',
        'all_status' => 'Alle Status',
        'total_contacts' => 'Nachrichten gesamt',
        'unread_contacts' => 'Ungelesene Nachrichten',
        'read_contacts' => 'Gelesene Nachrichten',
        'todays_contacts' => 'Heutige Nachrichten',
        'week_contacts' => 'Diese Woche',
        'bulk_actions' => 'Massenaktionen',
        'select_action' => 'Aktion wählen',
        'apply' => 'Anwenden',
        'export' => 'Exportieren',
        'back_to_list' => 'Zurück zur Liste',
        'reply_to' => 'Antworten an',
        'reply_subject' => 'Antwort Betreff',
        'reply_message' => 'Antwort Nachricht',
        'send_reply' => 'Antwort senden',
        'cancel' => 'Abbrechen',
        'ip_address' => 'IP-Adresse',
        'user_agent' => 'Browser',
        'contact_details' => 'Kontaktdetails',
        'original_message' => 'Ursprüngliche Nachricht',
        'no_subject' => 'Kein Betreff'
    ],
    'en' => [
        'contacts' => 'Contacts',
        'contact_list' => 'Contact List',
        'view_contact' => 'View Message',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'subject' => 'Subject',
        'message' => 'Message',
        'status' => 'Status',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'reply' => 'Reply',
        'created' => 'Created',
        'read' => 'Read',
        'unread' => 'Unread',
        'mark_read' => 'Mark as read',
        'mark_unread' => 'Mark as unread',
        'confirm_delete' => 'Are you sure you want to delete this message?',
        'search' => 'Search...',
        'no_contacts' => 'No messages found',
        'filter_status' => 'Filter by status',
        'filter_date' => 'Filter by date',
        'all_status' => 'All Status',
        'total_contacts' => 'Total Messages',
        'unread_contacts' => 'Unread Messages',
        'read_contacts' => 'Read Messages',
        'todays_contacts' => 'Today\'s Messages',
        'week_contacts' => 'This Week',
        'bulk_actions' => 'Bulk Actions',
        'select_action' => 'Select Action',
        'apply' => 'Apply',
        'export' => 'Export',
        'back_to_list' => 'Back to List',
        'reply_to' => 'Reply to',
        'reply_subject' => 'Reply Subject',
        'reply_message' => 'Reply Message',
        'send_reply' => 'Send Reply',
        'cancel' => 'Cancel',
        'ip_address' => 'IP Address',
        'user_agent' => 'Browser',
        'contact_details' => 'Contact Details',
        'original_message' => 'Original Message',
        'no_subject' => 'No Subject'
    ]
];

$t = $translations[$admin_lang];

include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
            <!-- Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 fw-bold">
                    <i class="fas fa-envelope text-primary me-2"></i>
                    <?php echo $t['contacts']; ?>
                    <?php if ($stats['unread'] > 0): ?>
                        <span class="badge bg-danger ms-2"><?php echo $stats['unread']; ?></span>
                    <?php endif; ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <?php if ($action === 'view'): ?>
                        <a href="contacts.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i><?php echo $t['back_to_list']; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo escape($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo escape($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($action === 'list'): ?>
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card admin-card stats-card">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-envelope fa-2x text-primary"></i>
                                </div>
                                <div class="stat-number h4 fw-bold"><?php echo $stats['total']; ?></div>
                                <div class="stat-label text-muted small"><?php echo $t['total_contacts']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card admin-card stats-card danger">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-envelope-open fa-2x text-danger"></i>
                                </div>
                                <div class="stat-number h4 fw-bold"><?php echo $stats['unread']; ?></div>
                                <div class="stat-label text-muted small"><?php echo $t['unread_contacts']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card admin-card stats-card success">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                                <div class="stat-number h4 fw-bold"><?php echo $stats['read']; ?></div>
                                <div class="stat-label text-muted small"><?php echo $t['read_contacts']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card admin-card stats-card info">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-calendar-day fa-2x text-info"></i>
                                </div>
                                <div class="stat-number h4 fw-bold"><?php echo $stats['today']; ?></div>
                                <div class="stat-label text-muted small"><?php echo $t['todays_contacts']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card admin-card stats-card warning">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-calendar-week fa-2x text-warning"></i>
                                </div>
                                <div class="stat-number h4 fw-bold"><?php echo $stats['this_week']; ?></div>
                                <div class="stat-label text-muted small"><?php echo $t['week_contacts']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card admin-card">
                            <div class="card-body">
                                <form method="GET" class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label for="search" class="form-label small fw-bold"><?php echo $t['search']; ?></label>
                                        <input type="text" class="form-control" id="search" name="search" 
                                               value="<?php echo escape($_GET['search'] ?? ''); ?>"
                                               placeholder="Name, E-Mail, Betreff, Nachricht...">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label for="status" class="form-label small fw-bold"><?php echo $t['filter_status']; ?></label>
                                        <select class="form-select" id="status" name="status">
                                            <option value=""><?php echo $t['all_status']; ?></option>
                                            <option value="unread" <?php echo ($_GET['status'] ?? '') === 'unread' ? 'selected' : ''; ?>><?php echo $t['unread']; ?></option>
                                            <option value="read" <?php echo ($_GET['status'] ?? '') === 'read' ? 'selected' : ''; ?>><?php echo $t['read']; ?></option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label for="date" class="form-label small fw-bold"><?php echo $t['filter_date']; ?></label>
                                        <input type="date" class="form-control" id="date" name="date" 
                                               value="<?php echo escape($_GET['date'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-1"></i>Filter
                                        </button>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="btn-group w-100">
                                            <a href="contacts.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-refresh me-1"></i>Reset
                                            </a>
                                            <button type="button" class="btn btn-outline-success" onclick="exportContacts()">
                                                <i class="fas fa-download me-1"></i><?php echo $t['export']; ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contacts List -->
                <div class="row">
                    <div class="col-12">
                        <div class="card admin-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    <?php echo $t['contact_list']; ?>
                                </h5>
                                <div class="card-tools">
                                    <small class="text-muted">
                                        <?php echo count($contacts); ?> 
                                        <?php echo $admin_lang === 'de' ? 'Nachrichten gefunden' : 'messages found'; ?>
                                    </small>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($contacts)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted"><?php echo $t['no_contacts']; ?></h5>
                                    </div>
                                <?php else: ?>
                                    <form id="bulkForm" method="POST">
                                        <input type="hidden" name="action" value="bulk_action">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        
                                        <!-- Bulk Actions -->
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox" id="selectAll" class="form-check-input me-2">
                                                <label for="selectAll" class="form-check-label small fw-bold">
                                                    <?php echo $admin_lang === 'de' ? 'Alle auswählen' : 'Select All'; ?>
                                                </label>
                                            </div>
                                            
                                            <div class="d-flex align-items-center">
                                                <label class="form-label small fw-bold me-2 mb-0"><?php echo $t['bulk_actions']; ?>:</label>
                                                <select name="bulk_action" class="form-select form-select-sm me-2" style="width: auto;">
                                                    <option value=""><?php echo $t['select_action']; ?></option>
                                                    <option value="mark_read"><?php echo $t['mark_read']; ?></option>
                                                    <option value="mark_unread"><?php echo $t['mark_unread']; ?></option>
                                                    <option value="delete"><?php echo $t['delete']; ?></option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary" 
                                                        onclick="return confirm('<?php echo $admin_lang === 'de' ? 'Ausgewählte Aktionen ausführen?' : 'Execute selected actions?'; ?>')">
                                                    <?php echo $t['apply']; ?>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" id="contactsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="30"><input type="checkbox" class="form-check-input" id="selectAllHeader"></th>
                                                        <th><?php echo $t['name']; ?></th>
                                                        <th><?php echo $t['subject']; ?></th>
                                                        <th><?php echo $t['message']; ?></th>
                                                        <th><?php echo $t['status']; ?></th>
                                                        <th><?php echo $t['created']; ?></th>
                                                        <th class="text-end"><?php echo $t['actions']; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($contacts as $contact): ?>
                                                        <tr class="<?php echo !$contact['is_read'] ? 'table-warning' : ''; ?>">
                                                            <td>
                                                                <input type="checkbox" name="selected_contacts[]" 
                                                                       value="<?php echo $contact['id']; ?>" 
                                                                       class="form-check-input contact-checkbox">
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <?php if (!$contact['is_read']): ?>
                                                                        <i class="fas fa-circle text-warning me-2" style="font-size: 8px;" title="<?php echo $t['unread']; ?>"></i>
                                                                    <?php endif; ?>
                                                                    <div>
                                                                        <div class="fw-bold"><?php echo escape($contact['name']); ?></div>
                                                                        <small class="text-muted"><?php echo escape($contact['email']); ?></small>
                                                                        <?php if ($contact['phone']): ?>
                                                                            <br><small class="text-muted">
                                                                                <i class="fas fa-phone me-1"></i><?php echo escape($contact['phone']); ?>
                                                                            </small>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="fw-bold">
                                                                    <?php echo escape($contact['subject'] ?: $t['no_subject']); ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="message-preview">
                                                                    <?php echo escape(createExcerpt($contact['message'], 100)); ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge <?php echo $contact['is_read'] ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                                                    <?php echo $contact['is_read'] ? $t['read'] : $t['unread']; ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">
                                                                    <?php echo formatDate($contact['created_at'], 'd.m.Y H:i'); ?>
                                                                </small>
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="btn-group" role="group">
                                                                    <a href="?action=view&id=<?php echo $contact['id']; ?>" 
                                                                       class="btn btn-sm btn-outline-primary" 
                                                                       title="<?php echo $t['view']; ?>">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-info" 
                                                                            data-bs-toggle="modal" 
                                                                            data-bs-target="#replyModal<?php echo $contact['id']; ?>" 
                                                                            title="<?php echo $t['reply']; ?>">
                                                                        <i class="fas fa-reply"></i>
                                                                    </button>
                                                                    <form method="POST" class="d-inline"
                                                                          onsubmit="return confirm('<?php echo $t['confirm_delete']; ?>')">
                                                                        <input type="hidden" name="action" value="delete">
                                                                        <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                                title="<?php echo $t['delete']; ?>">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        
                                                        <!-- Reply Modal -->
                                                        <div class="modal fade" id="replyModal<?php echo $contact['id']; ?>" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">
                                                                            <i class="fas fa-reply me-2"></i>
                                                                            <?php echo $t['reply_to']; ?> <?php echo escape($contact['name']); ?>
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <form method="POST">
                                                                        <input type="hidden" name="action" value="reply">
                                                                        <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                                        <div class="modal-body">
                                                                            <div class="row g-3">
                                                                                <div class="col-12">
                                                                                    <label for="reply_email" class="form-label fw-bold"><?php echo $t['email']; ?></label>
                                                                                    <input type="email" class="form-control" name="reply_email" 
                                                                                           value="<?php echo escape($contact['email']); ?>" readonly>
                                                                                </div>
                                                                                
                                                                                <div class="col-12">
                                                                                    <label for="reply_subject" class="form-label fw-bold"><?php echo $t['reply_subject']; ?></label>
                                                                                    <input type="text" class="form-control" name="reply_subject" 
                                                                                           value="Re: <?php echo escape($contact['subject'] ?: $t['no_subject']); ?>" required>
                                                                                </div>
                                                                                
                                                                                <div class="col-12">
                                                                                    <label for="reply_message" class="form-label fw-bold"><?php echo $t['reply_message']; ?></label>
                                                                                    <textarea class="form-control" name="reply_message" rows="8" required 
                                                                                              placeholder="<?php echo $admin_lang === 'de' ? 'Ihre Antwort...' : 'Your reply...'; ?>"></textarea>
                                                                                </div>
                                                                                
                                                                                <div class="col-12">
                                                                                    <div class="card bg-light">
                                                                                        <div class="card-header">
                                                                                            <h6 class="card-title mb-0"><?php echo $t['original_message']; ?></h6>
                                                                                        </div>
                                                                                        <div class="card-body">
                                                                                            <strong><?php echo $t['subject']; ?>:</strong> <?php echo escape($contact['subject'] ?: $t['no_subject']); ?><br>
                                                                                            <strong><?php echo $t['created']; ?>:</strong> <?php echo formatDate($contact['created_at'], 'd.m.Y H:i'); ?><br><br>
                                                                                            <?php echo nl2br(escape($contact['message'])); ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                                <?php echo $t['cancel']; ?>
                                                                            </button>
                                                                            <button type="submit" class="btn btn-primary">
                                                                                <i class="fas fa-paper-plane me-2"></i><?php echo $t['send_reply']; ?>
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($action === 'view' && $contact): ?>
                <!-- Contact Detail View -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card admin-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-envelope-open me-2"></i>
                                    <?php echo $t['contact_details']; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <strong><?php echo $t['name']; ?>:</strong><br>
                                        <span class="fs-5"><?php echo escape($contact['name']); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong><?php echo $t['email']; ?>:</strong><br>
                                        <a href="mailto:<?php echo $contact['email']; ?>" class="fs-5"><?php echo escape($contact['email']); ?></a>
                                    </div>
                                    <?php if ($contact['phone']): ?>
                                    <div class="col-md-6">
                                        <strong><?php echo $t['phone']; ?>:</strong><br>
                                        <a href="tel:<?php echo $contact['phone']; ?>" class="fs-5"><?php echo escape($contact['phone']); ?></a>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-md-6">
                                        <strong><?php echo $t['created']; ?>:</strong><br>
                                        <span class="fs-5"><?php echo formatDate($contact['created_at'], 'd.m.Y H:i'); ?></span>
                                    </div>
                                </div>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <strong><?php echo $t['subject']; ?>:</strong><br>
                                        <h4 class="text-primary"><?php echo escape($contact['subject'] ?: $t['no_subject']); ?></h4>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <strong><?php echo $t['message']; ?>:</strong><br>
                                        <div class="message-content bg-light p-4 rounded mt-2">
                                            <?php echo nl2br(escape($contact['message'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge <?php echo $contact['is_read'] ? 'bg-success' : 'bg-warning text-dark'; ?> me-2">
                                            <?php echo $contact['is_read'] ? $t['read'] : $t['unread']; ?>
                                        </span>
                                        <small class="text-muted">
                                            <?php echo $t['ip_address']; ?>: <?php echo escape($contact['ip_address'] ?: 'N/A'); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-primary" 
                                                data-bs-toggle="modal" data-bs-target="#replyModalDetail">
                                            <i class="fas fa-reply me-2"></i><?php echo $t['reply']; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Actions -->
                        <div class="card admin-card mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0"><?php echo $t['actions']; ?></h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#replyModalDetail">
                                        <i class="fas fa-reply me-2"></i><?php echo $t['reply']; ?>
                                    </button>
                                    
                                    <?php if (!$contact['is_read']): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-check me-2"></i><?php echo $t['mark_read']; ?>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" class="d-inline"
                                          onsubmit="return confirm('<?php echo $t['confirm_delete']; ?>')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            <i class="fas fa-trash me-2"></i><?php echo $t['delete']; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Technical Details -->
                        <div class="card admin-card">
                            <div class="card-header">
                                <h6 class="card-title mb-0"><?php echo $admin_lang === 'de' ? 'Technische Details' : 'Technical Details'; ?></h6>
                            </div>
                            <div class="card-body">
                                <div class="technical-details">
                                    <div class="detail-item mb-2">
                                        <strong><?php echo $t['ip_address']; ?>:</strong><br>
                                        <small class="text-muted"><?php echo escape($contact['ip_address'] ?: 'N/A'); ?></small>
                                    </div>
                                    
                                    <div class="detail-item mb-2">
                                        <strong><?php echo $t['user_agent']; ?>:</strong><br>
                                        <small class="text-muted"><?php echo escape(createExcerpt($contact['user_agent'] ?: 'N/A', 80)); ?></small>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <strong>ID:</strong><br>
                                        <small class="text-muted">#<?php echo str_pad($contact['id'], 6, '0', STR_PAD_LEFT); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reply Modal for Detail View -->
                <div class="modal fade" id="replyModalDetail" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-reply me-2"></i>
                                    <?php echo $t['reply_to']; ?> <?php echo escape($contact['name']); ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="reply">
                                <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="reply_email_detail" class="form-label fw-bold"><?php echo $t['email']; ?></label>
                                            <input type="email" class="form-control" name="reply_email" 
                                                   value="<?php echo escape($contact['email']); ?>" readonly>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label for="reply_subject_detail" class="form-label fw-bold"><?php echo $t['reply_subject']; ?></label>
                                            <input type="text" class="form-control" name="reply_subject" 
                                                   value="Re: <?php echo escape($contact['subject'] ?: $t['no_subject']); ?>" required>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label for="reply_message_detail" class="form-label fw-bold"><?php echo $t['reply_message']; ?></label>
                                            <textarea class="form-control" name="reply_message" rows="10" required 
                                                      placeholder="<?php echo $admin_lang === 'de' ? 'Ihre Antwort...' : 'Your reply...'; ?>"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <?php echo $t['cancel']; ?>
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i><?php echo $t['send_reply']; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
// Bulk selection functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const checkboxes = document.querySelectorAll('.contact-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            if (selectAllHeader) selectAllHeader.checked = this.checked;
        });
    }
    
    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            if (selectAll) selectAll.checked = this.checked;
        });
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.contact-checkbox:checked').length;
            const totalCount = checkboxes.length;
            
            if (selectAll) {
                selectAll.checked = checkedCount === totalCount;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < totalCount;
            }
            
            if (selectAllHeader) {
                selectAllHeader.checked = checkedCount === totalCount;
                selectAllHeader.indeterminate = checkedCount > 0 && checkedCount < totalCount;
            }
        });
    });
});

// Export functionality
function exportContacts() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.open('contacts_export.php?' + params.toString());
}

// Auto-refresh unread count (every 30 seconds)
setInterval(function() {
    fetch('api/unread_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.querySelector('.badge');
                if (badge && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                } else if (badge && data.count === 0) {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.log('Error checking unread count:', error));
}, 30000);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Press 'r' to reply to the currently viewed message
    if (e.key === 'r' && !e.ctrlKey && !e.altKey && !e.shiftKey) {
        const replyButton = document.querySelector('[data-bs-target="#replyModalDetail"]');
        if (replyButton && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            replyButton.click();
        }
    }
    
    // Press 'm' to mark as read
    if (e.key === 'm' && !e.ctrlKey && !e.altKey && !e.shiftKey) {
        const markReadButton = document.querySelector('button[type="submit"]:has(i.fa-check)');
        if (markReadButton && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            markReadButton.click();
        }
    }
    
    // Press 'Esc' to go back to list
    if (e.key === 'Escape') {
        const backButton = document.querySelector('a[href="contacts.php"]');
        if (backButton && window.location.search.includes('action=view')) {
            window.location.href = 'contacts.php';
        }
    }
});

// Message preview truncation
document.querySelectorAll('.message-preview').forEach(element => {
    const text = element.textContent;
    if (text.length > 100) {
        element.title = text; // Show full text on hover
    }
});

// Auto-mark as read when viewing (already handled in PHP, but could be enhanced with AJAX)
</script>

<?php include 'includes/admin_footer.php'; ?>