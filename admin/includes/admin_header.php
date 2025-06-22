<?php
// Handle language change before any output - support both 'lang' and 'change_lang' parameters
$lang_param = $_GET['change_lang'] ?? $_GET['lang'] ?? null;

if ($lang_param && in_array($lang_param, ['de', 'en'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['admin_language'] = $lang_param;
    
    // Update in database if user is logged in
    if (isset($_SESSION['admin_id'])) {
        try {
            $db = Database::getInstance();
            $db->execute(
                "UPDATE admins SET language = ? WHERE id = ?",
                [$lang_param, $_SESSION['admin_id']]
            );
        } catch (Exception $e) {
            log_error("Failed to update admin language: " . $e->getMessage());
        }
    }
    
    // Redirect to same page without language parameter
    $current_url = $_SERVER['PHP_SELF'];
    if (!empty($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $params);
        unset($params['change_lang']);
        unset($params['lang']);
        if (!empty($params)) {
            $current_url .= '?' . http_build_query($params);
        }
    }
    header("Location: " . $current_url);
    exit;
}

$admin_lang = $_SESSION['admin_language'] ?? 'de';
$site_title = getSetting('site_title', 'Crew of Experts GmbH');
?>
<!DOCTYPE html>
<html lang="<?php echo $admin_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo escape($site_title); ?></title>
    
    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <!-- Admin specific styles -->
    <style>
        body {
            font-size: 0.9rem;
            background-color: #f8f9fa;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-header .navbar-brand {
            color: white !important;
            font-weight: 600;
        }
        
        .admin-header .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
        }
        
        .admin-header .navbar-nav .nav-link:hover {
            color: white !important;
        }
        
        .admin-header .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 0.5rem;
        }
        
        .notification-badge {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
            position: absolute;
            top: -5px;
            right: -5px;
        }
        
        .admin-content {
            padding-top: 1rem;
        }
        
        @media (max-width: 768px) {
            .admin-header .navbar-collapse {
                background: rgba(0, 0, 0, 0.1);
                border-radius: 0.5rem;
                margin-top: 1rem;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <nav class="navbar navbar-expand-lg admin-header sticky-top">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <i class="fas fa-shield-alt me-2"></i>
                Admin Panel
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <i class="fas fa-bars text-white"></i>
            </button>
            
            <!-- Navigation -->
            <div class="collapse navbar-collapse" id="adminNavbar">
                <!-- Left Side Navigation -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i>
                            <?php echo $admin_lang === 'de' ? 'Website ansehen' : 'View Website'; ?>
                        </a>
                    </li>
                </ul>
                
                <!-- Right Side Navigation -->
                <ul class="navbar-nav">
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <?php 
                            $unread_count = $db->selectOne("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0")['count'] ?? 0;
                            if ($unread_count > 0): 
                            ?>
                                <span class="notification-badge"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">
                                <?php echo $admin_lang === 'de' ? 'Benachrichtigungen' : 'Notifications'; ?>
                            </h6></li>
                            <?php if ($unread_count > 0): ?>
                                <li><a class="dropdown-item" href="contacts.php">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <?php echo $unread_count; ?> <?php echo $admin_lang === 'de' ? 'neue Nachrichten' : 'new messages'; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php else: ?>
                                <li><span class="dropdown-item-text text-muted">
                                    <?php echo $admin_lang === 'de' ? 'Keine neuen Benachrichtigungen' : 'No new notifications'; ?>
                                </span></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="contacts.php">
                                <?php echo $admin_lang === 'de' ? 'Alle anzeigen' : 'View all'; ?>
                            </a></li>
                        </ul>
                    </li>
                    
                    <!-- Language Switcher -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-1"></i>
                            <?php echo strtoupper($admin_lang); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item <?php echo $admin_lang === 'de' ? 'active' : ''; ?>" 
                                   href="<?php echo $_SERVER['PHP_SELF']; ?>?lang=de">
                                🇩🇪 Deutsch
                            </a></li>
                            <li><a class="dropdown-item <?php echo $admin_lang === 'en' ? 'active' : ''; ?>" 
                                   href="<?php echo $_SERVER['PHP_SELF']; ?>?lang=en">
                                🇺🇸 English
                            </a></li>
                        </ul>
                    </li>
                    
                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($_SESSION['admin_username'], 0, 1)); ?>
                            </div>
                            <span class="d-none d-md-inline"><?php echo escape($_SESSION['admin_username']); ?></span>
                            <i class="fas fa-chevron-down ms-2"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">
                                <?php echo escape($_SESSION['admin_username']); ?>
                            </h6></li>
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user text-primary me-2"></i>
                                <?php echo $admin_lang === 'de' ? 'Profil' : 'Profile'; ?>
                            </a></li>
                            <li><a class="dropdown-item" href="settings.php">
                                <i class="fas fa-cog text-secondary me-2"></i>
                                <?php echo $admin_lang === 'de' ? 'Einstellungen' : 'Settings'; ?>
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                <?php echo $admin_lang === 'de' ? 'Abmelden' : 'Logout'; ?>
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>