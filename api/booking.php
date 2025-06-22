<?php
// API for handling booking form submissions
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Handle different request types
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get available time slots for a specific date
    if (isset($_GET['action']) && $_GET['action'] === 'get_slots') {
        $date = $_GET['date'] ?? '';
        
        if (!$date || !strtotime($date)) {
            echo json_encode(['success' => false, 'message' => 'Invalid date']);
            exit;
        }
        
        // Check if date is in the future
        if (strtotime($date) < strtotime('today')) {
            echo json_encode(['success' => false, 'message' => 'Date must be in the future']);
            exit;
        }
        
        // Check if it's a weekend
        $dayOfWeek = date('N', strtotime($date));
        if ($dayOfWeek >= 6) { // 6 = Saturday, 7 = Sunday
            echo json_encode(['success' => false, 'slots' => []]);
            exit;
        }
        
        $available_slots = getAvailableTimeSlots($date);
        
        echo json_encode([
            'success' => true,
            'slots' => $available_slots,
            'date' => $date,
            'day_name' => getGermanDayName($date)
        ]);
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $service_type = trim($_POST['service_type'] ?? '');
    $booking_date = trim($_POST['booking_date'] ?? '');
    $booking_time = trim($_POST['booking_time'] ?? '');
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
    
    if (empty($service_type)) {
        $errors[] = 'Service-Typ ist erforderlich';
    }
    
    if (empty($booking_date)) {
        $errors[] = 'Datum ist erforderlich';
    } elseif (!strtotime($booking_date)) {
        $errors[] = 'Gültiges Datum ist erforderlich';
    } elseif (strtotime($booking_date) < strtotime('today')) {
        $errors[] = 'Datum muss in der Zukunft liegen';
    }
    
    if (empty($booking_time)) {
        $errors[] = 'Uhrzeit ist erforderlich';
    }
    
    // Check if time slot is still available
    if (empty($errors) && !isTimeSlotAvailable($booking_date, $booking_time)) {
        $errors[] = 'Der gewählte Termin ist leider nicht mehr verfügbar';
    }
    
    // Check for business hours
    if (empty($errors)) {
        $hour = date('H', strtotime($booking_time));
        if ($hour < BOOKING_START_HOUR || $hour >= BOOKING_END_HOUR) {
            $errors[] = 'Termine sind nur zwischen ' . BOOKING_START_HOUR . ':00 und ' . BOOKING_END_HOUR . ':00 Uhr möglich';
        }
    }
    
    // Check for weekends
    if (empty($errors)) {
        $dayOfWeek = date('N', strtotime($booking_date));
        if ($dayOfWeek >= 6) {
            $errors[] = 'Termine sind nur von Montag bis Freitag möglich';
        }
    }
    
    // Rate limiting by IP
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $db = Database::getInstance();
    
    // Check if same IP made booking in last 10 minutes
    $recent_bookings = $db->selectOne(
        "SELECT COUNT(*) as count FROM bookings WHERE created_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)",
        []
    );
    
    if ($recent_bookings && $recent_bookings['count'] >= 5) {
        $errors[] = 'Zu viele Buchungen in kurzer Zeit. Bitte warten Sie einige Minuten.';
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }
    
    // Save booking to database
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
        $booking_id = $db->lastInsertId();
        
        // Send confirmation email to customer
        $site_name = getSetting('site_title', SITE_NAME);
        $contact_phone = getSetting('contact_phone', '+49 123 456 7890');
        
        $customer_subject = "Terminbestätigung - {$site_name}";
        $customer_body = "
            <h2>Terminbestätigung</h2>
            <p>Liebe/r {$name},</p>
            <p>vielen Dank für Ihre Terminbuchung. Wir haben Ihren Termin erfolgreich reserviert:</p>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #0066cc; margin-top: 0;'>Ihre Termindetails:</h3>
                <p><strong>Service:</strong> {$service_type}</p>
                <p><strong>Datum:</strong> " . formatDate($booking_date) . " (" . getGermanDayName($booking_date) . ")</p>
                <p><strong>Uhrzeit:</strong> " . formatTime($booking_time) . " Uhr</p>
                <p><strong>Buchungs-ID:</strong> #" . str_pad($booking_id, 6, '0', STR_PAD_LEFT) . "</p>
            </div>
            
            <h3>Was passiert als nächstes?</h3>
            <ul>
                <li>Sie erhalten eine weitere E-Mail mit den genauen Details (Online-Meeting Link oder Adresse)</li>
                <li>Bei Fragen können Sie uns unter {$contact_phone} erreichen</li>
                <li>Bitte bringen Sie alle relevanten Unterlagen zum Termin mit</li>
            </ul>
            
            <p><strong>Terminverschiebung oder -absage:</strong><br>
            Falls Sie den Termin verschieben oder absagen möchten, kontaktieren Sie uns bitte mindestens 24 Stunden vorher.</p>
            
            <hr>
            <p>Mit freundlichen Grüßen<br>
            Ihr Team von {$site_name}</p>
        ";
        
        // Send notification email to admin
        $admin_email = getSetting('contact_email', ADMIN_EMAIL);
        $admin_subject = "Neue Terminbuchung - {$site_name}";
        $admin_body = "
            <h2>Neue Terminbuchung eingegangen</h2>
            
            <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <h3 style='color: #856404; margin-top: 0;'>Kundendaten:</h3>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>E-Mail:</strong> {$email}</p>
                <p><strong>Telefon:</strong> {$phone}</p>
            </div>
            
            <div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <h3 style='color: #155724; margin-top: 0;'>Termindetails:</h3>
                <p><strong>Service:</strong> {$service_type}</p>
                <p><strong>Datum:</strong> " . formatDate($booking_date) . " (" . getGermanDayName($booking_date) . ")</p>
                <p><strong>Uhrzeit:</strong> " . formatTime($booking_time) . " Uhr</p>
                <p><strong>Buchungs-ID:</strong> #" . str_pad($booking_id, 6, '0', STR_PAD_LEFT) . "</p>
            </div>
            
            <p><strong>Nachricht vom Kunden:</strong></p>
            <p style='background: #f8f9fa; padding: 10px; border-left: 4px solid #0066cc;'>" . nl2br(htmlspecialchars($message)) . "</p>
            
            <hr>
            <p><small>Buchung erstellt am: " . date('d.m.Y H:i:s') . "<br>
            IP-Adresse: {$client_ip}</small></p>
            
            <p><a href='" . SITE_URL . "/admin/booking.php' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Buchungen verwalten</a></p>
        ";
        
        // Send emails
        try {
            sendEmail($email, $customer_subject, $customer_body);
            sendEmail($admin_email, $admin_subject, $admin_body, $email);
        } catch (Exception $e) {
            log_error("Failed to send booking emails: " . $e->getMessage());
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Ihr Termin wurde erfolgreich gebucht! Sie erhalten in Kürze eine Bestätigung per E-Mail.',
            'booking_id' => $booking_id
        ]);
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Fehler beim Speichern der Buchung. Bitte versuchen Sie es erneut.'
        ]);
    }
    
} catch (Exception $e) {
    log_error("Booking form error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.'
    ]);
}
?>