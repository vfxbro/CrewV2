<?php
$page_title = 'Startseite';
$meta_description = 'Wir schaffen Verbindungen, die Zukunft gestalten. Individuelle Personalvermittlung - Wir bringen Fachkräfte und Unternehmen gezielt zusammen.';

require_once 'includes/header.php';

// Получаем данные для главной страницы
$featured_jobs = getFeaturedJobs(3);
$total_jobs = getActiveJobsCount();
$slides = getAllSlides(true);
?>

<!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold mb-4 animate-fade-in">
                        <?php echo getSetting('hero_title', 'Wir schaffen Verbindungen, die Zukunft gestalten.'); ?>
                    </h1>
                    <h2 class="h4 mb-4 text-light animate-fade-in-delay-1">
                        <?php echo getSetting('hero_subtitle', 'Individuelle Personalvermittlung'); ?>
                    </h2>
                    <p class="lead mb-4 animate-fade-in-delay-2">
                        <?php echo getSetting('hero_description', 'Wir bringen Fachkräfte und Unternehmen gezielt zusammen – effizient und zielgerichtet'); ?>
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 animate-fade-in-delay-3">
                        <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-search me-2"></i>
                            Stellenangebote ansehen
                            <?php if ($total_jobs > 0): ?>
                                <span class="badge bg-primary ms-2"><?php echo $total_jobs; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/booking.php" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Termin vereinbaren
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center animate-fade-in-delay-2">
                    <img src="<?php echo SITE_URL; ?>/assets/images/hero-image.jpg" 
                         alt="Personalvermittlung" 
                         class="img-fluid rounded-3 shadow-lg">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hero Background Pattern -->
    <div class="hero-pattern"></div>
</section>

