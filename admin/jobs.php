<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Check authentication
if (!check_admin_auth()) {
    redirect('login.php');
}

$admin_lang = $_SESSION['admin_language'] ?? 'de';
$db = Database::getInstance();

// Handle actions
$action = $_GET['action'] ?? 'list';
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
            case 'edit':
                $job_data = [
                    'title' => trim($_POST['title'] ?? ''),
                    'description' => trim($_POST['description'] ?? ''),
                    'short_description' => trim($_POST['short_description'] ?? ''),
                    'company' => trim($_POST['company'] ?? ''),
                    'location' => trim($_POST['location'] ?? ''),
                    'job_type' => trim($_POST['job_type'] ?? 'full_time'),
                    'salary_range' => trim($_POST['salary_range'] ?? ''),
                    'requirements' => trim($_POST['requirements'] ?? ''),
                    'benefits' => trim($_POST['benefits'] ?? ''),
                    'contact_email' => trim($_POST['contact_email'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'featured' => isset($_POST['featured']) ? 1 : 0
                ];
                
                // Validation
                $errors = [];
                if (empty($job_data['title'])) {
                    $errors[] = $admin_lang === 'de' ? 'Titel ist erforderlich' : 'Title is required';
                }
                if (empty($job_data['description'])) {
                    $errors[] = $admin_lang === 'de' ? 'Beschreibung ist erforderlich' : 'Description is required';
                }
                if ($job_data['contact_email'] && !validateEmail($job_data['contact_email'])) {
                    $errors[] = $admin_lang === 'de' ? 'Gültige E-Mail ist erforderlich' : 'Valid email is required';
                }
                
                if (empty($errors)) {
                    if ($_POST['action'] === 'edit' && $job_id) {
                        $job_data['id'] = $job_id;
                    }
                    
                    if (saveJob($job_data)) {
                        $message = $admin_lang === 'de' 
                            ? 'Stelle wurde erfolgreich gespeichert' 
                            : 'Job saved successfully';
                        
                        if ($_POST['action'] === 'add') {
                            $job_id = $db->lastInsertId();
                            $action = 'edit';
                        }
                    } else {
                        $error = $admin_lang === 'de' 
                            ? 'Fehler beim Speichern der Stelle' 
                            : 'Error saving job';
                    }
                } else {
                    $error = implode(', ', $errors);
                }
                break;
                
            case 'delete':
                if ($job_id && deleteJob($job_id)) {
                    $message = $admin_lang === 'de' 
                        ? 'Stelle wurde erfolgreich gelöscht' 
                        : 'Job deleted successfully';
                    $action = 'list';
                } else {
                    $error = $admin_lang === 'de' 
                        ? 'Fehler beim Löschen der Stelle' 
                        : 'Error deleting job';
                }
                break;
                
            case 'toggle_status':
                if ($job_id) {
                    $job = getJob($job_id);
                    if ($job) {
                        $new_status = $job['is_active'] ? 0 : 1;
                        $db->execute("UPDATE jobs SET is_active = ? WHERE id = ?", [$new_status, $job_id]);
                        $message = $admin_lang === 'de' 
                            ? 'Status wurde aktualisiert' 
                            : 'Status updated';
                    }
                }
                break;
                
            case 'toggle_featured':
                if ($job_id) {
                    $job = getJob($job_id);
                    if ($job) {
                        $new_featured = $job['featured'] ? 0 : 1;
                        $db->execute("UPDATE jobs SET featured = ? WHERE id = ?", [$new_featured, $job_id]);
                        $message = $admin_lang === 'de' 
                            ? 'Featured-Status wurde aktualisiert' 
                            : 'Featured status updated';
                    }
                }
                break;
        }
    }
}

// Get data based on action
$job = null;
$jobs = [];

if ($action === 'edit' && $job_id) {
    $job = getJob($job_id);
    if (!$job) {
        $action = 'list';
        $error = $admin_lang === 'de' ? 'Stelle nicht gefunden' : 'Job not found';
    }
} elseif ($action === 'list') {
    $jobs = getAllJobs();
}

