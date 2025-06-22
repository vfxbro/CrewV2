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
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
            case 'edit':
                $booking_data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'service_type' => trim($_POST['service_type'] ?? ''),
                    'booking_date' => trim($_POST['booking_date'] ?? ''),
                    'booking_time' => trim($_POST['booking_time'] ?? ''),
                    'message' => trim($_POST['message'] ?? ''),
                    'status' => trim($_POST['status'] ?? 'pending')
                ];
                
                // Validation
                $errors = [];
                if (empty($booking_data['name'])) {
                    $errors[] = $admin_lang === 'de' ? 'Name ist erforderlich' : 'Name is required';
                }
                if (empty($booking_data['email']) || !validateEmail($booking_data['email'])) {
                    $errors[] = $admin_lang === 'de' ? 'Gültige E-Mail ist erforderlich' : 'Valid email is required';
                }
                if (empty($booking_data['booking_date'])) {
                    $errors[] = $admin_lang === 'de' ? 'Datum ist erforderlich' : 'Date is required';
                }
                if (empty($booking_data['booking_time'])) {
                    $errors[] = $admin_lang === 'de' ? 'Uhrzeit ist erforderlich' : 'Time is required';
                }
                
                // Check for conflicts (except when editing current booking)
                if (empty($errors)) {
                    $conflict_query = "SELECT id FROM bookings WHERE booking_date = ? AND booking_time = ? AND status != 'cancelled'";
                    $conflict_params = [$booking_data['booking_date'], $booking_data['booking_time']];
                    
                    if ($_POST['action'] === 'edit' && $booking_id) {
                        $conflict_query .= " AND id != ?";
                        $conflict_params[] = $booking_id;
                    }
                    
                    $conflict = $db->selectOne($conflict_query, $conflict_params);
                    if ($conflict) {
                        $errors[] = $admin_lang === 'de' 
                            ? 'Dieser Termin ist bereits vergeben' 
                            : 'This time slot is already taken';
                    }
                }
                
                if (empty($errors)) {
                    if ($_POST['action'] === 'edit' && $booking_id) {
                        $result = $db->execute(
                            "UPDATE bookings SET name = ?, email = ?, phone = ?, service_type = ?, booking_date = ?, booking_time = ?, message = ?, status = ?, updated_at = NOW() WHERE id = ?",
                            [
                                $booking_data['name'], $booking_data['email'], $booking_data['phone'],
                                $booking_data['service_type'], $booking_data['booking_date'], $booking_data['booking_time'],
                                $booking_data['message'], $booking_data['status'], $booking_id
                            ]
                        );
                    } else {
                        $result = saveBooking($booking_data);
                        if ($result) {
                            $booking_id = $db->lastInsertId();
                            $action = 'edit';
                        }
                    }
                    
                    if ($result) {
                        $message = $admin_lang === 'de' 
                            ? 'Buchung wurde erfolgreich gespeichert' 
                            : 'Booking saved successfully';
                    } else {
                        $error = $admin_lang === 'de' 
                            ? 'Fehler beim Speichern der Buchung' 
                            : 'Error saving booking';
                    }
                } else {
                    $error = implode(', ', $errors);
                }
                break;
                
            case 'delete':
                if ($booking_id && deleteBooking($booking_id)) {
                    $message = $admin_lang === 'de' 
                        ? 'Buchung wurde erfolgreich gelöscht' 
                        : 'Booking deleted successfully';
                    $action = 'list';
                } else {
                    $error = $admin_lang === 'de' 
                        ? 'Fehler beim Löschen der Buchung' 
                        : 'Error deleting booking';
                }
                break;
                
            case 'update_status':
                if ($booking_id && isset($_POST['status'])) {
                    $new_status = $_POST['status'];
                    if (updateBookingStatus($booking_id, $new_status)) {
                        $message = $admin_lang === 'de' 
                            ? 'Status wurde aktualisiert' 
                            : 'Status updated';
                        
                        // Send email notification if confirmed
                        if ($new_status === 'confirmed') {
                            $booking = getBooking($booking_id);
                            if ($booking) {
                                $subject = $admin_lang === 'de' 
                                    ? 'Terminbestätigung - ' . getSetting('site_title', 'Crew of Experts')
                                    : 'Appointment Confirmation - ' . getSetting('site_title', 'Crew of Experts');
                                    
                                $email_body = $admin_lang === 'de' ? "
                                    <h2>Terminbestätigung</h2>
                                    <p>Liebe/r {$booking['name']},</p>
                                    <p>Ihr Termin wurde bestätigt:</p>
                                    <ul>
                                        <li><strong>Service:</strong> {$booking['service_type']}</li>
                                        <li><strong>Datum:</strong> " . formatDate($booking['booking_date']) . "</li>
                                        <li><strong>Uhrzeit:</strong> " . formatTime($booking['booking_time']) . "</li>
                                    </ul>
                                    <p>Wir freuen uns auf Sie!</p>
                                " : "
                                    <h2>Appointment Confirmation</h2>
                                    <p>Dear {$booking['name']},</p>
                                    <p>Your appointment has been confirmed:</p>
                                    <ul>
                                        <li><strong>Service:</strong> {$booking['service_type']}</li>
                                        <li><strong>Date:</strong> " . formatDate($booking['booking_date']) . "</li>
                                        <li><strong>Time:</strong> " . formatTime($booking['booking_time']) . "</li>
                                    </ul>
                                    <p>We look forward to seeing you!</p>
                                ";
                                
                                try {
                                    sendEmail($booking['email'], $subject, $email_body);
                                } catch (Exception $e) {
                                    log_error("Failed to send confirmation email: " . $e->getMessage());
                                }
                            }
                        }
                    } else {
                        $error = $admin_lang === 'de' 
                            ? 'Fehler beim Aktualisieren des Status' 
                            : 'Error updating status';
                    }
                }
                break;
                
            case 'bulk_action':
                $selected_ids = $_POST['selected_bookings'] ?? [];
                $bulk_action = $_POST['bulk_action'] ?? '';
                
                if (!empty($selected_ids) && $bulk_action) {
                    $success_count = 0;
                    foreach ($selected_ids as $id) {
                        $id = intval($id);
                        if ($bulk_action === 'delete') {
                            if (deleteBooking($id)) $success_count++;
                        } elseif (in_array($bulk_action, ['pending', 'confirmed', 'cancelled'])) {
                            if (updateBookingStatus($id, $bulk_action)) $success_count++;
                        }
                    }
                    
                    $message = $admin_lang === 'de' 
                        ? "$success_count Buchungen wurden aktualisiert" 
                        : "$success_count bookings updated";
                }
                break;
        }
    }
}

