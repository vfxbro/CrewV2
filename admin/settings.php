<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!check_admin_auth()) {
    redirect('login.php');
}

$admin_lang = $_SESSION['admin_language'] ?? 'de';
$message = '';
$error = '';

$setting_keys = [
    'site_title','site_description','hero_title','hero_subtitle','hero_description',
    'contact_email','contact_phone','contact_address','business_hours',
    'footer_description','footer_text',
    'facebook_url','linkedin_url','xing_url','instagram_url',
    'google_analytics_id','cookie_consent_enabled','booking_enabled','jobs_per_page',
    'cta_title','cta_text','cta_button_text','cta_button_url','default_keywords',
    'logo','logo_white','favicon','apple_icon','hero_image'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Ungültiger Sicherheits-Token.';
    } else {
        foreach ($setting_keys as $key) {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
            $uploaded = uploadFile($_FILES[$key]);
            if ($uploaded) {
                updateSetting($key, $uploaded, 'image');
                continue;
            }
        }

        if (in_array($key, ['cookie_consent_enabled','booking_enabled'])) {
            $value = isset($_POST[$key]) ? '1' : '0';
        } else {
            $value = trim($_POST[$key] ?? '');
        }
        updateSetting($key, $value);
    }
        $message = $admin_lang === 'de' ? 'Einstellungen gespeichert' : 'Settings saved';
    }
}

$current_settings = [];
foreach ($setting_keys as $key) {
    $current_settings[$key] = getSetting($key);
}

$translations = [
    'de' => [
        'settings' => 'Einstellungen',
        'general' => 'Allgemein',
        'contact' => 'Kontakt',
        'social' => 'Soziale Medien',
        'footer' => 'Footer',
        'other' => 'Weitere Einstellungen',
        'site_title' => 'Seitentitel',
        'site_description' => 'Seitenbeschreibung',
        'hero_title' => 'Hero Titel',
        'hero_subtitle' => 'Hero Untertitel',
        'hero_description' => 'Hero Beschreibung',
        'contact_email' => 'Kontakt E-Mail',
        'contact_phone' => 'Telefon',
        'contact_address' => 'Adresse',
        'business_hours' => 'Geschäftszeiten',
        'footer_description' => 'Footer Beschreibung',
        'footer_text' => 'Footer Text',
        'facebook_url' => 'Facebook URL',
        'linkedin_url' => 'LinkedIn URL',
        'xing_url' => 'Xing URL',
        'instagram_url' => 'Instagram URL',
        'google_analytics_id' => 'Google Analytics ID',
        'cookie_consent_enabled' => 'Cookie Hinweis anzeigen',
        'booking_enabled' => 'Buchung aktivieren',
        'jobs_per_page' => 'Jobs pro Seite',
        'cta_title' => 'CTA Titel',
        'cta_text' => 'CTA Text',
        'cta_button_text' => 'CTA Button Text',
        'cta_button_url' => 'CTA Button URL',
        'default_keywords' => 'Standard-Keywords',
        'logo' => 'Logo',
        'logo_white' => 'Logo (hell)',
        'favicon' => 'Favicon',
        'apple_icon' => 'Apple Touch Icon',
        'hero_image' => 'Hero Bild',
        'save' => 'Speichern'
    ],
    'en' => [
        'settings' => 'Settings',
        'general' => 'General',
        'contact' => 'Contact',
        'social' => 'Social Media',
        'footer' => 'Footer',
        'other' => 'Other Settings',
        'site_title' => 'Site Title',
        'site_description' => 'Site Description',
        'hero_title' => 'Hero Title',
        'hero_subtitle' => 'Hero Subtitle',
        'hero_description' => 'Hero Description',
        'contact_email' => 'Contact Email',
        'contact_phone' => 'Phone',
        'contact_address' => 'Address',
        'business_hours' => 'Business Hours',
        'footer_description' => 'Footer Description',
        'footer_text' => 'Footer Text',
        'facebook_url' => 'Facebook URL',
        'linkedin_url' => 'LinkedIn URL',
        'xing_url' => 'Xing URL',
        'instagram_url' => 'Instagram URL',
        'google_analytics_id' => 'Google Analytics ID',
        'cookie_consent_enabled' => 'Show Cookie Consent',
        'booking_enabled' => 'Enable Booking',
        'jobs_per_page' => 'Jobs per Page',
        'cta_title' => 'CTA Title',
        'cta_text' => 'CTA Text',
        'cta_button_text' => 'CTA Button Text',
        'cta_button_url' => 'CTA Button URL',
        'default_keywords' => 'Default Keywords',
        'logo' => 'Logo',
        'logo_white' => 'Logo (light)',
        'favicon' => 'Favicon',
        'apple_icon' => 'Apple Touch Icon',
        'hero_image' => 'Hero Image',
        'save' => 'Save'
    ]
];

$t = $translations[$admin_lang];