// Translations
$translations = [
    'de' => [
        'jobs' => 'Stellenangebote',
        'add_job' => 'Neue Stelle hinzufügen',
        'edit_job' => 'Stelle bearbeiten',
        'job_list' => 'Stellenliste',
        'title' => 'Titel',
        'description' => 'Beschreibung',
        'short_description' => 'Kurzbeschreibung',
        'company' => 'Unternehmen',
        'location' => 'Standort',
        'job_type' => 'Art',
        'salary_range' => 'Gehaltsbereich',
        'requirements' => 'Anforderungen',
        'benefits' => 'Vorteile',
        'contact_email' => 'Kontakt-E-Mail',
        'is_active' => 'Aktiv',
        'featured' => 'Featured',
        'actions' => 'Aktionen',
        'save' => 'Speichern',
        'cancel' => 'Abbrechen',
        'edit' => 'Bearbeiten',
        'delete' => 'Löschen',
        'view' => 'Ansehen',
        'status' => 'Status',
        'created' => 'Erstellt',
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'yes' => 'Ja',
        'no' => 'Nein',
        'confirm_delete' => 'Sind Sie sicher, dass Sie diese Stelle löschen möchten?',
        'full_time' => 'Vollzeit',
        'part_time' => 'Teilzeit',
        'contract' => 'Befristet',
        'internship' => 'Praktikum',
        'search' => 'Suchen...',
        'no_jobs' => 'Keine Stellenangebote gefunden'
    ],
    'en' => [
        'jobs' => 'Jobs',
        'add_job' => 'Add New Job',
        'edit_job' => 'Edit Job',
        'job_list' => 'Job List',
        'title' => 'Title',
        'description' => 'Description',
        'short_description' => 'Short Description',
        'company' => 'Company',
        'location' => 'Location',
        'job_type' => 'Type',
        'salary_range' => 'Salary Range',
        'requirements' => 'Requirements',
        'benefits' => 'Benefits',
        'contact_email' => 'Contact Email',
        'is_active' => 'Active',
        'featured' => 'Featured',
        'actions' => 'Actions',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'status' => 'Status',
        'created' => 'Created',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'yes' => 'Yes',
        'no' => 'No',
        'confirm_delete' => 'Are you sure you want to delete this job?',
        'full_time' => 'Full Time',
        'part_time' => 'Part Time',
        'contract' => 'Contract',
        'internship' => 'Internship',
        'search' => 'Search...',
        'no_jobs' => 'No jobs found'
    ]
];

$t = $translations[$admin_lang];

