<?php
require_once 'includes/header.php';

// Get job ID from URL
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$job_id) {
    header('Location: jobs.php');
    exit;
}

// Get job details
$job = getJob($job_id);

if (!$job || !$job['is_active']) {
    header('Location: jobs.php');
    exit;
}

// Set page meta data
$page_title = $job['title'];
$meta_description = createExcerpt($job['short_description'] ?: $job['description'], 160);

// Get related jobs
$related_jobs = getAllJobs(true);
$related_jobs = array_filter($related_jobs, function($j) use ($job_id) {
    return $j['id'] != $job_id;
});
$related_jobs = array_slice($related_jobs, 0, 3);

// Job type translations
$job_types = [
    'full_time' => 'Vollzeit',
    'part_time' => 'Teilzeit', 
    'contract' => 'Befristet',
    'internship' => 'Praktikum'
];
?>

<!-- Job Header -->
<section class="job-header py-5 bg-gradient-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="job-breadcrumb mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb text-white-50 mb-0">
                            <li class="breadcrumb-item">
                                <a href="<?php echo SITE_URL; ?>" class="text-white-50 text-decoration-none">
                                    <i class="fas fa-home me-1"></i>Startseite
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo SITE_URL; ?>/jobs.php" class="text-white-50 text-decoration-none">
                                    Stellenangebote
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white" aria-current="page">
                                <?php echo escape($job['title']); ?>
                            </li>
                        </ol>
                    </nav>
                </div>
                
                <div class="job-title-section">
                    <?php if ($job['featured']): ?>
                        <div class="featured-badge mb-3">
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="fas fa-star me-1"></i>Featured Position
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <h1 class="display-5 fw-bold mb-3"><?php echo escape($job['title']); ?></h1>
                    
                    <div class="job-meta d-flex flex-wrap gap-3 mb-4">
                        <?php if ($job['company']): ?>
                            <div class="meta-item">
                                <i class="fas fa-building me-2"></i>
                                <span><?php echo escape($job['company']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($job['location']): ?>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <span><?php echo escape($job['location']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($job['job_type']): ?>
                            <div class="meta-item">
                                <i class="fas fa-clock me-2"></i>
                                <span><?php echo $job_types[$job['job_type']] ?? $job['job_type']; ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($job['salary_range']): ?>
                            <div class="meta-item">
                                <i class="fas fa-euro-sign me-2"></i>
                                <span><?php echo escape($job['salary_range']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 text-lg-end">
                <div class="job-actions">
                    <button type="button" class="btn btn-light btn-lg px-4 mb-2" 
                            data-bs-toggle="modal" data-bs-target="#applicationModal">
                        <i class="fas fa-paper-plane me-2"></i>
                        Jetzt bewerben
                    </button>
                    <div class="job-share mt-3">
                        <p class="mb-2 text-white-50">Stelle teilen:</p>
                        <div class="share-buttons d-flex gap-2 justify-content-lg-end">
                            <a href="#" class="btn btn-outline-light btn-sm" onclick="shareJob('linkedin')">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="#" class="btn btn-outline-light btn-sm" onclick="shareJob('xing')">
                                <i class="fab fa-xing"></i>
                            </a>
                            <a href="#" class="btn btn-outline-light btn-sm" onclick="shareJob('email')">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <button class="btn btn-outline-light btn-sm" onclick="copyJobUrl()">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Job Content -->
<section class="job-content py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Job Description -->
                <?php if ($job['description']): ?>
                <div class="content-section mb-5">
                    <h2 class="h3 fw-bold mb-4">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        Stellenbeschreibung
                    </h2>
                    <div class="job-description">
                        <?php echo nl2br(escape($job['description'])); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Requirements -->
                <?php if ($job['requirements']): ?>
                <div class="content-section mb-5">
                    <h2 class="h3 fw-bold mb-4">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Anforderungen
                    </h2>
                    <div class="job-requirements">
                        <?php echo nl2br(escape($job['requirements'])); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Benefits -->
                <?php if ($job['benefits']): ?>
                <div class="content-section mb-5">
                    <h2 class="h3 fw-bold mb-4">
                        <i class="fas fa-gift text-warning me-2"></i>
                        Was wir bieten
                    </h2>
                    <div class="job-benefits">
                        <?php echo nl2br(escape($job['benefits'])); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Application Process -->
                <div class="content-section mb-5">
                    <h2 class="h3 fw-bold mb-4">
                        <i class="fas fa-route text-info me-2"></i>
                        Bewerbungsprozess
                    </h2>
                    <div class="process-steps">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="process-step d-flex align-items-start">
                                    <div class="step-number me-3">
                                        <span class="badge bg-primary rounded-circle">1</span>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Online-Bewerbung</h6>
                                        <p class="text-muted small mb-0">
                                            Senden Sie uns Ihre vollständigen Unterlagen über unser Bewerbungsformular.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="process-step d-flex align-items-start">
                                    <div class="step-number me-3">
                                        <span class="badge bg-primary rounded-circle">2</span>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Erstgespräch</h6>
                                        <p class="text-muted small mb-0">
                                            Wir lernen uns in einem persönlichen Gespräch kennen.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="process-step d-flex align-items-start">
                                    <div class="step-number me-3">
                                        <span class="badge bg-primary rounded-circle">3</span>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Kundenvorstellung</h6>
                                        <p class="text-muted small mb-0">
                                            Bei Interesse stellen wir Sie unserem Kunden vor.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="process-step d-flex align-items-start">
                                    <div class="step-number me-3">
                                        <span class="badge bg-success rounded-circle">4</span>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Vertragsabschluss</h6>
                                        <p class="text-muted small mb-0">
                                            Wir begleiten Sie bis zum erfolgreichen Abschluss.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Apply -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-rocket me-2"></i>
                            Schnell bewerben
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            Interessiert? Bewerben Sie sich jetzt mit wenigen Klicks!
                        </p>
                        <button type="button" class="btn btn-primary w-100 mb-3" 
                                data-bs-toggle="modal" data-bs-target="#applicationModal">
                            <i class="fas fa-paper-plane me-2"></i>
                            Bewerbung senden
                        </button>
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Bewerbung dauert nur 2 Minuten
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Job Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Job-Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="job-details-list">
                            <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Veröffentlicht:</span>
                                <span class="fw-bold"><?php echo formatDate($job['created_at']); ?></span>
                            </div>
                            
                            <?php if ($job['job_type']): ?>
                            <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Arbeitszeit:</span>
                                <span class="fw-bold"><?php echo $job_types[$job['job_type']] ?? $job['job_type']; ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($job['location']): ?>
                            <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Standort:</span>
                                <span class="fw-bold"><?php echo escape($job['location']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($job['salary_range']): ?>
                            <div class="detail-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Gehalt:</span>
                                <span class="fw-bold text-success"><?php echo escape($job['salary_range']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="detail-item d-flex justify-content-between py-2">
                                <span class="text-muted">Job-ID:</span>
                                <span class="fw-bold">#<?php echo str_pad($job['id'], 4, '0', STR_PAD_LEFT); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            Ihr Ansprechpartner
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="contact-person text-center">
                            <div class="avatar mb-3">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-user fa-2x text-white"></i>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-1">Recruiting Team</h6>
                            <p class="text-muted small mb-3">Crew of Experts</p>
                            
                            <div class="contact-actions">
                                <a href="tel:<?php echo getSetting('contact_phone'); ?>" 
                                   class="btn btn-outline-primary btn-sm w-100 mb-2">
                                    <i class="fas fa-phone me-2"></i>
                                    <?php echo getSetting('contact_phone', '+49 123 456 7890'); ?>
                                </a>
                                <a href="mailto:<?php echo $job['contact_email'] ?: getSetting('contact_email'); ?>" 
                                   class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="fas fa-envelope me-2"></i>
                                    E-Mail senden
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Similar Jobs -->
                <?php if (!empty($related_jobs)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-search me-2"></i>
                            Ähnliche Stellen
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($related_jobs as $related_job): ?>
                        <div class="related-job mb-3 pb-3 <?php echo $related_job !== end($related_jobs) ? 'border-bottom' : ''; ?>">
                            <h6 class="mb-1">
                                <a href="job.php?id=<?php echo $related_job['id']; ?>" 
                                   class="text-decoration-none">
                                    <?php echo escape($related_job['title']); ?>
                                </a>
                            </h6>
                            <?php if ($related_job['company']): ?>
                                <p class="text-muted small mb-1"><?php echo escape($related_job['company']); ?></p>
                            <?php endif; ?>
                            <?php if ($related_job['location']): ?>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?php echo escape($related_job['location']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="text-center mt-3">
                            <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-outline-primary btn-sm">
                                Alle Stellen ansehen
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="h3 fw-bold mb-3">Interesse geweckt?</h2>
                <p class="lead text-muted mb-0">
                    Bewerben Sie sich jetzt oder vereinbaren Sie ein kostenloses Beratungsgespräch 
                    für weitere Karrieremöglichkeiten.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button type="button" class="btn btn-primary btn-lg px-4 me-3" 
                        data-bs-toggle="modal" data-bs-target="#applicationModal">
                    <i class="fas fa-paper-plane me-2"></i>
                    Jetzt bewerben
                </button>
                <a href="<?php echo SITE_URL; ?>/booking.php" class="btn btn-outline-primary btn-lg px-4">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Beratung buchen
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Application Modal -->
<div class="modal fade" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applicationModalLabel">
                    <i class="fas fa-file-upload me-2"></i>
                    Bewerbung für: <?php echo escape($job['title']); ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="applicationForm" class="needs-validation" novalidate>
                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                    
                    <div class="row g-3">
                        <!-- Personal Information -->
                        <div class="col-12">
                            <h6 class="fw-bold mb-3">Persönliche Angaben</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Vorname *</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                            <div class="invalid-feedback">Bitte geben Sie Ihren Vornamen ein.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Nachname *</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                            <div class="invalid-feedback">Bitte geben Sie Ihren Nachnamen ein.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label">E-Mail *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">Bitte geben Sie eine gültige E-Mail-Adresse ein.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        
                        <!-- Files -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold mb-3">Bewerbungsunterlagen</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="cvFile" class="form-label">Lebenslauf * (PDF, DOC, DOCX)</label>
                            <input type="file" class="form-control" id="cvFile" name="cv_file" 
                                   accept=".pdf,.doc,.docx" required>
                            <div class="invalid-feedback">Bitte laden Sie Ihren Lebenslauf hoch.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="coverLetterFile" class="form-label">Anschreiben (PDF, DOC, DOCX)</label>
                            <input type="file" class="form-control" id="coverLetterFile" name="cover_letter_file" 
                                   accept=".pdf,.doc,.docx">
                        </div>
                        
                        <!-- Message -->
                        <div class="col-12 mt-4">
                            <label for="message" class="form-label">Nachricht</label>
                            <textarea class="form-control" id="message" name="message" rows="4" 
                                      placeholder="Teilen Sie uns mit, warum Sie sich für diese Stelle interessieren..."></textarea>
                        </div>
                        
                        <!-- Privacy -->
                        <div class="col-12 mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="privacyCheck" name="privacy_accepted" required>
                                <label class="form-check-label" for="privacyCheck">
                                    Ich habe die <a href="<?php echo SITE_URL; ?>/datenschutz.php" target="_blank">Datenschutzerklärung</a> 
                                    gelesen und stimme der Verarbeitung meiner Daten zu. *
                                </label>
                                <div class="invalid-feedback">Bitte stimmen Sie der Datenschutzerklärung zu.</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Abbrechen
                </button>
                <button type="submit" form="applicationForm" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Bewerbung senden
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Job sharing functions
function shareJob(platform) {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('<?php echo addslashes($job['title']); ?> - <?php echo addslashes($job['company'] ?: getSetting('site_title')); ?>');
    
    let shareUrl = '';
    
    switch(platform) {
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
            break;
        case 'xing':
            shareUrl = `https://www.xing.com/social_plugins/share?url=${url}`;
            break;
        case 'email':
            shareUrl = `mailto:?subject=${title}&body=Ich habe diese interessante Stellenausschreibung gefunden: ${url}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

function copyJobUrl() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        showNotification('Link wurde in die Zwischenablage kopiert!', 'success');
    }).catch(function() {
        showNotification('Fehler beim Kopieren des Links.', 'error');
    });
}

// Application form handling
document.getElementById('applicationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!this.checkValidity()) {
        e.stopPropagation();
        this.classList.add('was-validated');
        return;
    }
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    try {
        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Wird gesendet...';
        
        const response = await fetch('api/application.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Ihre Bewerbung wurde erfolgreich gesendet!', 'success');
            this.reset();
            this.classList.remove('was-validated');
            
            // Close modal after short delay
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('applicationModal')).hide();
            }, 2000);
        } else {
            showNotification(result.message || 'Ein Fehler ist aufgetreten.', 'error');
        }
    } catch (error) {
        showNotification('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.', 'error');
    } finally {
        // Reset button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Bewerbung senden';
    }
});

// File upload preview
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const label = this.closest('.col-md-6').querySelector('label');
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            
            // Add file info
            let infoDiv = this.nextElementSibling?.nextElementSibling?.nextElementSibling;
            if (!infoDiv || !infoDiv.classList.contains('file-info')) {
                infoDiv = document.createElement('div');
                infoDiv.classList.add('file-info', 'mt-2', 'small', 'text-muted');
                this.parentNode.appendChild(infoDiv);
            }
            
            infoDiv.innerHTML = `<i class="fas fa-file me-1"></i>${fileName} (${fileSize} MB)`;
        }
    });
});
</script>

<?php
// Footer  
include 'includes/footer.php';
?>