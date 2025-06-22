<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$admin_lang = $_SESSION['admin_language'] ?? 'de';

// Translations for sidebar
$sidebar_translations = [
    'de' => [
        'dashboard' => 'Dashboard',
        'content_management' => 'Inhalte verwalten',
        'jobs' => 'Stellenangebote',
        'pages' => 'Seiten',
        'slides' => 'Slider',
        'bookings' => 'Buchungen',
        'contacts' => 'Kontakte',
        'system' => 'System',
        'settings' => 'Einstellungen',
        'users' => 'Benutzer',
        'analytics' => 'Statistiken',
        'tools' => 'Tools',
        'backup' => 'Backup',
        'logs' => 'Logs'
    ],
    'en' => [
        'dashboard' => 'Dashboard',
        'content_management' => 'Content Management',
        'jobs' => 'Jobs',
        'pages' => 'Pages',
        'slides' => 'Slides',
        'bookings' => 'Bookings',
        'contacts' => 'Contacts',
        'system' => 'System',
        'settings' => 'Settings',
        'users' => 'Users',
        'analytics' => 'Analytics',
        'tools' => 'Tools',
        'backup' => 'Backup',
        'logs' => 'Logs'
    ]
];

$st = $sidebar_translations[$admin_lang];

// Get counts for badges
$db = Database::getInstance();
$pending_bookings = $db->selectOne("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")['count'] ?? 0;
$unread_contacts = $db->selectOne("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0")['count'] ?? 0;
$active_jobs = $db->selectOne("SELECT COUNT(*) as count FROM jobs WHERE is_active = 1")['count'] ?? 0;
?>

<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse">
    <div class="position-sticky pt-3">
        <!-- Main Navigation -->
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <?php echo $st['dashboard']; ?>
                </a>
            </li>
        </ul>
        
        <!-- Content Management Section -->
        <div class="sidebar-section mt-4">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                <span><?php echo $st['content_management']; ?></span>
            </h6>
            <ul class="nav flex-column">
                <!-- Jobs -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'jobs' ? 'active' : ''; ?>" href="jobs.php">
                        <i class="fas fa-briefcase"></i>
                        <?php echo $st['jobs']; ?>
                        <?php if ($active_jobs > 0): ?>
                            <span class="badge bg-success ms-auto"><?php echo $active_jobs; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <!-- Pages -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'pages' ? 'active' : ''; ?>" href="pages.php">
                        <i class="fas fa-file-alt"></i>
                        <?php echo $st['pages']; ?>
                    </a>
                </li>
                
                <!-- Slides -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'slides' ? 'active' : ''; ?>" href="slides.php">
                        <i class="fas fa-images"></i>
                        <?php echo $st['slides']; ?>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Customer Management Section -->
        <div class="sidebar-section mt-4">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                <span>Customer Management</span>
            </h6>
            <ul class="nav flex-column">
                <!-- Bookings -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'booking' ? 'active' : ''; ?>" href="booking.php">
                        <i class="fas fa-calendar-alt"></i>
                        <?php echo $st['bookings']; ?>
                        <?php if ($pending_bookings > 0): ?>
                            <span class="badge bg-warning text-dark ms-auto"><?php echo $pending_bookings; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <!-- Contacts -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'contacts' ? 'active' : ''; ?>" href="contacts.php">
                        <i class="fas fa-envelope"></i>
                        <?php echo $st['contacts']; ?>
                        <?php if ($unread_contacts > 0): ?>
                            <span class="badge bg-danger ms-auto"><?php echo $unread_contacts; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <!-- Analytics -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'analytics' ? 'active' : ''; ?>" href="analytics.php">
                        <i class="fas fa-chart-bar"></i>
                        <?php echo $st['analytics']; ?>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- System Section -->
        <div class="sidebar-section mt-4">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                <span><?php echo $st['system']; ?></span>
            </h6>
            <ul class="nav flex-column">
                <!-- Settings -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>" href="settings.php">
                        <i class="fas fa-cog"></i>
                        <?php echo $st['settings']; ?>
                    </a>
                </li>
                
                <!-- Users -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'users' ? 'active' : ''; ?>" href="users.php">
                        <i class="fas fa-users"></i>
                        <?php echo $st['users']; ?>
                    </a>
                </li>
                
                <!-- Tools Dropdown -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center <?php echo in_array($current_page, ['backup', 'logs', 'tools']) ? 'active' : ''; ?>" 
                       data-bs-toggle="collapse" href="#toolsCollapse" role="button" aria-expanded="false">
                        <i class="fas fa-tools"></i>
                        <span class="flex-grow-1"><?php echo $st['tools']; ?></span>
                        <i class="fas fa-chevron-down sidebar-arrow"></i>
                    </a>
                    <div class="collapse <?php echo in_array($current_page, ['backup', 'logs', 'tools']) ? 'show' : ''; ?>" id="toolsCollapse">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_page === 'backup' ? 'active' : ''; ?>" href="backup.php">
                                    <i class="fas fa-download"></i>
                                    <?php echo $st['backup']; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_page === 'logs' ? 'active' : ''; ?>" href="logs.php">
                                    <i class="fas fa-file-text"></i>
                                    <?php echo $st['logs']; ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        
        <!-- Quick Actions -->
        <div class="sidebar-section mt-4 mb-4">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                <span>Quick Actions</span>
            </h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-success" href="jobs.php?action=add">
                        <i class="fas fa-plus"></i>
                        <?php echo $admin_lang === 'de' ? 'Neue Stelle' : 'Add Job'; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-info" href="../" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <?php echo $admin_lang === 'de' ? 'Website ansehen' : 'View Site'; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.admin-sidebar {
    background: linear-gradient(180deg, #0066cc 0%, #0052a3 100%);
    box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
    color: white;
    height: calc(100vh - 56px);
    overflow-y: auto;
}