include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
            <!-- Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 fw-bold">
                    <i class="fas fa-briefcase text-primary me-2"></i>
                    <?php echo $t['jobs']; ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <?php if ($action === 'list'): ?>
                        <a href="?action=add" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i><?php echo $t['add_job']; ?>
                        </a>
                    <?php else: ?>
                        <a href="jobs.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i><?php echo $t['job_list']; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo escape($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo escape($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($action === 'list'): ?>
                <!-- Jobs List -->
                <div class="row">
                    <div class="col-12">
                        <div class="card admin-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    <?php echo $t['job_list']; ?>
                                </h5>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 200px;">
                                        <input type="text" id="jobSearch" class="form-control" placeholder="<?php echo $t['search']; ?>">
                                        <div class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($jobs)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted"><?php echo $t['no_jobs']; ?></h5>
                                        <a href="?action=add" class="btn btn-primary mt-3">
                                            <i class="fas fa-plus me-2"></i><?php echo $t['add_job']; ?>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="jobsTable" data-table="sortable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th data-sort="0"><?php echo $t['title']; ?></th>
                                                    <th data-sort="1"><?php echo $t['company']; ?></th>
                                                    <th data-sort="2"><?php echo $t['location']; ?></th>
                                                    <th data-sort="3"><?php echo $t['job_type']; ?></th>
                                                    <th data-sort="4"><?php echo $t['status']; ?></th>
                                                    <th data-sort="5"><?php echo $t['featured']; ?></th>
                                                    <th data-sort="6"><?php echo $t['created']; ?></th>
                                                    <th class="text-end"><?php echo $t['actions']; ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($jobs as $job): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <?php if ($job['featured']): ?>
                                                                    <i class="fas fa-star text-warning me-2" title="Featured"></i>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <div class="fw-bold"><?php echo escape($job['title']); ?></div>
                                                                    <?php if ($job['short_description']): ?>
                                                                        <small class="text-muted">
                                                                            <?php echo escape(createExcerpt($job['short_description'], 80)); ?>
                                                                        </small>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo escape($job['company'] ?: '-'); ?></td>
                                                        <td><?php echo escape($job['location'] ?: '-'); ?></td>
                                                        <td>
                                                            <?php 
                                                            $job_types = [
                                                                'full_time' => $t['full_time'],
                                                                'part_time' => $t['part_time'],
                                                                'contract' => $t['contract'],
                                                                'internship' => $t['internship']
                                                            ];
                                                            echo $job_types[$job['job_type']] ?? $job['job_type'];
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <form method="POST" class="d-inline" onsubmit="return confirm('<?php echo $admin_lang === 'de' ? 'Status ändern?' : 'Change status?'; ?>')">
                                                                <input type="hidden" name="action" value="toggle_status">
                                                                <input type="hidden" name="id" value="<?php echo $job['id']; ?>">
                                                                <button type="submit" class="btn btn-sm <?php echo $job['is_active'] ? 'btn-success' : 'btn-secondary'; ?>">
                                                                    <?php echo $job['is_active'] ? $t['active'] : $t['inactive']; ?>
                                                                </button>
                                                            </form>
                                                        </td>
                                                        <td>
                                                            <form method="POST" class="d-inline" onsubmit="return confirm('<?php echo $admin_lang === 'de' ? 'Featured-Status ändern?' : 'Change featured status?'; ?>')">
                                                                <input type="hidden" name="action" value="toggle_featured">
                                                                <input type="hidden" name="id" value="<?php echo $job['id']; ?>">
                                                                <button type="submit" class="btn btn-sm <?php echo $job['featured'] ? 'btn-warning' : 'btn-outline-warning'; ?>">
                                                                    <i class="fas fa-star"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?php echo formatDate($job['created_at'], 'd.m.Y H:i'); ?>
                                                            </small>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="btn-group" role="group">
                                                                <a href="../job.php?id=<?php echo $job['id']; ?>" 
                                                                   class="btn btn-sm btn-outline-primary" 
                                                                   target="_blank" 
                                                                   title="<?php echo $t['view']; ?>">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="?action=edit&id=<?php echo $job['id']; ?>" 
                                                                   class="btn btn-sm btn-outline-warning" 
                                                                   title="<?php echo $t['edit']; ?>">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form method="POST" class="d-inline" onsubmit="return confirm('<?php echo $t['confirm_delete']; ?>')">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?php echo $job['id']; ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="<?php echo $t['delete']; ?>">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Add/Edit Form -->
                <div class="row">
                    <div class="col-12">
                        <div class="card admin-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?> me-2"></i>
                                    <?php echo $action === 'add' ? $t['add_job'] : $t['edit_job']; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="needs-validation" novalidate>
                                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                                    
                                    <div class="row">
                                        <!-- Basic Information -->
                                        <div class="col-lg-8">
                                            <h6 class="fw-bold mb-3 text-primary">Grundinformationen</h6>
                                            
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label for="title" class="form-label fw-bold">
                                                        <?php echo $t['title']; ?> *
                                                    </label>
                                                    <input type="text" class="form-control" id="title" name="title" 
                                                           value="<?php echo escape($job['title'] ?? ''); ?>" required>
                                                    <div class="invalid-feedback">
                                                        <?php echo $admin_lang === 'de' ? 'Bitte geben Sie einen Titel ein' : 'Please enter a title'; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="company" class="form-label fw-bold">
                                                        <?php echo $t['company']; ?>
                                                    </label>
                                                    <input type="text" class="form-control" id="company" name="company" 
                                                           value="<?php echo escape($job['company'] ?? ''); ?>">
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="location" class="form-label fw-bold">
                                                        <?php echo $t['location']; ?>
                                                    </label>
                                                    <input type="text" class="form-control" id="location" name="location" 
                                                           value="<?php echo escape($job['location'] ?? ''); ?>">
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="job_type" class="form-label fw-bold">
                                                        <?php echo $t['job_type']; ?>
                                                    </label>
                                                    <select class="form-select" id="job_type" name="job_type">
                                                        <option value="full_time" <?php echo ($job['job_type'] ?? '') === 'full_time' ? 'selected' : ''; ?>><?php echo $t['full_time']; ?></option>
                                                        <option value="part_time" <?php echo ($job['job_type'] ?? '') === 'part_time' ? 'selected' : ''; ?>><?php echo $t['part_time']; ?></option>
                                                        <option value="contract" <?php echo ($job['job_type'] ?? '') === 'contract' ? 'selected' : ''; ?>><?php echo $t['contract']; ?></option>
                                                        <option value="internship" <?php echo ($job['job_type'] ?? '') === 'internship' ? 'selected' : ''; ?>><?php echo $t['internship']; ?></option>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="salary_range" class="form-label fw-bold">
                                                        <?php echo $t['salary_range']; ?>
                                                    </label>
                                                    <input type="text" class="form-control" id="salary_range" name="salary_range" 
                                                           value="<?php echo escape($job['salary_range'] ?? ''); ?>"
                                                           placeholder="z.B. 40.000 - 60.000 €">
                                                </div>
                                                
                                                <div class="col-12">
                                                    <label for="contact_email" class="form-label fw-bold">
                                                        <?php echo $t['contact_email']; ?>
                                                    </label>
                                                    <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                                           value="<?php echo escape($job['contact_email'] ?? ''); ?>">
                                                    <div class="form-text">
                                                        <?php echo $admin_lang === 'de' ? 'Optional - falls leer, wird die Standard-E-Mail verwendet' : 'Optional - if empty, default email will be used'; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <label for="short_description" class="form-label fw-bold">
                                                        <?php echo $t['short_description']; ?>
                                                    </label>
                                                    <textarea class="form-control" id="short_description" name="short_description" rows="3"><?php echo escape($job['short_description'] ?? ''); ?></textarea>
                                                    <div class="form-text">
                                                        <?php echo $admin_lang === 'de' ? 'Kurzer Text für Listen und Vorschau (max. 200 Zeichen)' : 'Short text for lists and preview (max. 200 characters)'; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Options -->
                                        <div class="col-lg-4">
                                            <h6 class="fw-bold mb-3 text-primary">Optionen</h6>
                                            
                                            <div class="card border-light">
                                                <div class="card-body">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                               <?php echo ($job['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label fw-bold" for="is_active">
                                                            <?php echo $t['is_active']; ?>
                                                        </label>
                                                        <div class="form-text">
                                                            <?php echo $admin_lang === 'de' ? 'Stelle auf der Website anzeigen' : 'Show job on website'; ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="featured" name="featured" 
                                                               <?php echo ($job['featured'] ?? 0) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label fw-bold" for="featured">
                                                            <?php echo $t['featured']; ?>
                                                        </label>
                                                        <div class="form-text">
                                                            <?php echo $admin_lang === 'de' ? 'Als Top-Stelle hervorheben' : 'Highlight as top job'; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php if ($job): ?>
                                            <div class="card border-light mt-3">
                                                <div class="card-body">
                                                    <h6 class="card-title">Information</h6>
                                                    <p class="card-text small">
                                                        <strong><?php echo $admin_lang === 'de' ? 'Erstellt:' : 'Created:'; ?></strong><br>
                                                        <?php echo formatDate($job['created_at'], 'd.m.Y H:i'); ?>
                                                    </p>
                                                    <p class="card-text small">
                                                        <strong><?php echo $admin_lang === 'de' ? 'Aktualisiert:' : 'Updated:'; ?></strong><br>
                                                        <?php echo formatDate($job['updated_at'], 'd.m.Y H:i'); ?>
                                                    </p>
                                                    <a href="../job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-external-link-alt me-1"></i>
                                                        <?php echo $admin_lang === 'de' ? 'Vorschau' : 'Preview'; ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Description -->
                                        <div class="col-12 mt-4">
                                            <h6 class="fw-bold mb-3 text-primary">Detailbeschreibung</h6>
                                            
                                            <div class="row g-3">
                                                <div class="col-lg-12">
                                                    <label for="description" class="form-label fw-bold">
                                                        <?php echo $t['description']; ?> *
                                                    </label>
                                                    <textarea class="form-control" id="description" name="description" rows="6" required data-autoresize><?php echo escape($job['description'] ?? ''); ?></textarea>
                                                    <div class="invalid-feedback">
                                                        <?php echo $admin_lang === 'de' ? 'Bitte geben Sie eine Beschreibung ein' : 'Please enter a description'; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                    <label for="requirements" class="form-label fw-bold">
                                                        <?php echo $t['requirements']; ?>
                                                    </label>
                                                    <textarea class="form-control" id="requirements" name="requirements" rows="6" data-autoresize><?php echo escape($job['requirements'] ?? ''); ?></textarea>
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                    <label for="benefits" class="form-label fw-bold">
                                                        <?php echo $t['benefits']; ?>
                                                    </label>
                                                    <textarea class="form-control" id="benefits" name="benefits" rows="6" data-autoresize><?php echo escape($job['benefits'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Form Actions -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between">
                                                <a href="jobs.php" class="btn btn-outline-secondary">
                                                    <i class="fas fa-arrow-left me-2"></i><?php echo $t['cancel']; ?>
                                                </a>
                                                <button type="submit" class="btn btn-primary" data-action="save">
                                                    <i class="fas fa-save me-2"></i><?php echo $t['save']; ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
// Search functionality
document.getElementById('jobSearch')?.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#jobsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Auto-resize textareas
document.querySelectorAll('textarea[data-autoresize]').forEach(textarea => {
    function autoResize() {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }
    
    textarea.addEventListener('input', autoResize);
    autoResize(); // Initial resize
});

// Form validation enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.needs-validation');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Focus first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
            form.classList.add('was-validated');
        });
    }
});

