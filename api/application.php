<?php
// API for handling job application submissions
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

try {
    // Get form data
    $job_id = intval($_POST['job_id'] ?? 0);
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (!$job_id) {
        $errors[] = 'Ungültige Stellenausschreibung';
    }
    
    if (empty($first_name)) {
        $errors[] = 'Vorname ist erforderlich';
    }
    
    if (empty($last_name)) {
        $errors[] = 'Nachname ist erforderlich';
    }
    
    if (empty($email)) {
        $errors[] = 'E-Mail ist erforderlich';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Gültige E-Mail-Adresse ist erforderlich';
    }
    
    // Check if job exists and is active
    $db = Database::getInstance();
    $job = $db->selectOne("SELECT * FROM jobs WHERE id = ? AND is_active = 1", [$job_id]);
    
    if (!$job) {
        $errors[] = 'Die Stellenausschreibung ist nicht mehr verfügbar';
    }
    
    // File validation
    $cv_file = null;
    $cover_letter_file = null;
    
    // CV file is required
    if (!isset($_FILES['cv_file']) || $_FILES['cv_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Lebenslauf ist erforderlich';
    } else {
        $cv_file = $_FILES['cv_file'];
        
        // Check file size (max 5MB)
        if ($cv_file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'Lebenslauf ist zu groß (max. 5MB)';
        }
        
        // Check file type
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $file_type = mime_content_type($cv_file['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Lebenslauf muss eine PDF, DOC oder DOCX Datei sein';
        }
    }
    
    // Cover letter is optional
    if (isset($_FILES['cover_letter_file']) && $_FILES['cover_letter_file']['error'] === UPLOAD_ERR_OK) {
        $cover_letter_file = $_FILES['cover_letter_file'];
        
        // Check file size (max 5MB)
        if ($cover_letter_file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'Anschreiben ist zu groß (max. 5MB)';
        }
        
        // Check file type
        $file_type = mime_content_type($cover_letter_file['tmp_name']);
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Anschreiben muss eine PDF, DOC oder DOCX Datei sein';
        }
    }
    
    // Rate limiting by email
    $recent_applications = $db->selectOne(
        "SELECT COUNT(*) as count FROM contacts WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
        [$email]
    );
    
    if ($recent_applications && $recent_applications['count'] >= 2) {
        $errors[] = 'Sie haben bereits eine Bewerbung in der letzten Stunde eingereicht. Bitte warten Sie.';
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = '../' . UPLOAD_PATH . 'applications/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filenames
    $application_id = uniqid('app_');
    $cv_filename = null;
    $cover_letter_filename = null;
    
    // Upload CV file
    if ($cv_file) {
        $cv_extension = pathinfo($cv_file['name'], PATHINFO_EXTENSION);
        $cv_filename = $application_id . '_cv.' . $cv_extension;
        $cv_path = $upload_dir . $cv_filename;
        
        if (!move_uploaded_file($cv_file['tmp_name'], $cv_path)) {
            echo json_encode([
                'success' => false,
                'message' => 'Fehler beim Hochladen des Lebenslaufs'
            ]);
            exit;
        }
    }
    
    // Upload cover letter file (if provided)
    if ($cover_letter_file) {
        $cover_extension = pathinfo($cover_letter_file['name'], PATHINFO_EXTENSION);
        $cover_letter_filename = $application_id . '_cover.' . $cover_extension;
        $cover_path = $upload_dir . $cover_letter_filename;
        
        if (!move_uploaded_file($cover_letter_file['tmp_name'], $cover_path)) {
            // If cover letter upload fails, it's not critical, just log it
            log_error("Failed to upload cover letter for application: " . $application_id);
            $cover_letter_filename = null;
        }
    }
    
    // Save application to contacts table (we'll use it for applications too)
    $application_subject = "Bewerbung für: " . $job['title'];
    $application_message = "BEWERBUNG\n\n";
    $application_message .= "Position: " . $job['title'] . "\n";
    $application_message .= "Job-ID: #" . str_pad($job['id'], 4, '0', STR_PAD_LEFT) . "\n";
    if ($job['company']) {
        $application_message .= "Unternehmen: " . $job['company'] . "\n";
    }
    $application_message .= "\nBewerber:\n";
    $application_message .= "Name: " . $first_name . " " . $last_name . "\n";
    $application_message .= "E-Mail: " . $email . "\n";
    if ($phone) {
        $application_message .= "Telefon: " . $phone . "\n";
    }
    $application_message .= "\nAngehängte Dateien:\n";
    $application_message .= "- Lebenslauf: " . ($cv_filename ?: 'Nicht verfügbar') . "\n";
    if ($cover_letter_filename) {
        $application_message .= "- Anschreiben: " . $cover_letter_filename . "\n";
    }
    if ($message) {
        $application_message .= "\nNachricht vom Bewerber:\n" . $message;
    }
    
    $contact_data = [
        'name' => $first_name . ' ' . $last_name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $application_subject,
        'message' => $application_message
    ];
    
    if (saveContact($contact_data)) {
        $contact_id = $db->lastInsertId();
        
        // Send confirmation email to applicant
        $site_name = getSetting('site_title', SITE_NAME);
        $contact_email = getSetting('contact_email', ADMIN_EMAIL);
        
        $applicant_subject = "Bewerbungsbestätigung - {$site_name}";
        $applicant_body = "
            <h2>Vielen Dank für Ihre Bewerbung!</h2>
            <p>Liebe/r {$first_name} {$last_name},</p>
            <p>vielen Dank für Ihre Bewerbung. Wir haben Ihre Unterlagen erfolgreich erhalten:</p>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #0066cc; margin-top: 0;'>Ihre Bewerbungsdetails:</h3>
                <p><strong>Position:</strong> {$job['title']}</p>
                <p><strong>Job-ID:</strong> #" . str_pad($job['id'], 4, '0', STR_PAD_LEFT) . "</p>
                " . ($job['company'] ? "<p><strong>Unternehmen:</strong> {$job['company']}</p>" : "") . "
                <p><strong>Bewerbungs-ID:</strong> #{$contact_id}</p>
                <p><strong>Eingereicht am:</strong> " . date('d.m.Y H:i') . " Uhr</p>
            </div>
            
            <h3>Wie geht es weiter?</h3>
            <ul>
                <li>Wir prüfen Ihre Unterlagen sorgfältig</li>
                <li>Bei Interesse melden wir uns innerhalb von 5 Werktagen bei Ihnen</li>
                <li>Wir führen ein persönliches Gespräch mit Ihnen</li>
                <li>Bei Passung stellen wir Sie unserem Kunden vor</li>
            </ul>
            
            <div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <h4 style='color: #155724; margin-top: 0;'>Wichtige Hinweise:</h4>
                <ul style='color: #155724; margin-bottom: 0;'>
                    <li>Bitte haben Sie Geduld - wir bearbeiten jede Bewerbung individuell</li>
                    <li>Auch bei Absagen erhalten Sie eine Rückmeldung von uns</li>
                    <li>Bei Fragen können Sie uns unter {$contact_email} kontaktieren</li>
                </ul>
            </div>
            
            <hr>
            <p>Mit freundlichen Grüßen<br>
            Ihr Recruiting-Team von {$site_name}</p>
            
            <p><small>Diese E-Mail wurde automatisch generiert. Bei Rückfragen antworten Sie bitte auf diese E-Mail oder kontaktieren Sie uns direkt.</small></p>
        ";
        
        // Send notification email to admin
        $admin_subject = "Neue Bewerbung eingegangen - {$job['title']}";
        $admin_body = "
            <h2>Neue Bewerbung eingegangen</h2>
            
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <h3 style='color: #856404; margin-top: 0;'>Stellenausschreibung:</h3>
                <p><strong>Position:</strong> {$job['title']}</p>
                <p><strong>Job-ID:</strong> #" . str_pad($job['id'], 4, '0', STR_PAD_LEFT) . "</p>
                " . ($job['company'] ? "<p><strong>Unternehmen:</strong> {$job['company']}</p>" : "") . "
                " . ($job['location'] ? "<p><strong>Standort:</strong> {$job['location']}</p>" : "") . "
            </div>
            
            <div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <h3 style='color: #155724; margin-top: 0;'>Bewerberdaten:</h3>
                <p><strong>Name:</strong> {$first_name} {$last_name}</p>
                <p><strong>E-Mail:</strong> {$email}</p>
                " . ($phone ? "<p><strong>Telefon:</strong> {$phone}</p>" : "") . "
                <p><strong>Bewerbungs-ID:</strong> #{$contact_id}</p>
            </div>
            
            <div style='background: #cce5ff; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <h3 style='color: #004085; margin-top: 0;'>Angehängte Dateien:</h3>
                <ul style='color: #004085;'>
                    <li>Lebenslauf: {$cv_filename}</li>
                    " . ($cover_letter_filename ? "<li>Anschreiben: {$cover_letter_filename}</li>" : "<li>Anschreiben: Nicht vorhanden</li>") . "
                </ul>
                <p><small>Dateien befinden sich im Verzeichnis: applications/</small></p>
            </div>
            
            " . ($message ? "
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <h3 style='margin-top: 0;'>Nachricht vom Bewerber:</h3>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
            " : "") . "
            
            <hr>
            <p><small>Bewerbung eingegangen am: " . date('d.m.Y H:i:s') . "<br>
            IP-Adresse: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "</small></p>
            
            <p><a href='" . SITE_URL . "/admin/contacts.php' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Bewerbungen verwalten</a></p>
        ";
        
        // Send emails
        try {
            sendEmail($email, $applicant_subject, $applicant_body);
            
            // Send to job contact email if available, otherwise to admin
            $notification_email = $job['contact_email'] ?: $contact_email;
            sendEmail($notification_email, $admin_subject, $admin_body, $email);
            
        } catch (Exception $e) {
            log_error("Failed to send application emails: " . $e->getMessage());
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Ihre Bewerbung wurde erfolgreich eingereicht! Sie erhalten in Kürze eine Bestätigung per E-Mail.',
            'application_id' => $contact_id
        ]);
        
    } else {
        // If database save failed, clean up uploaded files
        if ($cv_filename && file_exists($upload_dir . $cv_filename)) {
            unlink($upload_dir . $cv_filename);
        }
        if ($cover_letter_filename && file_exists($upload_dir . $cover_letter_filename)) {
            unlink($upload_dir . $cover_letter_filename);
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Fehler beim Speichern der Bewerbung. Bitte versuchen Sie es erneut.'
        ]);
    }
    
} catch (Exception $e) {
    log_error("Application form error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.'
    ]);
}
?>