<?php
$page_title = 'Stellenangebote';
$meta_description = 'Entdecken Sie aktuelle Stellenangebote bei Crew of Experts. Finden Sie Ihren Traumjob in verschiedenen Branchen und Bereichen.';

require_once 'includes/header.php';

// Pagination settings
$jobs_per_page = 12;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $jobs_per_page;

// Filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$job_type = isset($_GET['type']) ? trim($_GET['type']) : '';

// Build query
$where_conditions = ['is_active = 1'];
$params = [];

if (!empty($search)) {
    $where_conditions[] = '(title LIKE ? OR description LIKE ? OR company LIKE ?)';
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($location)) {
    $where_conditions[] = 'location LIKE ?';
    $params[] = '%' . $location . '%';
}

if (!empty($job_type)) {
    $where_conditions[] = 'job_type = ?';
    $params[] = $job_type;
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Get total count for pagination
$db = Database::getInstance();
$count_query = "SELECT COUNT(*) as total FROM jobs $where_clause";
$total_result = $db->selectOne($count_query, $params);
$total_jobs = $total_result ? $total_result['total'] : 0;

// Get jobs for current page
$jobs_query = "SELECT * FROM jobs $where_clause ORDER BY featured DESC, created_at DESC LIMIT $jobs_per_page OFFSET $offset";
$jobs = $db->select($jobs_query, $params);

// Get unique locations for filter
$locations = $db->select("SELECT DISTINCT location FROM jobs WHERE is_active = 1 AND location IS NOT NULL AND location != '' ORDER BY location");

// Pagination data
$pagination = paginate($total_jobs, $jobs_per_page, $current_page);
?>

<!-- Page Header -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Stellenangebote</h1>
                <p class="lead mb-0">
                    Entdecken Sie aktuelle Karrieremöglichkeiten und finden Sie Ihren Traumjob. 
                    Wir haben exklusive Stellen, die perfekt zu Ihren Fähigkeiten passen.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="stats-box bg-white bg-opacity-10 rounded-3 p-3">
                    <div class="stat-number display-6 fw-bold"><?php echo $total_jobs; ?></div>
                    <div class="stat-label">Offene Stelle<?php echo $total_jobs != 1 ? 'n' : ''; ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search and Filters -->
<section class="filters-section py-4 bg-light">
    <div class="container">
        <form method="GET" action="" class="search-form">
            <div class="row g-3 align-items-end">
                <!-- Search -->
                <div class="col-lg-4">
                    <label for="search" class="form-label fw-bold">
                        <i class="fas fa-search me-1"></i>Stichwort
                    </label>
                    <div class="search-box">
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               placeholder="Job, Unternehmen, Fähigkeiten..." 
                               value="<?php echo escape($search); ?>">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
                
                <!-- Location -->
                <div class="col-lg-3">
                    <label for="location" class="form-label fw-bold">
                        <i class="fas fa-map-marker-alt me-1"></i>Standort
                    </label>
                    <select class="form-select" id="location" name="location">
                        <option value="">Alle Standorte</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?php echo escape($loc['location']); ?>" 
                                    <?php echo $location === $loc['location'] ? 'selected' : ''; ?>>
                                <?php echo escape($loc['location']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Job Type -->
                <div class="col-lg-3">
                    <label for="type" class="form-label fw-bold">
                        <i class="fas fa-briefcase me-1"></i>Art
                    </label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Alle Arten</option>
                        <option value="full_time" <?php echo $job_type === 'full_time' ? 'selected' : ''; ?>>Vollzeit</option>
                        <option value="part_time" <?php echo $job_type === 'part_time' ? 'selected' : ''; ?>>Teilzeit</option>
                        <option value="contract" <?php echo $job_type === 'contract' ? 'selected' : ''; ?>>Befristet</option>
                        <option value="internship" <?php echo $job_type === 'internship' ? 'selected' : ''; ?>>Praktikum</option>
                    </select>
                </div>
                
                <!-- Submit -->
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filtern
                    </button>
                </div>
            </div>
            
            <!-- Clear Filters -->
            <?php if (!empty($search) || !empty($location) || !empty($job_type)): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <a href="jobs.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>Filter zurücksetzen
                    </a>
                    <span class="text-muted ms-3">
                        <span id="resultsCount"><?php echo $total_jobs; ?> Ergebnis<?php echo $total_jobs != 1 ? 'se' : ''; ?></span>
                    </span>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
</section>

<!-- Jobs Listing -->
<section class="jobs-listing py-5">
    <div class="container">
        <?php if (empty($jobs)): ?>
            <!-- No Results -->
            <div class="row">
                <div class="col-12">
                    <div class="no-results text-center py-5">
                        <div class="no-results-icon mb-4">
                            <i class="fas fa-search fa-4x text-muted"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-3">Keine Stellenangebote gefunden</h3>
                        <p class="text-muted mb-4">
                            <?php if (!empty($search) || !empty($location) || !empty($job_type)): ?>
                                Leider konnten wir keine Stellenangebote finden, die Ihren Suchkriterien entsprechen.
                                <br>Versuchen Sie es mit anderen Begriffen oder entfernen Sie einige Filter.
                            <?php else: ?>
                                Derzeit sind keine Stellenangebote verfügbar. Schauen Sie später wieder vorbei oder 
                                melden Sie sich für unseren Newsletter an, um über neue Stellen informiert zu werden.
                            <?php endif; ?>
                        </p>
                        <div class="no-results-actions">
                            <?php if (!empty($search) || !empty($location) || !empty($job_type)): ?>
                                <a href="jobs.php" class="btn btn-primary me-3">
                                    <i class="fas fa-refresh me-2"></i>Alle Stellen anzeigen
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo SITE_URL; ?>/booking.php" class="btn btn-outline-primary">
                                <i class="fas fa-calendar-alt me-2"></i>Beratungstermin vereinbaren
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Jobs Grid -->
            <div class="row g-4" id="jobsGrid">
                <?php 
                $job_types = [
                    'full_time' => 'Vollzeit',
                    'part_time' => 'Teilzeit',
                    'contract' => 'Befristet',
                    'internship' => 'Praktikum'
                ];
                
                foreach ($jobs as $job): 
                ?>
                    <div class="col-lg-6 col-xl-4">
                        <div class="job-card bg-white rounded-3 shadow-sm p-4 h-100 hover-lift" data-job-type="<?php echo $job['job_type']; ?>">
                            <!-- Featured Badge -->
                            <?php if ($job['featured']): ?>
                            <div class="job-featured-badge mb-3">
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-star me-1"></i>Featured
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Job Header -->
                            <div class="job-header mb-3">
                                <h3 class="h5 fw-bold mb-2">
                                    <a href="job.php?id=<?php echo $job['id']; ?>" 
                                       class="text-decoration-none text-dark hover-primary">
                                        <?php echo escape($job['title']); ?>
                                    </a>
                                </h3>
                                
                                <?php if ($job['company']): ?>
                                <p class="company text-muted mb-1">
                                    <i class="fas fa-building me-1"></i>
                                    <?php echo escape($job['company']); ?>
                                </p>
                                <?php endif; ?>
                                
                                <?php if ($job['location']): ?>
                                <p class="location text-muted mb-1">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?php echo escape($job['location']); ?>
                                </p>
                                <?php endif; ?>
                                
                                <div class="job-meta d-flex flex-wrap gap-2 mb-2">
                                    <?php if ($job['job_type']): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo $job_types[$job['job_type']] ?? $job['job_type']; ?>
                                    </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($job['salary_range']): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="fas fa-euro-sign me-1"></i>
                                        <?php echo escape($job['salary_range']); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Job Description -->
                            <div class="job-description mb-4">
                                <p class="text-muted mb-0">
                                    <?php echo createExcerpt($job['short_description'] ?: $job['description'], 120); ?>
                                </p>
                            </div>
                            
                            <!-- Job Footer -->
                            <div class="job-footer mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?php echo formatDate($job['created_at']); ?>
                                    </small>
                                    <div class="job-actions">
                                        <a href="job.php?id=<?php echo $job['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Details
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-sm ms-2" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#applicationModal"
                                                onclick="openApplicationModal('<?php echo $job['id']; ?>')">
                                            <i class="fas fa-paper-plane me-1"></i>Bewerben
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <nav aria-label="Stellenangebote Pagination" class="d-flex justify-content-center">
                        <ul class="pagination">
                            <!-- Previous Page -->
                            <?php if ($pagination['has_prev']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['prev_page']])); ?>">
                                        <i class="fas fa-chevron-left me-1"></i>Zurück
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left me-1"></i>Zurück
                                    </span>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Page Numbers -->
                            <?php
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($pagination['total_pages'], $current_page + 2);
                            
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                                <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Next Page -->
                            <?php if ($pagination['has_next']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['next_page']])); ?>">
                                        Weiter<i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        Weiter<i class="fas fa-chevron-right ms-1"></i>
                                    </span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    
                    <!-- Results Info -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Seite <?php echo $current_page; ?> von <?php echo $pagination['total_pages']; ?> 
                            (<?php echo $total_jobs; ?> Stelle<?php echo $total_jobs != 1 ? 'n' : ''; ?> insgesamt)
                        </small>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="h2 fw-bold mb-3">Nicht das Richtige dabei?</h2>
                <p class="lead mb-0">
                    Lassen Sie uns wissen, was Sie suchen! Wir haben oft exklusive Stellenangebote, 
                    die nicht öffentlich ausgeschrieben sind.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo SITE_URL; ?>/booking.php" class="btn btn-light btn-lg px-4 me-3">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Beratungstermin
                </a>
                <a href="<?php echo SITE_URL; ?>/bewerber.php" class="btn btn-outline-light btn-lg px-4">
                    <i class="fas fa-user-tie me-2"></i>
                    Mehr erfahren
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
                    <i class="fas fa-file-upload me-2"></i>Bewerbung senden
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="applicationForm" class="needs-validation" novalidate>
                    <input type="hidden" id="applicationJobId" name="job_id" value="">
                    
                    <div class="row g-3">
                        <!-- Personal Information -->
                        <div class="col-12">
                            <h6 class="fw-bold mb-3">Persönliche Angaben</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="applicantFirstName" class="form-label">Vorname *</label>
                            <input type="text" class="form-control" id="applicantFirstName" name="first_name" required>
                            <div class="invalid-feedback">Bitte geben Sie Ihren Vornamen ein.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="applicantLastName" class="form-label">Nachname *</label>
                            <input type="text" class="form-control" id="applicantLastName" name="last_name" required>
                            <div class="invalid-feedback">Bitte geben Sie Ihren Nachnamen ein.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="applicantEmail" class="form-label">E-Mail *</label>
                            <input type="email" class="form-control" id="applicantEmail" name="email" required>
                            <div class="invalid-feedback">Bitte geben Sie eine gültige E-Mail-Adresse ein.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="applicantPhone" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="applicantPhone" name="phone">
                        </div>
                        
                        <!-- Files -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold mb-3">Bewerbungsunterlagen</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="cvFile" class="form-label">Lebenslauf * (PDF, DOC, DOCX)</label>
                            <input type="file" class="form-control" id="cvFile" name="cv_file" accept=".pdf,.doc,.docx" required>
                            <div class="invalid-feedback">Bitte laden Sie Ihren Lebenslauf hoch.</div>
                            <div class="file-preview-container mt-2"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="coverLetterFile" class="form-label">Anschreiben (PDF, DOC, DOCX)</label>
                            <input type="file" class="form-control" id="coverLetterFile" name="cover_letter_file" accept=".pdf,.doc,.docx">
                            <div class="file-preview-container mt-2"></div>
                        </div>
                        
                        <!-- Message -->
                        <div class="col-12 mt-4">
                            <label for="applicationMessage" class="form-label">Nachricht</label>
                            <textarea class="form-control" id="applicationMessage" name="message" rows="4" 
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
                    <i class="fas fa-file-upload me-2"></i>Bewerbung senden
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// Footer
include 'includes/footer.php';
?>