<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!check_admin_auth()) {
    redirect('login.php');
}

$admin_lang = $_SESSION['admin_language'] ?? 'de';
$db = Database::getInstance();

$action = $_GET['action'] ?? 'list';
$slide_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Ungültiger Sicherheits-Token.';
    } else {
    switch ($_POST['action']) {
        case 'add':
        case 'edit':
            $slide_data = [
                'title' => trim($_POST['title'] ?? ''),
                'subtitle' => trim($_POST['subtitle'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'image_url' => trim($_POST['image_url'] ?? ''),
                'button_text' => trim($_POST['button_text'] ?? ''),
                'button_url' => trim($_POST['button_url'] ?? ''),
                'sort_order' => intval($_POST['sort_order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploaded = uploadFile($_FILES['image_file']);
                if ($uploaded) {
                    $slide_data['image_url'] = $uploaded;
                }
            }

            $errors = [];
            if (!$slide_data['title']) {
                $errors[] = $admin_lang === 'de' ? 'Titel ist erforderlich' : 'Title is required';
            }
            if (!$slide_data['image_url']) {
                $errors[] = $admin_lang === 'de' ? 'Bild-URL ist erforderlich' : 'Image URL required';
            }

            if (empty($errors)) {
                if ($_POST['action'] === 'edit') {
                    $slide_data['id'] = $slide_id;
                }

                if (saveSlide($slide_data)) {
                    $message = $admin_lang === 'de' ? 'Slide gespeichert' : 'Slide saved';
                    if ($_POST['action'] === 'add') {
                        $slide_id = $db->lastInsertId();
                        $action = 'edit';
                    }
                } else {
                    $error = $admin_lang === 'de' ? 'Fehler beim Speichern' : 'Error saving';
                }
            } else {
                $error = implode(', ', $errors);
            }
            break;

        case 'delete':
            if ($slide_id && deleteSlide($slide_id)) {
                $message = $admin_lang === 'de' ? 'Slide gelöscht' : 'Slide deleted';
                $action = 'list';
            } else {
                $error = $admin_lang === 'de' ? 'Fehler beim Löschen' : 'Error deleting';
            }
            break;
    }
    }
}

$slide = null;
if (in_array($action, ['edit', 'delete']) && $slide_id) {
    $slide = getSlide($slide_id);
    if (!$slide && $action !== 'delete') {
        $action = 'list';
    }
}

$slides = getAllSlides();

$translations = [
    'de' => [
        'slides' => 'Slider',
        'add_slide' => 'Neuen Slide hinzufügen',
        'edit_slide' => 'Slide bearbeiten',
        'slide_list' => 'Slider Liste',
        'title' => 'Titel',
        'subtitle' => 'Untertitel',
        'description' => 'Beschreibung',
        'image' => 'Bild URL',
        'button_text' => 'Button Text',
        'button_url' => 'Button URL',
        'sort_order' => 'Reihenfolge',
        'active' => 'Aktiv',
        'actions' => 'Aktionen',
        'save' => 'Speichern',
        'edit' => 'Bearbeiten',
        'delete' => 'Löschen',
        'confirm_delete' => 'Diesen Slide löschen?',
        'no_slides' => 'Keine Slides gefunden'
    ],
    'en' => [
        'slides' => 'Slides',
        'add_slide' => 'Add New Slide',
        'edit_slide' => 'Edit Slide',
        'slide_list' => 'Slide List',
        'title' => 'Title',
        'subtitle' => 'Subtitle',
        'description' => 'Description',
        'image' => 'Image URL',
        'button_text' => 'Button Text',
        'button_url' => 'Button URL',
        'sort_order' => 'Order',
        'active' => 'Active',
        'actions' => 'Actions',
        'save' => 'Save',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Delete this slide?',
        'no_slides' => 'No slides found'
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
                    <i class="fas fa-image text-primary me-2"></i>
                    <?php echo $t['slides']; ?>
                </h1>
                <div class="btn-toolbar">
                    <?php if ($action === 'list'): ?>
                        <a href="?action=add" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i><?php echo $t['add_slide']; ?>
                        </a>
                    <?php else: ?>
                        <a href="slides.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i><?php echo $t['slide_list']; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo escape($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo escape($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($action === 'list'): ?>
                <div class="card admin-card">
                    <div class="card-body table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo $t['title']; ?></th>
                                    <th><?php echo $t['image']; ?></th>
                                    <th><?php echo $t['active']; ?></th>
                                    <th><?php echo $t['actions']; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($slides): ?>
                                    <?php foreach ($slides as $s): ?>
                                    <tr>
                                        <td><?php echo escape($s['title']); ?></td>
                                        <td><?php echo escape($s['image_url']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $s['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $s['is_active'] ? 'Yes' : 'No'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="?action=edit&id=<?php echo $s['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-danger" href="?action=delete&id=<?php echo $s['id']; ?>" onclick="return confirm('<?php echo $t['confirm_delete']; ?>')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center"><?php echo $t['no_slides']; ?></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php elseif (in_array($action, ['add', 'edit'])): ?>
                <div class="card admin-card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $action; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['title']; ?> *</label>
                                <input type="text" class="form-control" name="title" value="<?php echo escape($slide['title'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['subtitle']; ?></label>
                                <input type="text" class="form-control" name="subtitle" value="<?php echo escape($slide['subtitle'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['description']; ?></label>
                                <textarea class="form-control" name="description" rows="3"><?php echo escape($slide['description'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['image']; ?> *</label>
                                <?php if (!empty($slide['image_url'])): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo SITE_URL . '/' . $slide['image_url']; ?>" alt="Slide" class="img-fluid" style="max-height:120px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control mb-2" name="image_file" accept="image/*">
                                <input type="text" class="form-control" name="image_url" value="<?php echo escape($slide['image_url'] ?? ''); ?>" placeholder="Image URL">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['button_text']; ?></label>
                                <input type="text" class="form-control" name="button_text" value="<?php echo escape($slide['button_text'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['button_url']; ?></label>
                                <input type="text" class="form-control" name="button_url" value="<?php echo escape($slide['button_url'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['sort_order']; ?></label>
                                <input type="number" class="form-control" name="sort_order" value="<?php echo escape($slide['sort_order'] ?? 0); ?>">
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?php echo ($slide['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active"><?php echo $t['active']; ?></label>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i><?php echo $t['save']; ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>

