<?php
require_once '../includes/config.php';

if (!check_admin_auth()) {
    redirect('login.php');
}

// Handle language change
if (isset($_GET['change_lang']) && in_array($_GET['change_lang'], ['de', 'en'])) {
    $_SESSION['admin_language'] = $_GET['change_lang'];
    
    // Update in database
    $db = Database::getInstance();
    $db->execute(
        "UPDATE admins SET language = ? WHERE id = ?",
        [$_GET['change_lang'], $_SESSION['admin_id']]
    );
    
    echo "<div style='background: green; color: white; padding: 10px; margin: 10px;'>";
    echo "Sprache erfolgreich geändert zu: " . $_GET['change_lang'];
    echo "</div>";
    
    // Redirect after 2 seconds
    echo "<script>setTimeout(() => { window.location.href = 'test_lang.php'; }, 2000);</script>";
    exit;
}

$admin_lang = $_SESSION['admin_language'] ?? 'de';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Language Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .info { background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .lang-switch { margin: 20px 0; }
        .lang-switch a { 
            display: inline-block; 
            padding: 10px 20px; 
            margin: 5px; 
            background: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px;
        }
        .lang-switch a:hover { background: #0056b3; }
        .current { background: #28a745 !important; }
    </style>
</head>
<body>
    <h1>Language Test Page</h1>
    
    <div class="info">
        <strong>Current Language:</strong> <?php echo $admin_lang; ?><br>
        <strong>Session Language:</strong> <?php echo $_SESSION['admin_language'] ?? 'not set'; ?><br>
        <strong>Admin ID:</strong> <?php echo $_SESSION['admin_id'] ?? 'not set'; ?><br>
        <strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s'); ?>
    </div>
    
    <div class="lang-switch">
        <strong>Change Language:</strong><br>
        <a href="?change_lang=de" class="<?php echo $admin_lang === 'de' ? 'current' : ''; ?>">
            🇩🇪 Deutsch
        </a>
        <a href="?change_lang=en" class="<?php echo $admin_lang === 'en' ? 'current' : ''; ?>">
            🇺🇸 English
        </a>
    </div>
    
    <div class="info">
        <h3><?php echo $admin_lang === 'de' ? 'Deutscher Text' : 'English Text'; ?></h3>
        <p>
            <?php if ($admin_lang === 'de'): ?>
                Dies ist ein Test der deutschen Sprache. Wenn Sie diesen Text sehen, funktioniert die Sprachumschaltung.
            <?php else: ?>
                This is a test of the English language. If you see this text, the language switching is working.
            <?php endif; ?>
        </p>
    </div>
    
    <div style="margin-top: 30px;">
        <a href="dashboard.php" style="color: #007bff;">← Back to Dashboard</a>
    </div>
</body>
</html>