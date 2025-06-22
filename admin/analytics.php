<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!check_admin_auth()) {
    redirect('login.php');
}

$admin_lang = $_SESSION['admin_language'] ?? 'de';
$translations = [
    'de' => [
        'analytics' => 'Statistiken',
        'coming_soon' => 'Analysefunktionen werden bald verfügbar sein.'
    ],
    'en' => [
        'analytics' => 'Analytics',
        'coming_soon' => 'Analytics features will be available soon.'
    ]
];
$t = $translations[$admin_lang];

include 'includes/admin_header.php';
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i><?php echo $t['analytics']; ?></h1>
            </div>
            <div class="alert alert-info">
                <?php echo $t['coming_soon']; ?>
            </div>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