.admin-sidebar .nav-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    margin: 0.125rem 0.75rem;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: center;
}

.admin-sidebar .nav-link:hover {
    color: white;
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateX(4px);
}

.admin-sidebar .nav-link.active {
    color: white;
    background-color: rgba(255, 255, 255, 0.2);
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.admin-sidebar .nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 60%;
    background-color: white;
    border-radius: 0 2px 2px 0;
}

.admin-sidebar .nav-link i {
    width: 20px;
    text-align: center;
    margin-right: 0.75rem;
    font-size: 0.9rem;
}

.admin-sidebar .badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.4rem;
}

.sidebar-heading {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: rgba(255, 255, 255, 0.6) !important;
}

.sidebar-section {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 1rem;
}

.sidebar-section:last-child {
    border-bottom: none;
}

.sidebar-arrow {
    font-size: 0.7rem;
    transition: transform 0.3s ease;
}

.nav-link[aria-expanded="true"] .sidebar-arrow {
    transform: rotate(180deg);
}

.collapse .nav-link {
    font-size: 0.85rem;
    margin-left: 0.5rem;
    margin-right: 1rem;
}

/* Scrollbar Styling */
.admin-sidebar::-webkit-scrollbar {
    width: 4px;
}

.admin-sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.admin-sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
}

.admin-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Mobile Responsiveness */
@media (max-width: 767.98px) {
    .admin-sidebar {
        position: fixed;
        top: 56px;
        left: 0;
        z-index: 1000;
        width: 280px;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .admin-sidebar.show {
        transform: translateX(0);
    }
    
    .sidebar-backdrop {
        position: fixed;
        top: 56px;
        left: 0;
        z-index: 999;
        width: 100%;
        height: calc(100vh - 56px);
        background: rgba(0, 0, 0, 0.5);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .sidebar-backdrop.show {
        opacity: 1;
        visibility: visible;
    }
}

/* Animation for badges */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.nav-link .badge {
    animation: pulse 2s infinite;
}

/* Hover effects for icons */
.nav-link:hover i {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}
</style>

<script>
// Mobile sidebar toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const navbarToggler = document.querySelector('.navbar-toggler');
    
    if (window.innerWidth <= 767) {
        // Create backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(backdrop);
        
        // Toggle sidebar
        navbarToggler?.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        });
        
        // Close sidebar when clicking backdrop
        backdrop.addEventListener('click', function() {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        });
        
        // Close sidebar when clicking on links (mobile)
        sidebar.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (!this.hasAttribute('data-bs-toggle')) {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                }
            });
        });
    }
});

// Language switcher
document.querySelectorAll('[href*="lang="]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const url = new URL(window.location.href);
        const lang = this.href.split('lang=')[1];
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
    });
});
</script>