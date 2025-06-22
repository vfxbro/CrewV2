<?php
$page_title = 'Für Arbeitgeber';
$meta_description = 'Finden Sie die besten Talente für Ihr Unternehmen mit Crew of Experts. Professionelle Personalvermittlung und maßgeschneiderte Recruiting-Lösungen.';

require_once 'includes/header.php';

// Get some statistics
$total_jobs = getActiveJobsCount();
?>

<!-- Page Header -->
<section class="page-header bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Für Arbeitgeber</h1>
                <p class="lead mb-0">
                    Mitarbeiter sind der Schlüssel zu Ihrem Erfolg – aber nicht jeder passt in Ihr Unternehmen. 
                    Wir verstehen Ihre Anforderungen genau und finden die richtige Besetzung, die Ihr Team stärkt.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="stats-box bg-white bg-opacity-10 rounded-3 p-3">
                    <div class="stat-number display-6 fw-bold">500+</div>
                    <div class="stat-label">Erfolgreiche Vermittlungen</div>
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
                <h2 class="h1 fw-bold mb-3">Warum Unternehmen uns vertrauen</h2>
                <p class="lead text-muted">Ihre Vorteile bei der Zusammenarbeit mit Crew of Experts</p>
            </div>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4 h-100 bg-white rounded-3 shadow-sm hover-lift">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-database fa-3x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Exklusive Bewerberdatenbank</h5>
                    <p class="text-muted mb-0">
                        Zugang zu hochqualifizierten Kandidaten, die Sie anderswo nicht finden. 
                        Über 5.000 aktive Profile in unserer Datenbank.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4 h-100 bg-white rounded-3 shadow-sm hover-lift">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-bullseye fa-3x text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Passgenaue Direktvermittlung</h5>
                    <p class="text-muted mb-0">
                        Kein Bewerberchaos, sondern gezielte Vorschläge, die wirklich passen. 
                        Nur vorqualifizierte Kandidaten für Ihre Position.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4 h-100 bg-white rounded-3 shadow-sm hover-lift">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-tachometer-alt fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Schnelligkeit & Effizienz</h5>
                    <p class="text-muted mb-0">
                        Wir besetzen Ihre vakanten Stellen zügig und zuverlässig. 
                        Durchschnittliche Besetzungszeit: unter 4 Wochen.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4 h-100 bg-white rounded-3 shadow-sm hover-lift">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-headset fa-3x text-info"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Persönliche Beratung</h5>
                    <p class="text-muted mb-0">
                        Wir hören zu, analysieren Ihre Bedürfnisse und liefern individuelle Lösungen. 
                        Immer für Sie erreichbar.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 text-center">
                <div class="cta-highlight bg-success bg-opacity-10 rounded-3 p-4">
                    <h3 class="h4 fw-bold text-success mb-3">
                        🚀 Sparen Sie Zeit und gewinnen Sie die besten Talente für Ihr Unternehmen!
                    </h3>
                    <p class="text-muted mb-3">
                        Kostenlose Erstberatung und unverbindliches Angebot
                    </p>
                    <a href="<?php echo SITE_URL; ?>/booking.php" class="btn btn-success btn-lg px-4">
                        <i class="fas fa-phone me-2"></i>Kostenlose Beratung anfragen
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="h1 fw-bold mb-3">Unsere Recruiting-Services</h2>
                <p class="lead text-muted">Maßgeschneiderte Lösungen für Ihre Personalanforderungen</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="service-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="service-icon mb-3">
                        <i class="fas fa-search fa-3x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Executive Search</h4>
                    <p class="text-muted mb-4">
                        Diskrete Suche nach Führungskräften und Spezialisten. 
                        Direktansprache und vertrauliche Verhandlungen.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>C-Level Positionen</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Abteilungsleiter</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Senior Spezialisten</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Diskrete Abwicklung</li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="service-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="service-icon mb-3">
                        <i class="fas fa-users fa-3x text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Direct Recruiting</h4>
                    <p class="text-muted mb-4">
                        Schnelle Besetzung von Fach- und Führungspositionen 
                        durch gezielte Kandidatenansprache.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Fachkräfte</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Projektleiter</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Teamleiter</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Schnelle Besetzung</li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="service-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="service-icon mb-3">
                        <i class="fas fa-cogs fa-3x text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Recruiting Consulting</h4>
                    <p class="text-muted mb-4">
                        Beratung und Optimierung Ihrer internen Recruiting-Prozesse 
                        und HR-Strategien.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Prozessoptimierung</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Employer Branding</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>HR-Strategien</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Marktanalysen</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="process-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="h1 fw-bold mb-3">Unser Recruiting-Prozess</h2>
                <p class="lead text-muted">Strukturiert, transparent und erfolgsorientiert</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-2 col-md-4">
                <div class="process-step text-center">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-4">1</span>
                    </div>
                    <div class="step-icon mb-3">
                        <i class="fas fa-handshake fa-2x text-primary"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Erstberatung</h6>
                    <p class="text-muted small">
                        Analyse Ihrer Anforderungen und Unternehmenskultur
                    </p>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4">
                <div class="process-step text-center">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-4">2</span>
                    </div>
                    <div class="step-icon mb-3">
                        <i class="fas fa-file-contract fa-2x text-primary"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Auftragsklärung</h6>
                    <p class="text-muted small">
                        Detaillierte Stellenbeschreibung und Suchstrategie
                    </p>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4">
                <div class="process-step text-center">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-4">3</span>
                    </div>
                    <div class="step-icon mb-3">
                        <i class="fas fa-search fa-2x text-primary"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Kandidatensuche</h6>
                    <p class="text-muted small">
                        Systematische Suche und Direktansprache
                    </p>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4">
                <div class="process-step text-center">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-4">4</span>
                    </div>
                    <div class="step-icon mb-3">
                        <i class="fas fa-user-check fa-2x text-primary"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Vorqualifikation</h6>
                    <p class="text-muted small">
                        Screening und Bewertung der Kandidaten
                    </p>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4">
                <div class="process-step text-center">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary rounded-circle p-3 fs-4">5</span>
                    </div>
                    <div class="step-icon mb-3">
                        <i class="fas fa-presentation fa-2x text-primary"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Präsentation</h6>
                    <p class="text-muted small">
                        Vorstellung geeigneter Kandidaten
                    </p>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4">
                <div class="process-step text-center">
                    <div class="step-number mb-3">
                        <span class="badge bg-success rounded-circle p-3 fs-4">6</span>
                    </div>
                    <div class="step-icon mb-3">
                        <i class="fas fa-trophy fa-2x text-success"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Erfolgreiche Besetzung</h6>
                    <p class="text-muted small">
                        Vertragsabschluss und Nachbetreuung
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Industries Section -->
<section class="industries-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="h1 fw-bold mb-3">Unsere Branchen-Expertise</h2>
                <p class="lead text-muted">Spezialisiert auf verschiedene Industrien und Bereiche</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-2 col-md-4 col-6">
                <div class="industry-card text-center p-3 bg-white rounded-3 hover-lift">
                    <i class="fas fa-laptop-code fa-2x text-primary mb-2"></i>
                    <h6 class="fw-bold mb-0">IT & Software</h6>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 col-6">
                <div class="industry-card text-center p-3 bg-white rounded-3 hover-lift">
                    <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                    <h6 class="fw-bold mb-0">Finance</h6>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 col-6">
                <div class="industry-card text-center p-3 bg-white rounded-3 hover-lift">
                    <i class="fas fa-cogs fa-2x text-warning mb-2"></i>
                    <h6 class="fw-bold mb-0">Engineering</h6>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 col-6">
                <div class="industry-card text-center p-3 bg-white rounded-3 hover-lift">
                    <i class="fas fa-bullhorn fa-2x text-info mb-2"></i>
                    <h6 class="fw-bold mb-0">Marketing</h6>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 col-6">
                <div class="industry-card text-center p-3 bg-white rounded-3 hover-lift">
                    <i class="fas fa-users fa-2x text-danger mb-2"></i>
                    <h6 class="fw-bold mb-0">Sales</h6>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 col-6">
                <div class="industry-card text-center p-3 bg-white rounded-3 hover-lift">
                    <i class="fas fa-user-tie fa-2x text-dark mb-2"></i>
                    <h6 class="fw-bold mb-0">Management</h6>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="h1 fw-bold mb-3">Das sagen unsere Kunden</h2>
                <p class="lead text-muted">Erfolgsgeschichten von zufriedenen Unternehmen</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="testimonial-card bg-white p-4 rounded-3 shadow-sm h-100">
                    <div class="testimonial-rating mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <blockquote class="blockquote mb-3">
                        <p class="mb-0 fst-italic">
                            "Crew of Experts hat uns dabei geholfen, unseren neuen CTO zu finden. 
                            Der Prozess war professionell und die Kandidatenqualität hervorragend."
                        </p>
                    </blockquote>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="author-avatar bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <span class="text-white fw-bold">AB</span>
                        </div>
                        <div>
                            <div class="author-name fw-bold">Andreas Becker</div>
                            <div class="author-title text-muted small">CEO, TechStart GmbH</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="testimonial-card bg-white p-4 rounded-3 shadow-sm h-100">
                    <div class="testimonial-rating mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <blockquote class="blockquote mb-3">
                        <p class="mb-0 fst-italic">
                            "Schnell, effizient und zielgerichtet. Binnen 3 Wochen hatten wir 
                            unseren neuen Vertriebsleiter gefunden. Genau das, was wir gesucht haben!"
                        </p>
                    </blockquote>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="author-avatar bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <span class="text-white fw-bold">SM</span>
                        </div>
                        <div>
                            <div class="author-name fw-bold">Sabine Müller</div>
                            <div class="author-title text-muted small">HR Director, InnovateCorp</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="testimonial-card bg-white p-4 rounded-3 shadow-sm h-100">
                    <div class="testimonial-rating mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <blockquote class="blockquote mb-3">
                        <p class="mb-0 fst-italic">
                            "Die Beratung war ausgezeichnet und die vorgeschlagenen Kandidaten 
                            passten perfekt zu unserem Unternehmen. Absolute Weiterempfehlung!"
                        </p>
                    </blockquote>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="author-avatar bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <span class="text-dark fw-bold">MW</span>
                        </div>
                        <div>
                            <div class="author-name fw-bold">Michael Wagner</div>
                            <div class="author-title text-muted small">Geschäftsführer, ProServ AG</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="statistics-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="h2 fw-bold mb-3">Unsere Erfolge in Zahlen</h2>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-item text-center">
                    <div class="stat-number display-4 fw-bold mb-2">500+</div>
                    <div class="stat-label h5">Erfolgreiche Vermittlungen</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-item text-center">
                    <div class="stat-number display-4 fw-bold mb-2">150+</div>
                    <div class="stat-label h5">Zufriedene Unternehmen</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-item text-center">
                    <div class="stat-number display-4 fw-bold mb-2">&lt;4</div>
                    <div class="stat-label h5">Wochen Ø Besetzungszeit</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-item text-center">
                    <div class="stat-number display-4 fw-bold mb-2">95%</div>
                    <div class="stat-label h5">Erfolgsquote</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="h2 fw-bold mb-3">Bereit für die besten Talente?</h2>
                <p class="lead text-muted mb-0">
                    Vereinbaren Sie noch heute ein kostenloses Beratungsgespräch und erfahren Sie, 
                    wie wir Ihnen beim Recruiting helfen können.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo SITE_URL; ?>/booking.php" class="btn btn-primary btn-lg px-4 me-3">
                    <i class="fas fa-phone me-2"></i>
                    Kostenlose Beratung
                </a>
                <a href="mailto:<?php echo getSetting('contact_email', 'info@crew-experts.com'); ?>" class="btn btn-outline-primary btn-lg px-4">
                    <i class="fas fa-envelope me-2"></i>
                    E-Mail senden
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Footer
include 'includes/footer.php';
?>