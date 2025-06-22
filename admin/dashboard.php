<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Check authentication
if (!check_admin_auth()) {
    redirect('login.php');
}

// Get statistics
$db = Database::getInstance();

$stats = [
    'total_jobs' => $db->selectOne("SELECT COUNT(*) as count FROM jobs")['count'] ?? 0,
    'active_jobs' => $db->selectOne("SELECT COUNT(*) as count FROM jobs WHERE is_active = 1")['count'] ?? 0,
    'total_bookings' => $db->selectOne("SELECT COUNT(*) as count FROM bookings")['count'] ?? 0,
    'pending_bookings' => $db->selectOne("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")['count'] ?? 0,
    'total_contacts' => $db->selectOne("SELECT COUNT(*) as count FROM contacts")['count'] ?? 0,
    'unread_contacts' => $db->selectOne("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0")['count'] ?? 0
];

// Recent activities
$recent_jobs = $db->select("SELECT * FROM jobs ORDER BY created_at DESC LIMIT 5");
$recent_bookings = $db->select("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 5");
$recent_contacts = $db->select("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");

// Monthly stats for chart
$monthly_stats = $db->select("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as jobs_count
    FROM jobs 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month
");

$admin_lang = $_SESSION['admin_language'] ?? 'de';

// Translations
$translations = [
    'de' => [
        'dashboard' => 'Dashboard',
        'overview' => 'Übersicht',
        'total_jobs' => 'Stellenangebote gesamt',
        'active_jobs' => 'Aktive Stellen',
        'total_bookings' => 'Buchungen gesamt',
        'pending_bookings' => 'Offene Buchungen',
        'total_contacts' => 'Kontakte gesamt',
        'unread_contacts' => 'Ungelesene Kontakte',
        'recent_jobs' => 'Neueste Stellenangebote',
        'recent_bookings' => 'Neueste Buchungen',
        'recent_contacts' => 'Neueste Kontakte',
        'view_all' => 'Alle anzeigen',
        'no_data' => 'Keine Daten verfügbar',
        'quick_actions' => 'Schnellaktionen',
        'add_job' => 'Neue Stelle hinzufügen',
        'manage_bookings' => 'Buchungen verwalten',
        'site_settings' => 'Website-Einstellungen',
        'quick_actions' => 'Schnellaktionen',
        'add_job' => 'Neue Stelle hinzufügen',
        'manage_bookings' => 'Buchungen verwalten',
        'site_settings' => 'Website-Einstellungen',
        'system_info' => 'System-Informationen',
        'monthly_jobs' => 'Stellenangebote pro Monat',
        'status' => 'Status',
        'date' => 'Datum',
        'name' => 'Name',
        'email' => 'E-Mail',
        'phone' => 'Telefon',
        'title' => 'Titel',
        'company' => 'Unternehmen',
        'featured' => 'Featured',
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'pending' => 'Ausstehend',
        'confirmed' => 'Bestätigt',
        'cancelled' => 'Storniert',
        'read' => 'Gelesen',
        'unread' => 'Ungelesen',
        'service_type' => 'Service-Art',
        'booking_time' => 'Termin',
        'subject' => 'Betreff',
        'message' => 'Nachricht',
        'welcome_back' => 'Willkommen zurück',
        'last_login' => 'Letzte Anmeldung'
    ],
    'en' => [
        'dashboard' => 'Dashboard',
        'overview' => 'Overview',
        'total_jobs' => 'Total Jobs',
        'active_jobs' => 'Active Jobs',
        'total_bookings' => 'Total Bookings',
        'pending_bookings' => 'Pending Bookings',
        'total_contacts' => 'Total Contacts',
        'unread_contacts' => 'Unread Contacts',
        'recent_jobs' => 'Recent Jobs',
        'recent_bookings' => 'Recent Bookings',
        'recent_contacts' => 'Recent Contacts',
        'view_all' => 'View All',
        'no_data' => 'No data available',
        'quick_actions' => 'Quick Actions',
        'add_job' => 'Add New Job',
        'manage_bookings' => 'Manage Bookings',
        'site_settings' => 'Site Settings',
        'system_info' => 'System Information',
        'monthly_jobs' => 'Jobs per Month',
        'status' => 'Status',
        'date' => 'Date',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'title' => 'Title',
        'company' => 'Company',
        'featured' => 'Featured',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
        'read' => 'Read',
        'unread' => 'Unread',
        'service_type' => 'Service Type',
        'booking_time' => 'Appointment',
        'subject' => 'Subject',
        'message' => 'Message',
        'welcome_back' => 'Welcome back',
        'last_login' => 'Last login'
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
                    <i class="fas fa-tachometer-alt text-primary me-2"></i>
                    <?php echo $t['dashboard']; ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-primary border-0 bg-primary bg-opacity-10">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">
                                    <?php echo $t['welcome_back']; ?>, <?php echo escape($_SESSION['admin_username']); ?>!
                                </h5>
                                <p class="mb-0 text-muted">
                                    <?php echo $t['last_login']; ?>: <?php echo date('d.m.Y H:i', $_SESSION['login_time']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card admin-card stats-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        <?php echo $t['total_jobs']; ?>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['total_jobs']; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card admin-card stats-card success">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        <?php echo $t['active_jobs']; ?>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['active_jobs']; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card admin-card stats-card info">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        <?php echo $t['total_bookings']; ?>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['total_bookings']; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card admin-card stats-card warning">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        <?php echo $t['pending_bookings']; ?>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['pending_bookings']; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card admin-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bolt text-primary me-2"></i>
                                <?php echo $t['quick_actions']; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <a href="jobs.php?action=add" class="btn btn-primary w-100">
                                        <i class="fas fa-plus me-2"></i>
                                        <?php echo $t['add_job']; ?>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="booking.php" class="btn btn-info w-100">
                                        <i class="fas fa-calendar-check me-2"></i>
                                        <?php echo $t['manage_bookings']; ?>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="settings.php" class="btn btn-secondary w-100">
                                        <i class="fas fa-cog me-2"></i>
                                        <?php echo $t['site_settings']; ?>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="../" target="_blank" class="btn btn-success w-100">
                                        <i class="fas fa-external-link-alt me-2"></i>
                                        Website ansehen
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Recent Activity -->
            <div class="row">
                <!-- Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="card admin-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line text-primary me-2"></i>
                                <?php echo $t['monthly_jobs']; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="jobsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- System Info -->
                <div class="col-lg-4 mb-4">
                    <div class="card admin-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-server text-primary me-2"></i>
                                <?php echo $t['system_info']; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="system-info">
                                <div class="info-item d-flex justify-content-between mb-2">
                                    <span>PHP Version:</span>
                                    <span class="fw-bold"><?php echo PHP_VERSION; ?></span>
                                </div>
                                <div class="info-item d-flex justify-content-between mb-2">
                                    <span>MySQL Version:</span>
                                    <span class="fw-bold">
                                        <?php 
                                        $version = $db->selectOne("SELECT VERSION() as version");
                                        echo explode('-', $version['version'])[0];
                                        ?>
                                    </span>
                                </div>
                                <div class="info-item d-flex justify-content-between mb-2">
                                    <span>Server:</span>
                                    <span class="fw-bold"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
                                </div>
                                <div class="info-item d-flex justify-content-between mb-2">
                                    <span>Disk Space:</span>
                                    <span class="fw-bold">
                                        <?php 
                                        $bytes = disk_free_space(".");
                                        $gb = round($bytes / 1024 / 1024 / 1024, 2);
                                        echo $gb . " GB";
                                        ?>
                                    </span>
                                </div>
                                <div class="info-item d-flex justify-content-between">
                                    <span>Memory Limit:</span>
                                    <span class="fw-bold"><?php echo ini_get('memory_limit'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row">
                <!-- Recent Jobs -->
                <div class="col-lg-4 mb-4">
                    <div class="card admin-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-briefcase text-primary me-2"></i>
                                <?php echo $t['recent_jobs']; ?>
                            </h5>
                            <a href="jobs.php" class="btn btn-sm btn-outline-primary">
                                <?php echo $t['view_all']; ?>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($recent_jobs)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0"><?php echo $t['no_data']; ?></p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recent_jobs as $job): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo escape($job['title']); ?></h6>
                                                <small><?php echo formatDate($job['created_at']); ?></small>
                                            </div>
                                            <p class="mb-1 text-muted small">
                                                <?php echo escape($job['company']); ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted"><?php echo escape($job['location']); ?></small>
                                                <div>
                                                    <?php if ($job['featured']): ?>
                                                        <span class="badge bg-warning text-dark">Featured</span>
                                                    <?php endif; ?>
                                                    <span class="badge <?php echo $job['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?php echo $job['is_active'] ? $t['active'] : $t['inactive']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="col-lg-4 mb-4">
                    <div class="card admin-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <?php echo $t['recent_bookings']; ?>
                            </h5>
                            <a href="booking.php" class="btn btn-sm btn-outline-primary">
                                <?php echo $t['view_all']; ?>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($recent_bookings)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0"><?php echo $t['no_data']; ?></p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recent_bookings as $booking): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo escape($booking['name']); ?></h6>
                                                <small><?php echo formatDate($booking['created_at']); ?></small>
                                            </div>
                                            <p class="mb-1 text-muted small">
                                                <?php echo escape($booking['service_type']); ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <?php echo formatDate($booking['booking_date']) . ' ' . formatTime($booking['booking_time']); ?>
                                                </small>
                                                <span class="badge <?php 
                                                    echo $booking['status'] === 'confirmed' ? 'bg-success' : 
                                                        ($booking['status'] === 'pending' ? 'bg-warning' : 'bg-danger'); 
                                                ?>">
                                                    <?php 
                                                    $status_labels = [
                                                        'pending' => $t['pending'],
                                                        'confirmed' => $t['confirmed'],
                                                        'cancelled' => $t['cancelled']
                                                    ];
                                                    echo $status_labels[$booking['status']] ?? $booking['status'];
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Contacts -->
                <div class="col-lg-4 mb-4">
                    <div class="card admin-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <?php echo $t['recent_contacts']; ?>
                                <?php if ($stats['unread_contacts'] > 0): ?>
                                    <span class="badge bg-danger ms-2"><?php echo $stats['unread_contacts']; ?></span>
                                <?php endif; ?>
                            </h5>
                            <a href="contacts.php" class="btn btn-sm btn-outline-primary">
                                <?php echo $t['view_all']; ?>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($recent_contacts)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0"><?php echo $t['no_data']; ?></p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recent_contacts as $contact): ?>
                                        <div class="list-group-item <?php echo !$contact['is_read'] ? 'bg-light' : ''; ?>">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1 <?php echo !$contact['is_read'] ? 'fw-bold' : ''; ?>">
                                                    <?php echo escape($contact['name']); ?>
                                                </h6>
                                                <small><?php echo formatDate($contact['created_at']); ?></small>
                                            </div>
                                            <p class="mb-1 text-muted small">
                                                <?php echo escape($contact['subject'] ?: 'Kein Betreff'); ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted"><?php echo escape($contact['email']); ?></small>
                                                <span class="badge <?php echo $contact['is_read'] ? 'bg-secondary' : 'bg-primary'; ?>">
                                                    <?php echo $contact['is_read'] ? $t['read'] : $t['unread']; ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Jobs Chart
const ctx = document.getElementById('jobsChart').getContext('2d');
const jobsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            <?php 
            foreach ($monthly_stats as $stat) {
                $date = DateTime::createFromFormat('Y-m', $stat['month']);
                echo "'" . $date->format('M Y') . "',";
            }
            ?>
        ],
        datasets: [{
            label: '<?php echo $t['total_jobs']; ?>',
            data: [<?php echo implode(',', array_column($monthly_stats, 'jobs_count')); ?>],
            borderColor: '#0066cc',
            backgroundColor: 'rgba(0, 102, 204, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<?php include 'includes/admin_footer.php'; ?>