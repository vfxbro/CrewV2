<?php
$page_title = 'Für Bewerber';
$meta_description = 'Entdecken Sie exklusive Karrieremöglichkeiten mit Crew of Experts. Wir begleiten Sie persönlich auf dem Weg zu Ihrem Traumjob.';

require_once 'includes/header.php';

// Get some statistics
$total_jobs = getActiveJobsCount();
$featured_jobs = getFeaturedJobs(3);
?>

<!-- Page Header -->
<section class="page-header bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Für Bewerber</h1>
                <p class="lead mb-0">
                    Wir verstehen, dass ein Job mehr ist als nur eine Stelle – er ist ein wichtiger Teil Ihres Lebens. 
                    Deshalb begleiten wir Sie persönlich auf dem Weg zu Ihrer nächsten Herausforderung.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="stats-box bg-white bg-opacity-10 rounded-3 p-3">
                    <div class="stat-number display-6 fw-bold"><?php echo $total_jobs; ?></div>
                    <div class="stat-label">Aktuelle Stellenangebote</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="benefits-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="h1 fw-bold mb-3">Mit uns bekommst du nicht nur eine Jobvermittlung</h2>
                <p class="lead text-muted">sondern echte Karriereunterstützung</p>
            </div>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4 h-100 bg-white rounded-3 shadow-sm hover-lift">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-star fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Exklusive Jobangebote</h5>
                    <p class="text-muted mb-0">
                        Viele unserer Stellen findest du nicht auf Jobportalen, sondern nur bei uns. 
                        Zugang zu versteckten Arbeitsmärkten.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4 h-100 bg-white rounded-3 shadow-sm hover-lift">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-user-tie fa-3x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Persönliche Karriereberatung</h5>
                    <p class="text-muted mb-0">
                        Wir nehmen uns Zeit, um herauszufinden, was wirklich zu dir passt. 
                        Individuelle Beratung für deine Karriereziele.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4 h-100 bg-white rounded-3 shadow-sm hover-lift">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-file-alt fa-3x text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Bewerbung optimieren</h5>
                    <p class="text-muted mb-0">
                        Lebenslauf-Check & Bewerbungstipps, um deine Chancen zu maximieren. 
                        Professionelle Unterstützung bei allen Unterlagen.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4 h-100 bg-white rounded-3 shadow-sm hover-lift">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-rocket fa-3x text-info"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Schnelle Prozesse</h5>
                    <p class="text-muted mb-0">
                        Kein Warten, keine langen Bewerbungswege – wir bringen dich direkt 
                        zu deinem neuen Arbeitgeber.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 text-center">
                <div class="cta-highlight bg-primary bg-opacity-10 rounded-3 p-4">
                    <h3 class="h4 fw-bold text-primary mb-3">
                        🚀 Lass uns gemeinsam deine Karriere vorantreiben!
                    </h3>
                    <p class="text-muted mb-3">
                        Über 85% unserer Kandidaten finden durch uns ihren Traumjob
                    </p>
                    <a href="<?php echo SITE_URL; ?>/booking.php" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-calendar-alt me-2"></i>Kostenlosen Beratungstermin vereinbaren
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="process-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="h1 fw-bold mb-3">So funktioniert's</h2>
                <p class="lead text-muted">Dein Weg zum Traumjob in 4 einfachen Schritten</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="process-step text-center">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-3">1</span>
                    </div>
                    <div class="step-icon mb-3">
                        <i class="fas fa-calendar-check fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Erstberatung vereinbaren</h5>
                    <p class="text-muted">
                        Kostenloser Termin online oder vor Ort. Wir lernen dich und deine 
                        Wünsche kennen.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="process-step text-center">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-3">2</span>
                    </div>
                    <div class="step-icon mb-3">
                        <i class="fas fa-search fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Passende Stellen finden</h5>
                    <p class="text-muted">
                        Wir durchsuchen unsere exklusive Datenbank nach Positionen, 
                        die perfekt zu dir passen.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="process-step text-center">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-3">3</span>
                    </div>
                    <div class="step-icon mb-3">
                        <i class="fas fa-file-upload fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Bewerbung optimieren</h5>
                    <p class="text-muted">
                        Wir helfen dir dabei, deine Bewerbungsunterlagen zu perfektionieren 
                        und vorzubereiten.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="process-step text-center">
                    <div class="step-number mb-3">
                        <span class="badge bg-success rounded-circle p-3 fs-3">4</span>
                    </div>
                    <div class="step-icon mb-3">
                        <i class="fas fa-handshake fa-2x text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Erfolgreiche Vermittlung</h5>
                    <p class="text-muted">
                        Wir begleiten dich bis zum Vertragsabschluss und darüber hinaus. 
                        Dein Erfolg ist unser Ziel!
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Detail Section -->
<section class="services-detail py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Column -->
            <div class="col-lg-6">
                <h2 class="h2 fw-bold mb-4">Unsere Services für Sie</h2>
                
                <div class="service-list">
                    <div class="service-item d-flex mb-4">
                        <div class="service-icon me-3">
                            <i class="fas fa-comments fa-2x text-primary"></i>
                        </div>
                        <div class="service-content">
                            <h5 class="fw-bold mb-2">Karriereberatung</h5>
                            <p class="text-muted mb-0">
                                Analyse Ihrer Stärken und Entwicklungsmöglichkeiten. 
                                Strategische Planung für Ihre berufliche Laufbahn.
                            </p>
                        </div>
                    </div>
                    
                    <div class="service-item d-flex mb-4">
                        <div class="service-icon me-3">
                            <i class="fas fa-edit fa-2x text-primary"></i>
                        </div>
                        <div class="service-content">
                            <h5 class="fw-bold mb-2">Bewerbungscoaching</h5>
                            <p class="text-muted mb-0">
                                Optimierung von Lebenslauf und Anschreiben. 
                                Vorbereitung auf Vorstellungsgespräche und Assessment Center.
                            </p>
                        </div>
                    </div>
                    
                    <div class="service-item d-flex mb-4">
                        <div class="service-icon me-3">
                            <i class="fas fa-network-wired fa-2x text-primary"></i>
                        </div>
                        <div class="service-content">
                            <h5 class="fw-bold mb-2">Netzwerk-Zugang</h5>
                            <p class="text-muted mb-0">
                                Verbindung zu Top-Unternehmen und Entscheidungsträgern. 
                                Zugang zu verdeckten Stellenmärkten.
                            </p>
                        </div>
                    </div>
                    
                    <div class="service-item d-flex">
                        <div class="service-icon me-3">
                            <i class="fas fa-headset fa-2x text-primary"></i>
                        </div>
                        <div class="service-content">
                            <h5 class="fw-bold mb-2">Nachbetreuung</h5>
                            <p class="text-muted mb-0">
                                Unterstützung auch nach der Stellenbesetzung. 
                                Hilfe bei der Integration im neuen Unternehmen.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="col-lg-6">
                <div class="testimonial-section">
                    <h3 class="h3 fw-bold mb-4">Was unsere Kandidaten sagen</h3>
                    
                    <div class="testimonial-card bg-white p-4 rounded-3 shadow-sm mb-4">
                        <div class="testimonial-content mb-3">
                            <i class="fas fa-quote-left text-primary me-2"></i>
                            <span class="fst-italic">
                                "Durch Crew of Experts habe ich nicht nur einen neuen Job gefunden, 
                                sondern den Karrieresprung geschafft, den ich mir schon lange gewünscht hatte. 
                                Die persönliche Betreuung war einfach großartig!"
                            </span>
                        </div>
                        <div class="testimonial-author d-flex align-items-center">
                            <div class="author-avatar bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <span class="text-white fw-bold">MS</span>
                            </div>
                            <div>
                                <div class="author-name fw-bold">Maria Schmidt</div>
                                <div class="author-title text-muted small">Marketing Managerin</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card bg-white p-4 rounded-3 shadow-sm mb-4">
                        <div class="testimonial-content mb-3">
                            <i class="fas fa-quote-left text-primary me-2"></i>
                            <span class="fst-italic">
                                "Die Beratung war so professionell und die Stellenvermittlung erfolgte 
                                viel schneller als ich erwartet hatte. Binnen 2 Wochen hatte ich 3 Angebote!"
                            </span>
                        </div>
                        <div class="testimonial-author d-flex align-items-center">
                            <div class="author-avatar bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <span class="text-white fw-bold">TK</span>
                            </div>
                            <div>
                                <div class="author-name fw-bold">Thomas Klein</div>
                                <div class="author-title text-muted small">IT-Projektleiter</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistics -->
                    <div class="stats-grid mt-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-item text-center p-3 bg-primary bg-opacity-10 rounded-3">
                                    <div class="stat-number h4 fw-bold text-primary mb-1">85%</div>
                                    <div class="stat-label small text-muted">Erfolgsrate</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item text-center p-3 bg-success bg-opacity-10 rounded-3">
                                    <div class="stat-number h4 fw-bold text-success mb-1">&lt; 4</div>
                                    <div class="stat-label small text-muted">Wochen Ø</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item text-center p-3 bg-warning bg-opacity-10 rounded-3">
                                    <div class="stat-number h4 fw-bold text-warning mb-1">500+</div>
                                    <div class="stat-label small text-muted">Vermittlungen</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item text-center p-3 bg-info bg-opacity-10 rounded-3">
                                    <div class="stat-number h4 fw-bold text-info mb-1">95%</div>
                                    <div class="stat-label small text-muted">Zufriedenheit</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Jobs Section -->
