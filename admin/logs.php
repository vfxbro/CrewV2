<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!check_admin_auth()) {
    redirect('login.php');
}

$admin_lang = $_SESSION['admin_language'] ?? 'de';
$translations = [
    'de' => [
        'logs' => 'Logs',
        'empty' => 'Keine Logeinträge gefunden.'
    ],
    'en' => [
        'logs' => 'Logs',
        'empty' => 'No log entries found.'
    ]
];
$t = $translations[$admin_lang];

$logfile = '../logs/error.log';
$log_content = file_exists($logfile) ? file_get_contents($logfile) : '';

include 'includes/admin_header.php';
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 fw-bold"><i class="fas fa-file-alt text-primary me-2"></i><?php echo $t['logs']; ?></h1>
            </div>
            <?php if ($log_content): ?>
                <pre style="background:#1e1e1e;color:#e0e0e0;padding:1rem;border-radius:4px; white-space:pre-wrap;"><?php echo escape($log_content); ?></pre>
            <?php else: ?>
                <div class="alert alert-info"><?php echo $t['empty']; ?></div>
            <?php endif; ?>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
