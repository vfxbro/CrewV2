<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!check_admin_auth()) {
    redirect('login.php');
}

$admin_lang = $_SESSION['admin_language'] ?? 'de';
$translations = [
    'de' => [
        'backup' => 'Backup',
        'download' => 'Datenbank-Backup herunterladen',
        'failed' => 'Backup konnte nicht erstellt werden.'
    ],
    'en' => [
        'backup' => 'Backup',
        'download' => 'Download database backup',
        'failed' => 'Failed to create backup.'
    ]
];
$t = $translations[$admin_lang];

if (isset($_GET['action']) && $_GET['action'] === 'download') {
    $file = 'backup_' . date('Ymd_His') . '.sql';
    $command = sprintf('mysqldump -h%s -u%s -p%s %s > %s', DB_HOST, DB_USER, DB_PASS, DB_NAME, $file);
    system($command, $retval);
    if ($retval === 0 && file_exists($file)) {
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename=' . basename($file));
        readfile($file);
        unlink($file);
        exit;
    } else {
        $error = $t['failed'];
    }
}

include 'includes/admin_header.php';
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 fw-bold"><i class="fas fa-download text-primary me-2"></i><?php echo $t['backup']; ?></h1>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo escape($error); ?></div>
            <?php endif; ?>
            <p><a class="btn btn-primary" href="?action=download"><i class="fas fa-download me-2"></i><?php echo $t['download']; ?></a></p>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
