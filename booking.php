<?php
$page_title = 'Termin buchen';
$meta_description = 'Vereinbaren Sie einen kostenlosen Beratungstermin bei Crew of Experts. Wir beraten Sie gerne persönlich zu Ihren Karrieremöglichkeiten.';

require_once 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'book_appointment') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $service_type = trim($_POST['service_type'] ?? '');
        $booking_date = trim($_POST['booking_date'] ?? '');
        $booking_time = trim($_POST['booking_time'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        $errors = [];
        
        // Validation
        if (empty($name)) $errors[] = 'Name ist erforderlich';
        if (empty($email) || !validateEmail($email)) $errors[] = 'Gültige E-Mail ist erforderlich';
        if (empty($service_type)) $errors[] = 'Service-Typ ist erforderlich';
        if (empty($booking_date)) $errors[] = 'Datum ist erforderlich';
        if (empty($booking_time)) $errors[] = 'Uhrzeit ist erforderlich';
        
        // Check if time slot is still available
        if (empty($errors) && !isTimeSlotAvailable($booking_date, $booking_time)) {
            $errors[] = 'Der gewählte Termin ist leider nicht mehr verfügbar';
        }
        
        if (empty($errors)) {
            $booking_data = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'service_type' => $service_type,
                'booking_date' => $booking_date,
                'booking_time' => $booking_time,
                'message' => $message
            ];
            
            if (saveBooking($booking_data)) {
                // Send confirmation email
                $subject = 'Terminbestätigung - Crew of Experts';
                $email_message = "
                    <h2>Terminbestätigung</h2>
                    <p>Liebe/r $name,</p>
                    <p>vielen Dank für Ihre Terminbuchung. Wir haben Ihren Termin erfolgreich reserviert:</p>
                    <ul>
                        <li><strong>Datum:</strong> " . formatDate($booking_date) . "</li>
                        <li><strong>Uhrzeit:</strong> " . formatTime($booking_time) . "</li>
                        <li><strong>Service:</strong> $service_type</li>
                    </ul>
                    <p>Wir freuen uns auf unser Gespräch!</p>
                    <p>Mit freundlichen Grüßen<br>Ihr Team von Crew of Experts</p>
                ";
                
                sendEmail($email, $subject, $email_message);
                
                $success_message = 'Ihr Termin wurde erfolgreich gebucht! Sie erhalten eine Bestätigung per E-Mail.';
            } else {
                $errors[] = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
            }
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Termin buchen</h1>
                <p class="lead mb-0">
                    Vereinbaren Sie einen kostenlosen Beratungstermin und lassen Sie uns gemeinsam 
                    Ihre berufliche Zukunft gestalten. Unser Team steht Ihnen gerne zur Verfügung.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="contact-info bg-white bg-opacity-10 rounded-3 p-3">
                    <div class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        <span><?php echo getSetting('contact_phone', '+49 123 456 7890'); ?></span>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        <span><?php echo getSetting('contact_email', 'info@crew-experts.com'); ?></span>
                    </div>
                    <div>
                        <i class="fas fa-clock me-2"></i>
                        <span>Mo-Fr: 9:00 - 17:00 Uhr</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Section -->
<section class="booking-section py-5">
    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo escape($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="row g-5">
            <!-- Calendar Section -->
            <div class="col-lg-7">
                <div class="booking-calendar-section">
                    <div class="section-header mb-4">
                        <h3 class="h4 fw-bold mb-2">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            Wählen Sie Datum und Uhrzeit
                        </h3>
                        <p class="text-muted mb-0">
                            Klicken Sie auf ein Datum, um verfügbare Zeiten anzuzeigen. 
                            Termine sind von Montag bis Freitag zwischen 9:00 und 17:00 Uhr verfügbar.
                        </p>
                    </div>
                    
                    <!-- Calendar Container -->
                    <div id="bookingCalendar" class="mb-4">
                        <!-- Calendar will be generated by JavaScript -->
                    </div>
                    
                    <!-- Time Slots Container -->
                    <div id="timeSlots" style="display: none;">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-clock text-primary me-2"></i>
                            Verfügbare Zeiten
                        </h6>
                        <div id="timeSlotsContainer" class="row g-2">
                            <!-- Time slots will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Booking Form Section -->
            <div class="col-lg-5">
                <div class="booking-form-section">
                    <div class="section-header mb-4">
                        <h3 class="h4 fw-bold mb-2">
                            <i class="fas fa-user text-primary me-2"></i>
                            Ihre Kontaktdaten
                        </h3>
                        <p class="text-muted mb-0">
                            Bitte füllen Sie das Formular aus, um Ihren Termin zu bestätigen.
                        </p>
                    </div>
                    
                    <!-- Booking Form -->
                    <div id="bookingFormSection" style="<?php echo isset($success_message) ? 'display: none;' : 'display: block;'; ?>">
                        <form id="bookingForm" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="book_appointment">
                            <input type="hidden" name="booking_date" id="selectedDate" value="">
                            <input type="hidden" name="booking_time" id="selectedTime" value="">
                            
                            <!-- Service Type -->
                            <div class="form-group mb-3">
                                <label for="serviceType" class="form-label fw-bold">
                                    Service-Art *
                                </label>
                                <select class="form-select" id="serviceType" name="service_type" required>
                                    <option value="">Bitte wählen...</option>
                                    <option value="Karriereberatung">Karriereberatung</option>
                                    <option value="Jobvermittlung">Jobvermittlung</option>
                                    <option value="Bewerbungsoptimierung">Bewerbungsoptimierung</option>
                                    <option value="Unternehmen - Personalsuche">Unternehmen - Personalsuche</option>
                                    <option value="Allgemeine Beratung">Allgemeine Beratung</option>
                                </select>
                                <div class="invalid-feedback">Bitte wählen Sie einen Service-Typ aus.</div>
                            </div>
                            
                            <!-- Personal Information -->
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="bookingName" class="form-label fw-bold">
                                            Vollständiger Name *
                                        </label>
                                        <input type="text" class="form-control" id="bookingName" name="name" 
                                               value="<?php echo isset($_POST['name']) ? escape($_POST['name']) : ''; ?>" 
                                               required>
                                        <div class="invalid-feedback">Bitte geben Sie Ihren Namen ein.</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bookingEmail" class="form-label fw-bold">
                                            E-Mail-Adresse *
                                        </label>
                                        <input type="email" class="form-control" id="bookingEmail" name="email" 
                                               value="<?php echo isset($_POST['email']) ? escape($_POST['email']) : ''; ?>" 
                                               required>
                                        <div class="invalid-feedback">Bitte geben Sie eine gültige E-Mail ein.</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bookingPhone" class="form-label fw-bold">
                                            Telefonnummer
                                        </label>
                                        <input type="tel" class="form-control" id="bookingPhone" name="phone" 
                                               value="<?php echo isset($_POST['phone']) ? escape($_POST['phone']) : ''; ?>">
                                        <div class="form-text">Optional, für Rückfragen</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Message -->
                            <div class="form-group mb-4">
                                <label for="bookingMessage" class="form-label fw-bold">
                                    Nachricht
                                </label>
                                <textarea class="form-control" id="bookingMessage" name="message" rows="4" 
                                          placeholder="Teilen Sie uns mit, womit wir Ihnen helfen können oder welche Fragen Sie haben..."><?php echo isset($_POST['message']) ? escape($_POST['message']) : ''; ?></textarea>
                                <div class="form-text">Optional, hilft uns bei der Vorbereitung</div>
                            </div>
                            
                            <!-- Selected Date/Time Display -->
                            <div id="selectedDateTime" class="alert alert-info d-none">
                                <h6 class="fw-bold mb-2">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    Gewählter Termin
                                </h6>
                                <div id="selectedDateTimeText"></div>
                            </div>
                            
                            <!-- Privacy Notice -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="privacyConsent" name="privacy_consent" required>
                                <label class="form-check-label" for="privacyConsent">
                                    Ich stimme der Verarbeitung meiner Daten gemäß der 
                                    <a href="<?php echo SITE_URL; ?>/datenschutz.php" target="_blank" class="text-primary">Datenschutzerklärung</a> zu. *
                                </label>
                                <div class="invalid-feedback">Bitte stimmen Sie der Datenschutzerklärung zu.</div>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100" disabled id="submitBooking">
                                <i class="fas fa-calendar-check me-2"></i>
                                Termin buchen
                            </button>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Ihre Daten werden vertraulich behandelt
                                </small>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Success Message -->
                    <?php if (isset($success_message)): ?>
                    <div class="booking-success text-center py-4">
                        <div class="success-icon mb-3">
                            <i class="fas fa-check-circle fa-4x text-success"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Termin erfolgreich gebucht!</h4>
                        <p class="text-muted mb-4">
                            Sie erhalten in Kürze eine Bestätigung per E-Mail mit allen Details zu Ihrem Termin.
                        </p>
                        <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Zur Startseite
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="benefits-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="h2 fw-bold mb-3">Warum einen Termin vereinbaren?</h2>
                <p class="lead text-muted">
                    Ein persönliches Gespräch bringt Sie Ihrem Traumjob einen großen Schritt näher
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-user-tie fa-3x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Persönliche Beratung</h5>
                    <p class="text-muted mb-0">
                        Individuelle Karriereberatung basierend auf Ihren Fähigkeiten und Zielen
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-search fa-3x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Exklusive Stellen</h5>
                    <p class="text-muted mb-0">
                        Zugang zu Stellenangeboten, die nicht öffentlich ausgeschrieben sind
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-file-alt fa-3x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Bewerbungsoptimierung</h5>
                    <p class="text-muted mb-0">
                        Professionelle Überarbeitung Ihrer Bewerbungsunterlagen
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card text-center p-4">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-handshake fa-3x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Langfristige Betreuung</h5>
                    <p class="text-muted mb-0">
                        Begleitung auch nach der erfolgreichen Stellenvermittlung
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-center mb-5">
                    <h2 class="h2 fw-bold mb-3">Häufige Fragen</h2>
                    <p class="lead text-muted">
                        Antworten auf die wichtigsten Fragen rund um Ihren Beratungstermin
                    </p>
                </div>
                
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                Ist die Beratung wirklich kostenlos?
                            </button>
                        </h3>
                        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ja, unser Erstberatungsgespräch ist für Sie völlig kostenlos und unverbindlich. 
                                Wir finanzieren uns über die Vermittlungsgebühren der Unternehmen, 
                                für Sie entstehen keine Kosten.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                Wie lange dauert ein Beratungsgespräch?
                            </button>
                        </h3>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ein Erstgespräch dauert in der Regel 30-60 Minuten. Wir nehmen uns gerne die Zeit, 
                                die nötig ist, um Sie und Ihre Ziele kennenzulernen und Ihnen einen ersten 
                                Überblick über Ihre Möglichkeiten zu geben.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                Kann ich den Termin auch online führen?
                            </button>
                        </h3>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Selbstverständlich! Wir bieten sowohl persönliche Termine in unserem Büro als auch 
                                Video-Calls über Teams, Zoom oder Skype an. Bei der Terminbuchung können Sie 
                                Ihre Präferenz angeben.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                Was sollte ich zum Termin mitbringen?
                            </button>
                        </h3>
                        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Bringen Sie gerne Ihren aktuellen Lebenslauf mit. Falls vorhanden, können Sie auch 
                                Stellenanzeigen mitbringen, die Sie interessieren. Ansonsten bringen Sie einfach 
                                Ihre Fragen und Vorstellungen mit – wir besprechen alles Weitere gemeinsam.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                Kann ich einen Termin kurzfristig absagen?
                            </button>
                        </h3>
                        <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="faq5" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ja, Sie können Ihren Termin bis zu 24 Stunden vorher kostenfrei absagen oder verschieben. 
                                Rufen Sie uns einfach an oder schreiben Sie eine E-Mail. Bei kurzfristigeren Absagen 
                                kontaktieren Sie uns bitte telefonisch.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Alternative Contact Section -->
<section class="alternative-contact py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="h3 fw-bold mb-3">Lieber direkt anrufen?</h2>
                <p class="mb-0">
                    Kein Problem! Unser Team ist von Montag bis Freitag von 9:00 bis 17:00 Uhr für Sie da.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="tel:<?php echo getSetting('contact_phone'); ?>" class="btn btn-light btn-lg px-4">
                    <i class="fas fa-phone me-2"></i>
                    <?php echo getSetting('contact_phone', '+49 123 456 7890'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<script>
// Custom JavaScript for booking page
document.addEventListener('DOMContentLoaded', function() {
    // Update submit button state when date/time is selected
    function updateSubmitButton() {
        const selectedDate = document.getElementById('selectedDate').value;
        const selectedTime = document.getElementById('selectedTime').value;
        const submitButton = document.getElementById('submitBooking');
        const privacyConsent = document.getElementById('privacyConsent').checked;
        
        if (selectedDate && selectedTime && privacyConsent) {
            submitButton.disabled = false;
            submitButton.classList.remove('btn-secondary');
            submitButton.classList.add('btn-primary');
        } else {
            submitButton.disabled = true;
            submitButton.classList.remove('btn-primary');
            submitButton.classList.add('btn-secondary');
        }
    }
    
    // Override selectDate function to update display
    const originalSelectDate = window.selectDate;
    window.selectDate = function(dateStr) {
        originalSelectDate(dateStr);
        updateSelectedDateTime();
        updateSubmitButton();
    };
    
    // Override selectTime function to update display
    const originalSelectTime = window.selectTime;
    window.selectTime = function(time) {
        originalSelectTime(time);
        updateSelectedDateTime();
        updateSubmitButton();
    };
    
    // Update selected date/time display
    function updateSelectedDateTime() {
        const selectedDate = document.getElementById('selectedDate').value;
        const selectedTime = document.getElementById('selectedTime').value;
        const displayDiv = document.getElementById('selectedDateTime');
        const textDiv = document.getElementById('selectedDateTimeText');
        
        if (selectedDate && selectedTime) {
            const date = new Date(selectedDate);
            const dayNames = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
            const monthNames = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
            
            const dayName = dayNames[date.getDay()];
            const day = date.getDate();
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();
            
            const formattedDate = `${dayName}, ${day}. ${month} ${year}`;
            const formattedTime = selectedTime.substring(0, 5); // Remove seconds
            
            textDiv.innerHTML = `
                <div class="row">
                    <div class="col-sm-6">
                        <strong><i class="fas fa-calendar me-2"></i>Datum:</strong><br>
                        ${formattedDate}
                    </div>
                    <div class="col-sm-6">
                        <strong><i class="fas fa-clock me-2"></i>Uhrzeit:</strong><br>
                        ${formattedTime} Uhr
                    </div>
                </div>
            `;
            
            displayDiv.classList.remove('d-none');
        } else {
            displayDiv.classList.add('d-none');
        }
    }
    
    // Privacy consent checkbox handler
    document.getElementById('privacyConsent').addEventListener('change', updateSubmitButton);
    
    // Form submission handler
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        const selectedDate = document.getElementById('selectedDate').value;
        const selectedTime = document.getElementById('selectedTime').value;
        
        if (!selectedDate || !selectedTime) {
            e.preventDefault();
            showNotification('Bitte wählen Sie ein Datum und eine Uhrzeit aus.', 'warning');
            return false;
        }
    });
    
    // Initialize calendar
    if (document.getElementById('bookingCalendar')) {
        loadBookingCalendar();
    }
    
    // Service type change handler
    document.getElementById('serviceType').addEventListener('change', function() {
        const value = this.value;
        const messageField = document.getElementById('bookingMessage');
        
        // Update placeholder based on service type
        const placeholders = {
            'Karriereberatung': 'Erzählen Sie uns von Ihren beruflichen Zielen und aktuellen Herausforderungen...',
            'Jobvermittlung': 'Beschreiben Sie Ihre Wunschposition und Ihre Qualifikationen...',
            'Bewerbungsoptimierung': 'Welche Bewerbungsunterlagen möchten Sie optimieren lassen?...',
            'Unternehmen - Personalsuche': 'Beschreiben Sie die zu besetzende Position und Ihre Anforderungen...',
            'Allgemeine Beratung': 'Wie können wir Ihnen helfen?...'
        };
        
        if (placeholders[value]) {
            messageField.placeholder = placeholders[value];
        }
    });
});
</script>

<?php
// Footer
include 'includes/footer.php';
?>