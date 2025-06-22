<?php
$page_title = 'Impressum';
$meta_description = 'Impressum und Kontaktdaten der Crew of Experts GmbH. Alle rechtlichen Informationen und Angaben gemäß § 5 TMG.';

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header bg-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="h2 fw-bold mb-0">Impressum</h1>
            </div>
        </div>
    </div>
</section>

<!-- Impressum Content -->
<section class="impressum-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="content-card bg-white rounded-3 shadow-sm p-4">
                    
                    <!-- Company Information -->
                    <div class="company-info mb-5">
                        <h2 class="h4 fw-bold mb-4 text-primary">
                            <i class="fas fa-building me-2"></i>
                            Angaben gemäß § 5 TMG
                        </h2>
                        
                        <div class="company-details">
                            <h3 class="h5 fw-bold mb-3"><?php echo escape(getSetting('site_title', 'Crew of Experts GmbH')); ?></h3>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="info-section">
                                        <h6 class="fw-bold mb-2 text-secondary">Anschrift:</h6>
                                        <address class="mb-0">
                                            <?php echo nl2br(escape(getSetting('contact_address', 'Musterstraße 123<br>12345 Berlin<br>Deutschland'))); ?>
                                        </address>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="info-section">
                                        <h6 class="fw-bold mb-2 text-secondary">Kontakt:</h6>
                                        <p class="mb-1">
                                            <i class="fas fa-phone text-primary me-2"></i>
                                            <a href="tel:<?php echo getSetting('contact_phone', '+49 123 456 7890'); ?>" class="text-decoration-none">
                                                <?php echo escape(getSetting('contact_phone', '+49 123 456 7890')); ?>
                                            </a>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-envelope text-primary me-2"></i>
                                            <a href="mailto:<?php echo getSetting('contact_email', 'info@crew-experts.com'); ?>" class="text-decoration-none">
                                                <?php echo escape(getSetting('contact_email', 'info@crew-experts.com')); ?>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Legal Information -->
                    <div class="legal-info mb-5">
                        <h2 class="h4 fw-bold mb-4 text-primary">
                            <i class="fas fa-gavel me-2"></i>
                            Rechtliche Angaben
                        </h2>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-section">
                                    <h6 class="fw-bold mb-2 text-secondary">Geschäftsführung:</h6>
                                    <p class="mb-0">Max Mustermann, Maria Beispiel</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-section">
                                    <h6 class="fw-bold mb-2 text-secondary">Handelsregister:</h6>
                                    <p class="mb-1">HRB 12345</p>
                                    <p class="mb-0 text-muted small">Amtsgericht Berlin</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-section">
                                    <h6 class="fw-bold mb-2 text-secondary">Umsatzsteuer-ID:</h6>
                                    <p class="mb-0">DE123456789</p>
                                    <p class="mb-0 text-muted small">gemäß § 27a UStG</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-section">
                                    <h6 class="fw-bold mb-2 text-secondary">Wirtschafts-ID:</h6>
                                    <p class="mb-0">DE123456789</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Responsible for Content -->
                    <div class="content-responsibility mb-5">
                        <h2 class="h4 fw-bold mb-4 text-primary">
                            <i class="fas fa-user-edit me-2"></i>
                            Verantwortlich für den Inhalt
                        </h2>
                        
                        <p class="mb-3">
                            Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV:
                        </p>
                        
                        <div class="responsibility-info bg-light p-3 rounded">
                            <p class="mb-1 fw-bold">Max Mustermann</p>
                            <address class="mb-0">
                                <?php echo nl2br(escape(getSetting('contact_address', 'Musterstraße 123<br>12345 Berlin<br>Deutschland'))); ?>
                            </address>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="professional-info mb-5">
                        <h2 class="h4 fw-bold mb-4 text-primary">
                            <i class="fas fa-certificate me-2"></i>
                            Berufsrechtliche Angaben
                        </h2>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-section">
                                    <h6 class="fw-bold mb-2 text-secondary">Berufsbezeichnung:</h6>
                                    <p class="mb-0">Personalvermittlung</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-section">
                                    <h6 class="fw-bold mb-2 text-secondary">Zuständige Kammer:</h6>
                                    <p class="mb-0">IHK Berlin</p>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="info-section">
                                    <h6 class="fw-bold mb-2 text-secondary">Berufsordnung:</h6>
                                    <p class="mb-1">Es gelten folgende berufsrechtliche Regelungen:</p>
                                    <ul class="list-unstyled ms-3">
                                        <li class="mb-1">• Sozialgesetzbuch (SGB) III</li>
                                        <li class="mb-1">• Arbeitnehmerüberlassungsgesetz (AÜG)</li>
                                        <li class="mb-1">• Allgemeines Gleichbehandlungsgesetz (AGG)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dispute Resolution -->
                    <div class="dispute-resolution mb-5">
                        <h2 class="h4 fw-bold mb-4 text-primary">
                            <i class="fas fa-balance-scale me-2"></i>
                            Streitschlichtung
                        </h2>
                        
                        <div class="alert alert-info">
                            <h6 class="fw-bold mb-2">Online-Streitbeilegung (OS):</h6>
                            <p class="mb-2">
                                Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit:
                            </p>
                            <p class="mb-3">
                                <a href="https://ec.europa.eu/consumers/odr/" target="_blank" rel="noopener" class="text-decoration-none">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    https://ec.europa.eu/consumers/odr/
                                </a>
                            </p>
                            <p class="mb-0">
                                Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer 
                                Verbraucherschlichtungsstelle teilzunehmen.
                            </p>
                        </div>
                    </div>

                    <!-- Liability -->
                    <div class="liability mb-5">
                        <h2 class="h4 fw-bold mb-4 text-primary">
                            <i class="fas fa-shield-alt me-2"></i>
                            Haftung für Inhalte
                        </h2>
                        
                        <p class="mb-3">
                            Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten 
                            nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als 
                            Diensteanbieter jedoch nicht unter der Verpflichtung, übermittelte oder gespeicherte 
                            fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine 
                            rechtswidrige Tätigkeit hinweisen.
                        </p>
                        
                        <p class="mb-3">
                            Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den 
                            allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch 
                            erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei 
                            Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.
                        </p>
                    </div>

                    <!-- Links -->
                    <div class="links-liability mb-5">
                        <h2 class="h4 fw-bold mb-4 text-primary">
                            <i class="fas fa-link me-2"></i>
                            Haftung für Links
                        </h2>
                        
                        <p class="mb-3">
                            Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen 
                            Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. 
                            Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der 
                            Seiten verantwortlich.
                        </p>
                        
                        <p class="mb-3">
                            Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße 
                            überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar. 
                            Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete 
                            Anhaltspunkte einer Rechtsverletzung nicht zumutbar.
                        </p>
                    </div>

                    <!-- Copyright -->
                    <div class="copyright mb-5">
                        <h2 class="h4 fw-bold mb-4 text-primary">
                            <i class="fas fa-copyright me-2"></i>
                            Urheberrecht
                        </h2>
                        
                        <p class="mb-3">
                            Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen 
                            dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art 
                            der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen 
                            Zustimmung des jeweiligen Autors bzw. Erstellers.
                        </p>
                        
                        <p class="mb-3">
                            Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch 
                            gestattet. Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden 
                            die Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche 
                            gekennzeichnet.
                        </p>
                    </div>

                    <!-- Contact Section -->
                    <div class="contact-section">
                        <h2 class="h4 fw-bold mb-4 text-primary">
                            <i class="fas fa-envelope me-2"></i>
                            Fragen zum Impressum
                        </h2>
                        
                        <div class="alert alert-light border">
                            <p class="mb-3">
                                Bei Fragen zu diesem Impressum oder anderen rechtlichen Angaben können Sie uns 
                                gerne kontaktieren:
                            </p>
                            
                            <div class="contact-methods">
                                <p class="mb-2">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <strong>Telefon:</strong> 
                                    <a href="tel:<?php echo getSetting('contact_phone'); ?>" class="text-decoration-none">
                                        <?php echo escape(getSetting('contact_phone', '+49 123 456 7890')); ?>
                                    </a>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <strong>E-Mail:</strong> 
                                    <a href="mailto:<?php echo getSetting('contact_email'); ?>" class="text-decoration-none">
                                        <?php echo escape(getSetting('contact_email', 'info@crew-experts.com')); ?>
                                    </a>
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-calendar text-primary me-2"></i>
                                    <strong>Termin vereinbaren:</strong> 
                                    <a href="<?php echo SITE_URL; ?>/booking.php" class="text-decoration-none">
                                        Online-Terminbuchung
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Last Updated -->
                    <div class="last-updated mt-5 pt-4 border-top">
                        <p class="text-muted small mb-0">
                            <i class="fas fa-clock me-1"></i>
                            Stand: <?php echo date('d.m.Y'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Actions -->
<section class="quick-actions py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="<?php echo SITE_URL; ?>/datenschutz.php" class="btn btn-outline-primary">
                        <i class="fas fa-shield-alt me-2"></i>Datenschutzerklärung
                    </a>
                    <a href="<?php echo SITE_URL; ?>/booking.php" class="btn btn-primary">
                        <i class="fas fa-calendar me-2"></i>Termin vereinbaren
                    </a>
                    <a href="<?php echo SITE_URL; ?>/" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>Zur Startseite
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Footer
include 'includes/footer.php';
?>