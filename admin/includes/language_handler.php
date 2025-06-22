<?php
// Language handler for admin panel
function handleLanguageChange() {
    if (isset($_GET['change_lang']) && in_array($_GET['change_lang'], ['de', 'en'])) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $new_language = $_GET['change_lang'];
        $_SESSION['admin_language'] = $new_language;
        
        // Update in database if user is logged in
        if (isset($_SESSION['admin_id'])) {
            try {
                $db = Database::getInstance();
                $db->execute(
                    "UPDATE admins SET language = ? WHERE id = ?",
                    [$new_language, $_SESSION['admin_id']]
                );
            } catch (Exception $e) {
                // Log error but don't break the process
                log_error("Failed to update admin language: " . $e->getMessage());
            }
        }
        
        // Build redirect URL without language parameter
        $current_url = $_SERVER['PHP_SELF'];
        $query_params = $_GET;
        unset($query_params['change_lang']);
        
        if (!empty($query_params)) {
            $current_url .= '?' . http_build_query($query_params);
        }
        
        header("Location: " . $current_url);
        exit;
    }
}

// Call this function at the beginning of each admin page
handleLanguageChange();
?>