// Get data based on action
$booking = null;
$bookings = [];

if ($action === 'edit' && $booking_id) {
    $booking = getBooking($booking_id);
    if (!$booking) {
        $action = 'list';
        $error = $admin_lang === 'de' ? 'Buchung nicht gefunden' : 'Booking not found';
    }
} elseif ($action === 'list') {
    // Get bookings with filters
    $status_filter = $_GET['status'] ?? '';
    $date_filter = $_GET['date'] ?? '';
    $search = $_GET['search'] ?? '';
    
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
}

// Get statistics for dashboard
$stats = [
    'total' => $db->selectOne("SELECT COUNT(*) as count FROM bookings")['count'] ?? 0,
    'pending' => $db->selectOne("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")['count'] ?? 0,
    'confirmed' => $db->selectOne("SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed'")['count'] ?? 0,
    'cancelled' => $db->selectOne("SELECT COUNT(*) as count FROM bookings WHERE status = 'cancelled'")['count'] ?? 0,
    'today' => $db->selectOne("SELECT COUNT(*) as count FROM bookings WHERE booking_date = CURDATE()")['count'] ?? 0
];

// Translations
$translations = [
    'de' => [
        'bookings' => 'Buchungen',
        'add_booking' => 'Neue Buchung hinzufügen',
        'edit_booking' => 'Buchung bearbeiten',
        'booking_list' => 'Buchungsliste',
        'name' => 'Name',
        'email' => 'E-Mail',
        'phone' => 'Telefon',
        'service_type' => 'Service-Art',
        'booking_date' => 'Datum',
        'booking_time' => 'Uhrzeit',
        'message' => 'Nachricht',
        'status' => 'Status',
        'actions' => 'Aktionen',
        'save' => 'Speichern',
        'cancel' => 'Abbrechen',
        'edit' => 'Bearbeiten',
        'delete' => 'Löschen',
        'view' => 'Ansehen',
        'created' => 'Erstellt',
        'pending' => 'Ausstehend',
        'confirmed' => 'Bestätigt',
        'cancelled' => 'Storniert',
        'confirm_delete' => 'Sind Sie sicher, dass Sie diese Buchung löschen möchten?',
        'search' => 'Suchen...',
        'no_bookings' => 'Keine Buchungen gefunden',
        'filter_status' => 'Nach Status filtern',
        'filter_date' => 'Nach Datum filtern',
        'all_status' => 'Alle Status',
        'total_bookings' => 'Buchungen gesamt',
        'pending_bookings' => 'Ausstehende Buchungen',
        'confirmed_bookings' => 'Bestätigte Buchungen',
        'cancelled_bookings' => 'Stornierte Buchungen',
        'todays_bookings' => 'Heutige Buchungen',
        'bulk_actions' => 'Massenaktionen',
        'select_action' => 'Aktion wählen',
        'apply' => 'Anwenden',
        'export' => 'Exportieren',
        'calendar_view' => 'Kalenderansicht',
        'upcoming' => 'Kommende Termine',
        'past' => 'Vergangene Termine'
    ],
    'en' => [
        'bookings' => 'Bookings',
        'add_booking' => 'Add New Booking',
        'edit_booking' => 'Edit Booking',
        'booking_list' => 'Booking List',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'service_type' => 'Service Type',
        'booking_date' => 'Date',
        'booking_time' => 'Time',
        'message' => 'Message',
        'status' => 'Status',
        'actions' => 'Actions',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'created' => 'Created',
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
        'confirm_delete' => 'Are you sure you want to delete this booking?',
        'search' => 'Search...',
        'no_bookings' => 'No bookings found',
        'filter_status' => 'Filter by status',
        'filter_date' => 'Filter by date',
        'all_status' => 'All Status',
        'total_bookings' => 'Total Bookings',
        'pending_bookings' => 'Pending Bookings',
        'confirmed_bookings' => 'Confirmed Bookings',
        'cancelled_bookings' => 'Cancelled Bookings',
        'todays_bookings' => 'Today\'s Bookings',
        'bulk_actions' => 'Bulk Actions',
        'select_action' => 'Select Action',
        'apply' => 'Apply',
        'export' => 'Export',
        'calendar_view' => 'Calendar View',
        'upcoming' => 'Upcoming',
        'past' => 'Past'
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
                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                    <?php echo $t['bookings']; ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <?php if ($action === 'list'): ?>
                        <div class="btn-group me-2">
                            <a href="?action=add" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i><?php echo $t['add_booking']; ?>
                            </a>
                            <a href="?action=calendar" class="btn btn-outline-secondary">
                                <i class="fas fa-calendar me-2"></i><?php echo $t['calendar_view']; ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="booking.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i><?php echo $t['booking_list']; ?>
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
                                    <i class="fas fa-calendar-check fa-2x text-primary"></i>
                                </div>
                                <div class="stat-number h4 fw-bold"><?php echo $stats['total']; ?></div>
                                <div class="stat-label text-muted small"><?php echo $t['total_bookings']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card admin-card stats-card warning">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                                <div class="stat-number h4 fw-bold"><?php echo $stats['pending']; ?></div>
                                <div class="stat-label text-muted small"><?php echo $t['pending_bookings']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card admin-card stats-card success">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                                <div class="stat-number h4 fw-bold"><?php echo $stats['confirmed']; ?></div>
                                <div class="stat-label text-muted small"><?php echo $t['confirmed_bookings']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card admin-card stats-card danger">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                </div>
                                <div class="stat-number h4 fw-bold"><?php echo $stats['cancelled']; ?></div>
                                <div class="stat-label text-muted small"><?php echo $t['cancelled_bookings']; ?></div>
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
                                <div class="stat-label text-muted small"><?php echo $t['todays_bookings']; ?></div>
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
                                    <div class="col-md-3">
                                        <label for="search" class="form-label small fw-bold"><?php echo $t['search']; ?></label>
                                        <input type="text" class="form-control" id="search" name="search" 
                                               value="<?php echo escape($_GET['search'] ?? ''); ?>"
                                               placeholder="Name, E-Mail, Service...">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label for="status" class="form-label small fw-bold"><?php echo $t['filter_status']; ?></label>
                                        <select class="form-select" id="status" name="status">
                                            <option value=""><?php echo $t['all_status']; ?></option>
                                            <option value="pending" <?php echo ($_GET['status'] ?? '') === 'pending' ? 'selected' : ''; ?>><?php echo $t['pending']; ?></option>
                                            <option value="confirmed" <?php echo ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : ''; ?>><?php echo $t['confirmed']; ?></option>
                                            <option value="cancelled" <?php echo ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>><?php echo $t['cancelled']; ?></option>
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
                                    
                                    <div class="col-md-3">
                                        <div class="btn-group w-100">
                                            <a href="booking.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-refresh me-1"></i>Reset
                                            </a>
                                            <button type="button" class="btn btn-outline-success" onclick="exportBookings()">
                                                <i class="fas fa-download me-1"></i><?php echo $t['export']; ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings List -->
                <div class="row">
                    <div class="col-12">
                        <div class="card admin-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    <?php echo $t['booking_list']; ?>
                                </h5>
                                <div class="card-tools">
                                    <small class="text-muted">
                                        <?php echo count($bookings); ?> 
                                        <?php echo $admin_lang === 'de' ? 'Buchungen gefunden' : 'bookings found'; ?>
                                    </small>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($bookings)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted"><?php echo $t['no_bookings']; ?></h5>
                                        <a href="?action=add" class="btn btn-primary mt-3">
                                            <i class="fas fa-plus me-2"></i><?php echo $t['add_booking']; ?>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <form id="bulkForm" method="POST">
                                        <input type="hidden" name="action" value="bulk_action">
                                        
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
                                                    <option value="confirmed"><?php echo $t['confirmed']; ?></option>
                                                    <option value="cancelled"><?php echo $t['cancelled']; ?></option>
                                                    <option value="pending"><?php echo $t['pending']; ?></option>
                                                    <option value="delete"><?php echo $t['delete']; ?></option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary" 
                                                        onclick="return confirm('<?php echo $admin_lang === 'de' ? 'Ausgewählte Aktionen ausführen?' : 'Execute selected actions?'; ?>')">
                                                    <?php echo $t['apply']; ?>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" id="bookingsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="30"><input type="checkbox" class="form-check-input" id="selectAllHeader"></th>
                                                        <th><?php echo $t['name']; ?></th>
                                                        <th><?php echo $t['service_type']; ?></th>
                                                        <th><?php echo $t['booking_date']; ?></th>
                                                        <th><?php echo $t['booking_time']; ?></th>
                                                        <th><?php echo $t['status']; ?></th>
                                                        <th><?php echo $t['created']; ?></th>
                                                        <th class="text-end"><?php echo $t['actions']; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($bookings as $booking): ?>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="selected_bookings[]" 
                                                                       value="<?php echo $booking['id']; ?>" 
                                                                       class="form-check-input booking-checkbox">
                                                            </td>
                                                            <td>
                                                                <div>
                                                                    <div class="fw-bold"><?php echo escape($booking['name']); ?></div>
                                                                    <small class="text-muted"><?php echo escape($booking['email']); ?></small>
                                                                    <?php if ($booking['phone']): ?>
                                                                        <br><small class="text-muted">
                                                                            <i class="fas fa-phone me-1"></i><?php echo escape($booking['phone']); ?>
                                                                        </small>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                            <td><?php echo escape($booking['service_type']); ?></td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <?php 
                                                                    $booking_date = new DateTime($booking['booking_date']);
                                                                    $today = new DateTime();
                                                                    $is_today = $booking_date->format('Y-m-d') === $today->format('Y-m-d');
                                                                    $is_past = $booking_date < $today;
                                                                    $is_future = $booking_date > $today;
                                                                    ?>
                                                                    
                                                                    <?php if ($is_today): ?>
                                                                        <i class="fas fa-calendar-day text-info me-2" title="<?php echo $admin_lang === 'de' ? 'Heute' : 'Today'; ?>"></i>
                                                                    <?php elseif ($is_past): ?>
                                                                        <i class="fas fa-calendar-minus text-muted me-2" title="<?php echo $admin_lang === 'de' ? 'Vergangen' : 'Past'; ?>"></i>
                                                                    <?php else: ?>
                                                                        <i class="fas fa-calendar-plus text-success me-2" title="<?php echo $admin_lang === 'de' ? 'Zukünftig' : 'Future'; ?>"></i>
                                                                    <?php endif; ?>
                                                                    
                                                                    <div>
                                                                        <div class="fw-bold"><?php echo formatDate($booking['booking_date'], 'd.m.Y'); ?></div>
                                                                        <small class="text-muted"><?php echo getGermanDayName($booking['booking_date']); ?></small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-secondary"><?php echo formatTime($booking['booking_time']); ?></span>
                                                            </td>
                                                            <td>
                                                                <form method="POST" class="d-inline">
                                                                    <input type="hidden" name="action" value="update_status">
                                                                    <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                                                                    <select name="status" class="form-select form-select-sm status-select" 
                                                                            onchange="if(confirm('<?php echo $admin_lang === 'de' ? 'Status ändern?' : 'Change status?'; ?>')) this.form.submit()">
                                                                        <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>><?php echo $t['pending']; ?></option>
                                                                        <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>><?php echo $t['confirmed']; ?></option>
                                                                        <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>><?php echo $t['cancelled']; ?></option>
                                                                    </select>
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">
                                                                    <?php echo formatDate($booking['created_at'], 'd.m.Y H:i'); ?>
                                                                </small>
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="btn-group" role="group">
                                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                                            data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $booking['id']; ?>" 
                                                                            title="<?php echo $t['view']; ?>">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                    <a href="?action=edit&id=<?php echo $booking['id']; ?>" 
                                                                       class="btn btn-sm btn-outline-warning" 
                                                                       title="<?php echo $t['edit']; ?>">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form method="POST" class="d-inline" 
                                                                          onsubmit="return confirm('<?php echo $t['confirm_delete']; ?>')">
                                                                        <input type="hidden" name="action" value="delete">
                                                                        <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                                title="<?php echo $t['delete']; ?>">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        
                                                        <!-- View Modal -->
                                                        <div class="modal fade" id="viewModal<?php echo $booking['id']; ?>" tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">
                                                                            <i class="fas fa-calendar-alt me-2"></i>
                                                                            <?php echo $admin_lang === 'de' ? 'Buchungsdetails' : 'Booking Details'; ?>
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row g-3">
                                                                            <div class="col-md-6">
                                                                                <strong><?php echo $t['name']; ?>:</strong><br>
                                                                                <?php echo escape($booking['name']); ?>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <strong><?php echo $t['email']; ?>:</strong><br>
                                                                                <a href="mailto:<?php echo $booking['email']; ?>"><?php echo escape($booking['email']); ?></a>
                                                                            </div>
                                                                            <?php if ($booking['phone']): ?>
                                                                            <div class="col-md-6">
                                                                                <strong><?php echo $t['phone']; ?>:</strong><br>
                                                                                <a href="tel:<?php echo $booking['phone']; ?>"><?php echo escape($booking['phone']); ?></a>
                                                                            </div>
                                                                            <?php endif; ?>
                                                                            <div class="col-md-6">
                                                                                <strong><?php echo $t['service_type']; ?>:</strong><br>
                                                                                <?php echo escape($booking['service_type']); ?>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <strong><?php echo $t['booking_date']; ?>:</strong><br>
                                                                                <?php echo formatDate($booking['booking_date'], 'd.m.Y') . ' (' . getGermanDayName($booking['booking_date']) . ')'; ?>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <strong><?php echo $t['booking_time']; ?>:</strong><br>
                                                                                <?php echo formatTime($booking['booking_time']); ?> Uhr
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <strong><?php echo $t['status']; ?>:</strong><br>
                                                                                <span class="badge bg-<?php 
                                                                                    echo $booking['status'] === 'confirmed' ? 'success' : 
                                                                                         ($booking['status'] === 'pending' ? 'warning' : 'danger'); 
                                                                                ?>">
                                                                                    <?php echo $t[$booking['status']]; ?>
                                                                                </span>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <strong><?php echo $t['created']; ?>:</strong><br>
                                                                                <?php echo formatDate($booking['created_at'], 'd.m.Y H:i'); ?>
                                                                            </div>
                                                                            <?php if ($booking['message']): ?>
                                                                            <div class="col-12">
                                                                                <strong><?php echo $t['message']; ?>:</strong><br>
                                                                                <div class="bg-light p-3 rounded">
                                                                                    <?php echo nl2br(escape($booking['message'])); ?>
                                                                                </div>
                                                                            </div>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                            <?php echo $admin_lang === 'de' ? 'Schließen' : 'Close'; ?>
                                                                        </button>
                                                                        <a href="?action=edit&id=<?php echo $booking['id']; ?>" class="btn btn-primary">
                                                                            <i class="fas fa-edit me-2"></i><?php echo $t['edit']; ?>
                                                                        </a>
                                                                    </div>
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

            <?php else: ?>
                <!-- Add/Edit Form -->
                <div class="row">
                    <div class="col-12">
                        <div class="card admin-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?> me-2"></i>
                                    <?php echo $action === 'add' ? $t['add_booking'] : $t['edit_booking']; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="needs-validation" novalidate>
                                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                                    
                                    <div class="row">
                                        <!-- Customer Information -->
                                        <div class="col-lg-6">
                                            <h6 class="fw-bold mb-3 text-primary">
                                                <?php echo $admin_lang === 'de' ? 'Kundendaten' : 'Customer Information'; ?>
                                            </h6>
                                            
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label for="name" class="form-label fw-bold">
                                                        <?php echo $t['name']; ?> *
                                                    </label>
                                                    <input type="text" class="form-control" id="name" name="name" 
                                                           value="<?php echo escape($booking['name'] ?? ''); ?>" required>
                                                    <div class="invalid-feedback">
                                                        <?php echo $admin_lang === 'de' ? 'Bitte geben Sie einen Namen ein' : 'Please enter a name'; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <label for="email" class="form-label fw-bold">
                                                        <?php echo $t['email']; ?> *
                                                    </label>
                                                    <input type="email" class="form-control" id="email" name="email" 
                                                           value="<?php echo escape($booking['email'] ?? ''); ?>" required>
                                                    <div class="invalid-feedback">
                                                        <?php echo $admin_lang === 'de' ? 'Bitte geben Sie eine gültige E-Mail ein' : 'Please enter a valid email'; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <label for="phone" class="form-label fw-bold">
                                                        <?php echo $t['phone']; ?>
                                                    </label>
                                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                                           value="<?php echo escape($booking['phone'] ?? ''); ?>">
                                                </div>
                                                
                                                <div class="col-12">
                                                    <label for="service_type" class="form-label fw-bold">
                                                        <?php echo $t['service_type']; ?> *
                                                    </label>
                                                    <select class="form-select" id="service_type" name="service_type" required>
                                                        <option value="">Bitte wählen...</option>
                                                        <option value="Karriereberatung" <?php echo ($booking['service_type'] ?? '') === 'Karriereberatung' ? 'selected' : ''; ?>>Karriereberatung</option>
                                                        <option value="Jobvermittlung" <?php echo ($booking['service_type'] ?? '') === 'Jobvermittlung' ? 'selected' : ''; ?>>Jobvermittlung</option>
                                                        <option value="Bewerbungsoptimierung" <?php echo ($booking['service_type'] ?? '') === 'Bewerbungsoptimierung' ? 'selected' : ''; ?>>Bewerbungsoptimierung</option>
                                                        <option value="Unternehmen - Personalsuche" <?php echo ($booking['service_type'] ?? '') === 'Unternehmen - Personalsuche' ? 'selected' : ''; ?>>Unternehmen - Personalsuche</option>
                                                        <option value="Allgemeine Beratung" <?php echo ($booking['service_type'] ?? '') === 'Allgemeine Beratung' ? 'selected' : ''; ?>>Allgemeine Beratung</option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        <?php echo $admin_lang === 'de' ? 'Bitte wählen Sie einen Service-Typ' : 'Please select a service type'; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Booking Information -->
                                        <div class="col-lg-6">
                                            <h6 class="fw-bold mb-3 text-primary">
                                                <?php echo $admin_lang === 'de' ? 'Termindetails' : 'Appointment Details'; ?>
                                            </h6>
                                            
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="booking_date" class="form-label fw-bold">
                                                        <?php echo $t['booking_date']; ?> *
                                                    </label>
                                                    <input type="date" class="form-control" id="booking_date" name="booking_date" 
                                                           value="<?php echo escape($booking['booking_date'] ?? ''); ?>" 
                                                           min="<?php echo date('Y-m-d'); ?>" required>
                                                    <div class="invalid-feedback">
                                                        <?php echo $admin_lang === 'de' ? 'Bitte wählen Sie ein Datum' : 'Please select a date'; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="booking_time" class="form-label fw-bold">
                                                        <?php echo $t['booking_time']; ?> *
                                                    </label>
                                                    <select class="form-select" id="booking_time" name="booking_time" required>
                                                        <option value="">Zeit wählen...</option>
                                                        <?php
                                                        for ($hour = BOOKING_START_HOUR; $hour < BOOKING_END_HOUR; $hour++) {
                                                            for ($minute = 0; $minute < 60; $minute += BOOKING_SLOT_DURATION) {
                                                                $time = sprintf('%02d:%02d:00', $hour, $minute);
                                                                $time_display = sprintf('%02d:%02d', $hour, $minute);
                                                                $selected = ($booking['booking_time'] ?? '') === $time ? 'selected' : '';
                                                                echo "<option value=\"$time\" $selected>$time_display</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        <?php echo $admin_lang === 'de' ? 'Bitte wählen Sie eine Uhrzeit' : 'Please select a time'; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <label for="status" class="form-label fw-bold">
                                                        <?php echo $t['status']; ?>
                                                    </label>
                                                    <select class="form-select" id="status" name="status">
                                                        <option value="pending" <?php echo ($booking['status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>><?php echo $t['pending']; ?></option>
                                                        <option value="confirmed" <?php echo ($booking['status'] ?? '') === 'confirmed' ? 'selected' : ''; ?>><?php echo $t['confirmed']; ?></option>
                                                        <option value="cancelled" <?php echo ($booking['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>><?php echo $t['cancelled']; ?></option>
                                                    </select>
                                                </div>
                                                
                                                <?php if ($booking): ?>
                                                <div class="col-12">
                                                    <div class="card border-light">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Information</h6>
                                                            <p class="card-text small">
                                                                <strong><?php echo $admin_lang === 'de' ? 'Erstellt:' : 'Created:'; ?></strong><br>
                                                                <?php echo formatDate($booking['created_at'], 'd.m.Y H:i'); ?>
                                                            </p>
                                                            <p class="card-text small">
                                                                <strong><?php echo $admin_lang === 'de' ? 'Aktualisiert:' : 'Updated:'; ?></strong><br>
                                                                <?php echo formatDate($booking['updated_at'], 'd.m.Y H:i'); ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Message -->
                                        <div class="col-12 mt-4">
                                            <h6 class="fw-bold mb-3 text-primary">
                                                <?php echo $admin_lang === 'de' ? 'Nachricht' : 'Message'; ?>
                                            </h6>
                                            
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="message" class="form-label fw-bold">
                                                        <?php echo $t['message']; ?>
                                                    </label>
                                                    <textarea class="form-control" id="message" name="message" rows="4" data-autoresize><?php echo escape($booking['message'] ?? ''); ?></textarea>
                                                    <div class="form-text">
                                                        <?php echo $admin_lang === 'de' ? 'Zusätzliche Informationen oder Wünsche' : 'Additional information or requests'; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Form Actions -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between">
                                                <a href="booking.php" class="btn btn-outline-secondary">
                                                    <i class="fas fa-arrow-left me-2"></i><?php echo $t['cancel']; ?>
                                                </a>
                                                <button type="submit" class="btn btn-primary" data-action="save">
                                                    <i class="fas fa-save me-2"></i><?php echo $t['save']; ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
    const checkboxes = document.querySelectorAll('.booking-checkbox');
    
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
            const checkedCount = document.querySelectorAll('.booking-checkbox:checked').length;
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
function exportBookings() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.open('booking_export.php?' + params.toString());
}

// Auto-resize textareas
document.querySelectorAll('textarea[data-autoresize]').forEach(textarea => {
    function autoResize() {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }
    
    textarea.addEventListener('input', autoResize);
    autoResize();
});

// Date validation
document.getElementById('booking_date')?.addEventListener('change', function() {
    const selectedDate = new Date(this.value);
    const dayOfWeek = selectedDate.getDay();
    
    // Check if weekend (Saturday = 6, Sunday = 0)
    if (dayOfWeek === 0 || dayOfWeek === 6) {
        alert('<?php echo $admin_lang === "de" ? "Wochenenden sind nicht verfügbar. Bitte wählen Sie einen Wochentag." : "Weekends are not available. Please choose a weekday."; ?>');
        this.value = '';
        return;
    }
    
    // Check available time slots (optional enhancement)
    checkAvailableTimeSlots(this.value);
});

function checkAvailableTimeSlots(date) {
    // This could be enhanced to check for available time slots via AJAX
    // For now, we'll keep it simple
}

// Form validation enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.needs-validation');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Focus first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
            form.classList.add('was-validated');
        });
    }
});

// Status color coding
document.querySelectorAll('.status-select').forEach(select => {
    function updateStatusColor() {
        const status = select.value;
        select.className = select.className.replace(/text-\w+/, '');
        
        if (status === 'confirmed') {
            select.classList.add('text-success');
        } else if (status === 'pending') {
            select.classList.add('text-warning');
        } else if (status === 'cancelled') {
            select.classList.add('text-danger');
        }
    }
    
    updateStatusColor();
    select.addEventListener('change', updateStatusColor);
});

// Save keyboard shortcut
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        const saveButton = document.querySelector('[data-action="save"]');
        if (saveButton) {
            saveButton.click();
        }
    }
});
</script>

<?php include 'includes/admin_footer.php'; ?>