<!-- Services Section -->
<section class="services-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="h1 fw-bold mb-3">Unsere Services</h2>
                <p class="lead text-muted">Maßgeschneiderte Lösungen für Ihre Karriere und Ihr Unternehmen</p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Für Bewerber -->
            <div class="col-lg-6">
                <div class="service-card h-100 bg-white rounded-3 shadow-sm p-4 hover-lift">
                    <div class="service-icon mb-3">
                        <i class="fas fa-user-tie fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-3">Für Bewerber</h3>
                    <p class="text-muted mb-4">
                        Wir verstehen, dass ein Job mehr ist als nur eine Stelle – er ist ein wichtiger Teil deines Lebens. 
                        Deshalb begleiten wir dich persönlich auf dem Weg zu deiner nächsten Herausforderung.
                    </p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Exklusive Jobangebote</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Persönliche Karriereberatung</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Bewerbungsoptimierung</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Schnelle Prozesse</li>
                    </ul>
                    <div class="text-center">
                        <a href="<?php echo SITE_URL; ?>/bewerber.php" class="btn btn-primary">
                            <i class="fas fa-arrow-right me-2"></i>Mehr erfahren
                        </a>
                    </div>
                    <div class="service-cta mt-3 text-center">
                        <small class="text-primary fw-bold">🚀 Lass uns gemeinsam deine Karriere vorantreiben!</small>
                    </div>
                </div>
            </div>
            
            <!-- Für Arbeitgeber -->
            <div class="col-lg-6">
                <div class="service-card h-100 bg-white rounded-3 shadow-sm p-4 hover-lift">
                    <div class="service-icon mb-3">
                        <i class="fas fa-building fa-3x text-primary"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-3">Für Arbeitgeber</h3>
                    <p class="text-muted mb-4">
                        Mitarbeiter sind der Schlüssel zu Ihrem Erfolg – aber nicht jeder passt in Ihr Unternehmen. 
                        Wir verstehen Ihre Anforderungen genau und finden die richtige Besetzung.
                    </p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Exklusive Bewerberdatenbank</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Passgenaue Direktvermittlung</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Schnelligkeit & Effizienz</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Persönliche Beratung</li>
                    </ul>
                    <div class="text-center">
                        <a href="<?php echo SITE_URL; ?>/arbeitnehmer.php" class="btn btn-primary">
                            <i class="fas fa-arrow-right me-2"></i>Mehr erfahren
                        </a>
                    </div>
                    <div class="service-cta mt-3 text-center">
                        <small class="text-primary fw-bold">🚀 Gewinnen Sie die besten Talente für Ihr Unternehmen!</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="process-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="h1 fw-bold mb-3">In 3 Schritten zu deinem perfekten Job</h2>
                <p class="lead text-muted">Schnell. Persönlich. Passgenau.</p>
                <p class="text-muted">Wir begleiten dich auf deinem Weg zur neuen Herausforderung – unkompliziert, individuell und professionell.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Schritt 1 -->
            <div class="col-lg-4">
                <div class="process-step text-center">
                    <div class="process-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-3">1</span>
                    </div>
                    <div class="process-icon mb-3">
                        <i class="fas fa-comments fa-3x text-primary"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-3">Persönliches Gespräch</h3>
                    <p class="text-muted">
                        Ein Job muss nicht nur fachlich, sondern auch menschlich zu dir passen. 
                        Deshalb nehmen wir uns die Zeit, dich und deine Wünsche genau zu verstehen.
                    </p>
                </div>
            </div>
            
            <!-- Schritt 2 -->
            <div class="col-lg-4">
                <div class="process-step text-center">
                    <div class="process-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-3">2</span>
                    </div>
                    <div class="process-icon mb-3">
                        <i class="fas fa-network-wired fa-3x text-primary"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-3">Passende Vermittlung</h3>
                    <p class="text-muted">
                        Dank unseres exklusiven Netzwerks bringen wir dich mit Unternehmen zusammen, 
                        die wirklich zu dir passen – oft mit Stellen, die du auf Jobportalen nicht findest.
                    </p>
                </div>
            </div>
            
            <!-- Schritt 3 -->
            <div class="col-lg-4">
                <div class="process-step text-center">
                    <div class="process-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-3">3</span>
                    </div>
                    <div class="process-icon mb-3">
                        <i class="fas fa-handshake fa-3x text-primary"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-3">Erfolgreiche Platzierung</h3>
                    <p class="text-muted">
                        Wir begleiten dich bis zum Vertragsabschluss – und darüber hinaus. 
                        Dein Erfolg ist unser Ziel!
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Jobs Section -->
<?php if (!empty($featured_jobs)): ?>
<section class="featured-jobs-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="h1 fw-bold mb-3">Aktuelle Stellenangebote</h2>
                <p class="lead text-muted">
                    Dein neuer Job wartet auf dich! Wir bieten dir exklusive Stellen, 
                    die perfekt zu deinen Fähigkeiten und Zielen passen.
                </p>
                <?php if ($total_jobs > 0): ?>
                <p class="text-primary fw-bold">
                    <i class="fas fa-briefcase me-2"></i>
                    Aktuell <?php echo $total_jobs; ?> offene Stelle<?php echo $total_jobs != 1 ? 'n' : ''; ?> verfügbar
                </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_jobs as $job): ?>
            <div class="col-lg-4">
                <div class="job-card bg-white rounded-3 shadow-sm p-4 h-100 hover-lift">
                    <?php if ($job['featured']): ?>
                    <div class="job-featured-badge mb-2">
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-star me-1"></i>Featured
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <h3 class="h5 fw-bold mb-2">
                        <a href="<?php echo SITE_URL; ?>/job.php?id=<?php echo $job['id']; ?>" 
                           class="text-decoration-none text-dark">
                            <?php echo escape($job['title']); ?>
                        </a>
                    </h3>
                    
                    <?php if ($job['company']): ?>
                    <p class="text-muted mb-2">
                        <i class="fas fa-building me-1"></i>
                        <?php echo escape($job['company']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if ($job['location']): ?>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        <?php echo escape($job['location']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if ($job['job_type']): ?>
                    <p class="text-muted mb-3">
                        <i class="fas fa-clock me-1"></i>
                        <?php 
                        $job_types = [
                            'full_time' => 'Vollzeit',
                            'part_time' => 'Teilzeit',
                            'contract' => 'Befristet',
                            'internship' => 'Praktikum'
                        ];
                        echo $job_types[$job['job_type']] ?? $job['job_type'];
                        ?>
                    </p>
                    <?php endif; ?>
                    
                    <p class="text-muted mb-3">
                        <?php echo createExcerpt($job['short_description'] ?: $job['description'], 120); ?>
                    </p>
                    
                    <div class="mt-auto">
                        <a href="<?php echo SITE_URL; ?>/job.php?id=<?php echo $job['id']; ?>" 
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Details ansehen
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-briefcase me-2"></i>
                    Alle Stellenangebote ansehen
                    <?php if ($total_jobs > count($featured_jobs)): ?>
                        <span class="badge bg-light text-primary ms-2">
                            +<?php echo $total_jobs - count($featured_jobs); ?> weitere
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="h2 fw-bold mb-3">Bereit für den nächsten Schritt?</h2>
                <p class="lead mb-0">
                    Vereinbaren Sie noch heute einen kostenlosen Beratungstermin und lassen Sie uns gemeinsam Ihre berufliche Zukunft gestalten.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo SITE_URL; ?>/booking.php" class="btn btn-light btn-lg px-4">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Jetzt Termin buchen
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Footer
include 'includes/footer.php';
?>