</main>

    <!-- Footer -->
    <footer class="footer bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Company Info -->
                <div class="col-lg-4">
                    <div class="footer-section">
                        <h5 class="fw-bold mb-3">
                            <img src="<?php echo SITE_URL; ?>/assets/images/logo-white.png" alt="<?php echo escape($site_title); ?>" height="30" class="me-2">
                            <?php echo escape($site_title); ?>
                        </h5>
                        <p class="text-light mb-3">
                            <?php echo escape(getSetting('footer_description', 'Wir schaffen Verbindungen, die Zukunft gestalten. Ihre Experten für individuelle Personalvermittlung.')); ?>
                        </p>
                        <div class="social-links">
                            <?php if (getSetting('facebook_url')): ?>
                            <a href="<?php echo getSetting('facebook_url'); ?>" class="text-light me-3" target="_blank" rel="noopener">
                                <i class="fab fa-facebook-f fa-lg"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (getSetting('linkedin_url')): ?>
                            <a href="<?php echo getSetting('linkedin_url'); ?>" class="text-light me-3" target="_blank" rel="noopener">
                                <i class="fab fa-linkedin-in fa-lg"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (getSetting('xing_url')): ?>
                            <a href="<?php echo getSetting('xing_url'); ?>" class="text-light me-3" target="_blank" rel="noopener">
                                <i class="fab fa-xing fa-lg"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (getSetting('instagram_url')): ?>
                            <a href="<?php echo getSetting('instagram_url'); ?>" class="text-light me-3" target="_blank" rel="noopener">
                                <i class="fab fa-instagram fa-lg"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2">
                    <div class="footer-section">
                        <h6 class="fw-bold mb-3">Services</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="<?php echo SITE_URL; ?>/bewerber.php" class="text-light text-decoration-none hover-primary">
                                    Für Bewerber
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo SITE_URL; ?>/arbeitnehmer.php" class="text-light text-decoration-none hover-primary">
                                    Für Arbeitgeber
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo SITE_URL; ?>/jobs.php" class="text-light text-decoration-none hover-primary">
                                    Stellenangebote
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo SITE_URL; ?>/booking.php" class="text-light text-decoration-none hover-primary">
                                    Termin buchen
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Company -->
                <div class="col-lg-2">
                    <div class="footer-section">
                        <h6 class="fw-bold mb-3">Unternehmen</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="<?php echo SITE_URL; ?>" class="text-light text-decoration-none hover-primary">
                                    Über uns
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo SITE_URL; ?>/impressum.php" class="text-light text-decoration-none hover-primary">
                                    Impressum
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo SITE_URL; ?>/datenschutz.php" class="text-light text-decoration-none hover-primary">
                                    Datenschutz
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-4">
                    <div class="footer-section">
                        <h6 class="fw-bold mb-3">Kontakt</h6>
                        <div class="contact-info">
                            <?php if (getSetting('contact_address')): ?>
                            <div class="contact-item mb-2">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                <span class="text-light"><?php echo escape(getSetting('contact_address')); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (getSetting('contact_phone')): ?>
                            <div class="contact-item mb-2">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                <a href="tel:<?php echo getSetting('contact_phone'); ?>" class="text-light text-decoration-none hover-primary">
                                    <?php echo escape(getSetting('contact_phone')); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (getSetting('contact_email')): ?>
                            <div class="contact-item mb-2">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                <a href="mailto:<?php echo getSetting('contact_email'); ?>" class="text-light text-decoration-none hover-primary">
                                    <?php echo escape(getSetting('contact_email')); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (getSetting('business_hours')): ?>
                            <div class="contact-item mb-3">
                                <i class="fas fa-clock me-2 text-primary"></i>
                                <span class="text-light"><?php echo escape(getSetting('business_hours', 'Mo-Fr: 9:00 - 17:00 Uhr')); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Newsletter Signup -->
                        <div class="newsletter-signup mt-3">
                            <h6 class="fw-bold mb-2">Newsletter</h6>
                            <p class="text-light small mb-2">Bleiben Sie über neue Stellenangebote informiert</p>
                            <form class="newsletter-form" id="newsletterForm">
                                <div class="input-group">
                                    <input type="email" class="form-control" placeholder="Ihre E-Mail" required>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <!-- Footer Bottom -->
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <p class="text-light mb-0">
                        <?php echo escape(getSetting('footer_text', '© 2025 Crew of Experts GmbH. Alle Rechte vorbehalten.')); ?>
                    </p>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <div class="footer-links">
                        <a href="<?php echo SITE_URL; ?>/impressum.php" class="text-light text-decoration-none me-3 hover-primary">
                            Impressum
                        </a>
                        <a href="<?php echo SITE_URL; ?>/datenschutz.php" class="text-light text-decoration-none hover-primary">
                            Datenschutz
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-primary rounded-circle position-fixed" style="bottom: 20px; right: 20px; display: none; z-index: 1000;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <!-- Custom JavaScript for this page -->
    <script>
        // Newsletter form submission
        document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            if (validateEmail(email)) {
                // Here you would typically send the email to your backend
                showNotification('Vielen Dank! Sie werden in Kürze unseren Newsletter erhalten.', 'success');
                this.reset();
            } else {
                showNotification('Bitte geben Sie eine gültige E-Mail-Adresse ein.', 'error');
            }
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Back to top button
        const backToTopButton = document.getElementById('backToTop');
        if (backToTopButton) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopButton.style.display = 'block';
                } else {
                    backToTopButton.style.display = 'none';
                }
            });
            
            backToTopButton.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialize popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    </script>
    
    <!-- Analytics (Google Analytics 4) -->
    <?php if (getSetting('google_analytics_id')): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo getSetting('google_analytics_id'); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo getSetting('google_analytics_id'); ?>');
    </script>
    <?php endif; ?>
    
    <!-- Cookie Consent -->
    <?php if (getSetting('cookie_consent_enabled', '1') == '1'): ?>
    <div id="cookieConsent" class="cookie-consent position-fixed bottom-0 start-0 end-0 bg-dark text-white p-3" style="z-index: 9999; display: none;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <p class="mb-0">
                        Diese Website verwendet Cookies, um Ihnen die bestmögliche Erfahrung zu bieten. 
                        <a href="<?php echo SITE_URL; ?>/datenschutz.php" class="text-primary">Mehr erfahren</a>
                    </p>
                </div>
                <div class="col-lg-4 text-end">
                    <button type="button" class="btn btn-primary btn-sm me-2" onclick="acceptCookies()">
                        Akzeptieren
                    </button>
                    <button type="button" class="btn btn-outline-light btn-sm" onclick="declineCookies()">
                        Ablehnen
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Cookie consent functionality
        function showCookieConsent() {
            if (!localStorage.getItem('cookieConsent')) {
                document.getElementById('cookieConsent').style.display = 'block';
            }
        }
        
        function acceptCookies() {
            localStorage.setItem('cookieConsent', 'accepted');
            document.getElementById('cookieConsent').style.display = 'none';
        }
        
        function declineCookies() {
            localStorage.setItem('cookieConsent', 'declined');
            document.getElementById('cookieConsent').style.display = 'none';
        }
        
        // Show cookie consent on page load
        document.addEventListener('DOMContentLoaded', showCookieConsent);
    </script>
    <?php endif; ?>

</body>
</html>