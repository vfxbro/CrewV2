<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$site_title = getSetting('site_title', 'Crew of Experts GmbH');
$site_description = getSetting('site_description', 'Wir schaffen Verbindungen, die Zukunft gestalten.');
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? escape($page_title) . ' - ' : ''; ?><?php echo escape($site_title); ?></title>
    <meta name="description" content="<?php echo isset($meta_description) ? escape($meta_description) : escape($site_description); ?>">
    <meta name="keywords" content="Personalvermittlung, Jobs, Karriere, Fachkräfte, Stellenvermittlung">
    <meta name="author" content="<?php echo escape($site_title); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:title" content="<?php echo isset($page_title) ? escape($page_title) . ' - ' : ''; ?><?php echo escape($site_title); ?>">
    <meta property="og:description" content="<?php echo isset($meta_description) ? escape($meta_description) : escape($site_description); ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="twitter:title" content="<?php echo isset($page_title) ? escape($page_title) . ' - ' : ''; ?><?php echo escape($site_title); ?>">
    <meta property="twitter:description" content="<?php echo isset($meta_description) ? escape($meta_description) : escape($site_description); ?>">
    <meta property="twitter:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL; ?>/assets/images/apple-touch-icon.png">

    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php echo escape($site_title); ?>",
        "description": "<?php echo escape($site_description); ?>",
        "url": "<?php echo SITE_URL; ?>",
        "logo": "<?php echo SITE_URL; ?>/assets/images/logo.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "<?php echo getSetting('contact_phone', '+49 123 456 7890'); ?>",
            "contactType": "customer service",
            "availableLanguage": "German"
        },
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<?php echo getSetting('contact_address', 'Musterstraße 123'); ?>",
            "addressCountry": "DE"
        },
        "sameAs": [
            "<?php echo getSetting('facebook_url', ''); ?>",
            "<?php echo getSetting('linkedin_url', ''); ?>",
            "<?php echo getSetting('xing_url', ''); ?>"
        ]
    }
    </script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="<?php echo escape($site_title); ?>" height="40" class="me-2">
                <span class="fw-bold text-primary d-none d-sm-inline"><?php echo escape($site_title); ?></span>
            </a>
            
            <!-- Mobile menu button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'index' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>">
                            <i class="fas fa-home me-1"></i>Startseite
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'jobs' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/jobs.php">
                            <i class="fas fa-briefcase me-1"></i>Stellenangebote
                            <?php 
                            $job_count = getActiveJobsCount();
                            if ($job_count > 0): 
                            ?>
                                <span class="badge bg-primary ms-1"><?php echo $job_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'booking' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/booking.php">
                            <i class="fas fa-calendar-alt me-1"></i>Termin buchen
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array($current_page, ['bewerber', 'arbeitnehmer']) ? 'active' : ''; ?>" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-users me-1"></i>Services
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                            <li>
                                <a class="dropdown-item <?php echo $current_page == 'bewerber' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/bewerber.php">
                                    <i class="fas fa-user-tie me-2"></i>Für Bewerber
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page == 'arbeitnehmer' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/arbeitnehmer.php">
                                    <i class="fas fa-building me-2"></i>Für Arbeitgeber
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array($current_page, ['impressum', 'datenschutz']) ? 'active' : ''; ?>" href="#" id="legalDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-info-circle me-1"></i>Info
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="legalDropdown">
                            <li>
                                <a class="dropdown-item <?php echo $current_page == 'impressum' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/impressum.php">
                                    <i class="fas fa-file-alt me-2"></i>Impressum
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $current_page == 'datenschutz' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/datenschutz.php">
                                    <i class="fas fa-shield-alt me-2"></i>Datenschutz
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white px-3 ms-2" href="<?php echo SITE_URL; ?>/booking.php">
                            <i class="fas fa-phone me-1"></i>Kontakt
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb (optional, only on inner pages) -->
    <?php if ($current_page != 'index'): ?>
    <nav aria-label="breadcrumb" class="bg-light py-2">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="<?php echo SITE_URL; ?>" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Startseite
                    </a>
                </li>
                <?php
                $page_titles = [
                    'jobs' => 'Stellenangebote',
                    'booking' => 'Termin buchen',
                    'bewerber' => 'Für Bewerber',
                    'arbeitnehmer' => 'Für Arbeitgeber',
                    'impressum' => 'Impressum',
                    'datenschutz' => 'Datenschutzerklärung'
                ];
                if (isset($page_titles[$current_page])):
                ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo $page_titles[$current_page]; ?>
                </li>
                <?php endif; ?>
            </ol>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Main Content -->
    <main id="main-content">