<?php if (!empty($featured_jobs)): ?>
<section class="featured-jobs py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="h2 fw-bold mb-3">Aktuelle Top-Stellenangebote</h2>
                <p class="lead text-muted">Entdecken Sie exklusive Karrieremöglichkeiten</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_jobs as $job): ?>
            <div class="col-lg-4">
                <div class="job-card bg-white rounded-3 shadow-sm p-4 h-100 hover-lift">
                    <div class="job-featured-badge mb-3">
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-star me-1"></i>Top-Angebot
                        </span>
                    </div>
                    
                    <h5 class="fw-bold mb-2">
                        <a href="job.php?id=<?php echo $job['id']; ?>" class="text-decoration-none text-dark">
                            <?php echo escape($job['title']); ?>
                        </a>
                    </h5>
                    
                    <?php if ($job['company']): ?>
                    <p class="text-muted mb-2">
                        <i class="fas fa-building me-1"></i>
                        <?php echo escape($job['company']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if ($job['location']): ?>
                    <p class="text-muted mb-3">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        <?php echo escape($job['location']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <p class="text-muted mb-3">
                        <?php echo createExcerpt($job['short_description'] ?: $job['description'], 100); ?>
                    </p>
                    
                    <div class="mt-auto">
                        <a href="job.php?id=<?php echo $job['id']; ?>" class="btn btn-outline-primary btn-sm">
                            Details ansehen
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-briefcase me-2"></i>
                    Alle Stellenangebote ansehen
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
                <h2 class="h2 fw-bold mb-3">Bereit für den nächsten Karriereschritt?</h2>
                <p class="lead mb-0">
                    Vereinbaren Sie noch heute Ihren kostenlosen Beratungstermin und starten Sie 
                    gemeinsam mit uns in Ihre berufliche Zukunft.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo SITE_URL; ?>/booking.php" class="btn btn-light btn-lg px-4 me-3">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Termin buchen
                </a>
                <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-outline-light btn-lg px-4">
                    <i class="fas fa-search me-2"></i>
                    Jobs ansehen
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Footer
include 'includes/footer.php';
?>