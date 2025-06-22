<?php
// API for handling contact form submissions
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
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name ist erforderlich';
    }
    
    if (empty($email)) {
        $errors[] = 'E-Mail ist erforderlich';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Gültige E-Mail-Adresse ist erforderlich';
    }
    
    if (empty($message)) {
        $errors[] = 'Nachricht ist erforderlich';
    }
    
    // Check for spam (simple honeypot and rate limiting)
    if (isset($_POST['website']) && !empty($_POST['website'])) {
        // Honeypot field filled - likely spam
        echo json_encode(['success' => false, 'message' => 'Spam detected']);
        exit;
    }
    
    // Rate limiting by IP
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $db = Database::getInstance();
    
    // Check if same IP sent message in last 5 minutes
    $recent_submissions = $db->selectOne(
        "SELECT COUNT(*) as count FROM contacts WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)",
        [$client_ip]
    );
    
    if ($recent_submissions && $recent_submissions['count'] >= 3) {
        echo json_encode([
            'success' => false, 
            'message' => 'Zu viele Nachrichten in kurzer Zeit. Bitte warten Sie 5 Minuten.'
        ]);
        exit;
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }
    
    // Save to database
    $contact_data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message
    ];
    
    if (saveContact($contact_data)) {
        // Send notification email to admin
        $admin_email = getSetting('contact_email', ADMIN_EMAIL);
        $site_name = getSetting('site_title', SITE_NAME);
        
        $email_subject = "Neue Kontaktanfrage von {$site_name}";
        $email_body = "
            <h2>Neue Kontaktanfrage</h2>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>E-Mail:</strong> {$email}</p>
            <p><strong>Telefon:</strong> {$phone}</p>
            <p><strong>Betreff:</strong> {$subject}</p>
            <p><strong>Nachricht:</strong></p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
            <hr>
            <p><small>Gesendet von: {$client_ip} am " . date('d.m.Y H:i:s') . "</small></p>
        ";
        
        // Send email (optional - don't fail if email sending fails)
        try {
            sendEmail($admin_email, $email_subject, $email_body, $email);
        } catch (Exception $e) {
            log_error("Failed to send contact notification email: " . $e->getMessage());
        }
        
        // Send auto-reply to user
        try {
            $auto_reply_subject = "Vielen Dank für Ihre Nachricht - {$site_name}";
            $auto_reply_body = "
                <h2>Vielen Dank für Ihre Nachricht!</h2>
                <p>Liebe/r {$name},</p>
                <p>vielen Dank für Ihre Nachricht. Wir haben Ihre Anfrage erhalten und werden uns schnellstmöglich bei Ihnen melden.</p>
                <p><strong>Ihre Nachricht:</strong></p>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
                <hr>
                <p>Mit freundlichen Grüßen<br>
                Ihr Team von {$site_name}</p>
                <p><small>Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.</small></p>
            ";
            
            sendEmail($email, $auto_reply_subject, $auto_reply_body, $admin_email);
        } catch (Exception $e) {
            log_error("Failed to send auto-reply email: " . $e->getMessage());
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Ihre Nachricht wurde erfolgreich gesendet. Wir melden uns schnellstmöglich bei Ihnen.'
        ]);
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Fehler beim Speichern der Nachricht. Bitte versuchen Sie es erneut.'
        ]);
    }
    
} catch (Exception $e) {
    log_error("Contact form error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.'
    ]);
}
?>