// Character counter for short description
const shortDescTextarea = document.getElementById('short_description');
if (shortDescTextarea) {
    const maxChars = 200;
    const counter = document.createElement('div');
    counter.className = 'form-text text-end';
    shortDescTextarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const remaining = maxChars - shortDescTextarea.value.length;
        counter.textContent = `${shortDescTextarea.value.length}/${maxChars} Zeichen`;
        counter.className = remaining < 20 ? 'form-text text-end text-warning' : 'form-text text-end text-muted';
    }
    
    shortDescTextarea.addEventListener('input', updateCounter);
    updateCounter();
}

// Save keyboard shortcut
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        const saveButton = document.querySelector('[data-action="save"]');
        if (saveButton) {
            saveButton.click();
        }
    }
});

// Preview functionality
function previewJob() {
    const title = document.getElementById('title').value;
    const company = document.getElementById('company').value;
    const location = document.getElementById('location').value;
    const description = document.getElementById('description').value;
    
    if (!title || !description) {
        alert('<?php echo $admin_lang === "de" ? "Bitte füllen Sie mindestens Titel und Beschreibung aus" : "Please fill at least title and description"; ?>');
        return;
    }
    
    // Open preview in new window
    const previewWindow = window.open('', '_blank');
    previewWindow.document.write(`
        <html>
        <head>
            <title>Job Preview</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container my-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">${title}</h2>
                        ${company ? `<p class="mb-0"><i class="fas fa-building me-2"></i>${company}</p>` : ''}
                        ${location ? `<p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>${location}</p>` : ''}
                    </div>
                    <div class="card-body">
                        <h5>Stellenbeschreibung</h5>
                        <div>${description.replace(/\n/g, '<br>')}</div>
                    </div>
                </div>
            </div>
        </body>
        </html>
    `);
}

// Add preview button if in edit/add mode
<?php if ($action !== 'list'): ?>
document.addEventListener('DOMContentLoaded', function() {
    const saveButton = document.querySelector('[data-action="save"]');
    if (saveButton) {
        const previewButton = document.createElement('button');
        previewButton.type = 'button';
        previewButton.className = 'btn btn-outline-info me-2';
        previewButton.innerHTML = '<i class="fas fa-eye me-2"></i><?php echo $admin_lang === "de" ? "Vorschau" : "Preview"; ?>';
        previewButton.onclick = previewJob;
        
        saveButton.parentNode.insertBefore(previewButton, saveButton);
    }
});
<?php endif; ?>
</script>

<?php include 'includes/admin_footer.php'; ?>