include 'includes/admin_header.php';
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 fw-bold">
                    <i class="fas fa-cog text-primary me-2"></i><?php echo $t['settings']; ?>
                </h1>
            </div>
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo escape($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title mb-0"><?php echo $t['general']; ?></h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['site_title']; ?></label>
                            <input type="text" class="form-control" name="site_title" value="<?php echo escape($current_settings['site_title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['site_description']; ?></label>
                            <textarea class="form-control" name="site_description" rows="2"><?php echo escape($current_settings['site_description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['hero_title']; ?></label>
                            <input type="text" class="form-control" name="hero_title" value="<?php echo escape($current_settings['hero_title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['hero_subtitle']; ?></label>
                            <input type="text" class="form-control" name="hero_subtitle" value="<?php echo escape($current_settings['hero_subtitle']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['hero_description']; ?></label>
                            <textarea class="form-control" name="hero_description" rows="2"><?php echo escape($current_settings['hero_description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['logo']; ?></label>
                            <?php if ($current_settings['logo']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo SITE_URL . '/' . $current_settings['logo']; ?>" alt="Logo" height="40">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['logo_white']; ?></label>
                            <?php if ($current_settings['logo_white']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo SITE_URL . '/' . $current_settings['logo_white']; ?>" alt="Logo White" height="40" style="background:#333;padding:4px;border-radius:4px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="logo_white" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['favicon']; ?></label>
                            <?php if ($current_settings['favicon']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo SITE_URL . '/' . $current_settings['favicon']; ?>" alt="Favicon" height="32">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="favicon" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['apple_icon']; ?></label>
                            <?php if ($current_settings['apple_icon']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo SITE_URL . '/' . $current_settings['apple_icon']; ?>" alt="Apple Icon" height="40">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="apple_icon" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['hero_image']; ?></label>
                            <?php if ($current_settings['hero_image']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo SITE_URL . '/' . $current_settings['hero_image']; ?>" alt="Hero" class="img-fluid" style="max-height:120px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="hero_image" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title mb-0"><?php echo $t['contact']; ?></h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['contact_email']; ?></label>
                            <input type="email" class="form-control" name="contact_email" value="<?php echo escape($current_settings['contact_email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['contact_phone']; ?></label>
                            <input type="text" class="form-control" name="contact_phone" value="<?php echo escape($current_settings['contact_phone']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['contact_address']; ?></label>
                            <textarea class="form-control" name="contact_address" rows="2"><?php echo escape($current_settings['contact_address']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['business_hours']; ?></label>
                            <input type="text" class="form-control" name="business_hours" value="<?php echo escape($current_settings['business_hours']); ?>">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title mb-0"><?php echo $t['social']; ?></h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['facebook_url']; ?></label>
                            <input type="text" class="form-control" name="facebook_url" value="<?php echo escape($current_settings['facebook_url']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['linkedin_url']; ?></label>
                            <input type="text" class="form-control" name="linkedin_url" value="<?php echo escape($current_settings['linkedin_url']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['xing_url']; ?></label>
                            <input type="text" class="form-control" name="xing_url" value="<?php echo escape($current_settings['xing_url']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['instagram_url']; ?></label>
                            <input type="text" class="form-control" name="instagram_url" value="<?php echo escape($current_settings['instagram_url']); ?>">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title mb-0"><?php echo $t['footer']; ?></h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['footer_description']; ?></label>
                            <textarea class="form-control" name="footer_description" rows="2"><?php echo escape($current_settings['footer_description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['footer_text']; ?></label>
                            <textarea class="form-control" name="footer_text" rows="2"><?php echo escape($current_settings['footer_text']); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title mb-0">CTA</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['cta_title']; ?></label>
                            <input type="text" class="form-control" name="cta_title" value="<?php echo escape($current_settings['cta_title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['cta_text']; ?></label>
                            <textarea class="form-control" name="cta_text" rows="2"><?php echo escape($current_settings['cta_text']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['cta_button_text']; ?></label>
                            <input type="text" class="form-control" name="cta_button_text" value="<?php echo escape($current_settings['cta_button_text']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['cta_button_url']; ?></label>
                            <input type="text" class="form-control" name="cta_button_url" value="<?php echo escape($current_settings['cta_button_url']); ?>">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="card-title mb-0"><?php echo $t['other']; ?></h5></div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="booking_enabled" name="booking_enabled" value="1" <?php echo $current_settings['booking_enabled'] == '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="booking_enabled"><?php echo $t['booking_enabled']; ?></label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="cookie_consent_enabled" name="cookie_consent_enabled" value="1" <?php echo $current_settings['cookie_consent_enabled'] == '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="cookie_consent_enabled"><?php echo $t['cookie_consent_enabled']; ?></label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['google_analytics_id']; ?></label>
                            <input type="text" class="form-control" name="google_analytics_id" value="<?php echo escape($current_settings['google_analytics_id']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['jobs_per_page']; ?></label>
                            <input type="number" class="form-control" name="jobs_per_page" value="<?php echo escape($current_settings['jobs_per_page']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $t['default_keywords']; ?></label>
                            <input type="text" class="form-control" name="default_keywords" value="<?php echo escape($current_settings['default_keywords']); ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i><?php echo $t['save']; ?>
                </button>
            </form>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
