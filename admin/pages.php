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
$page_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Ungültiger Sicherheits-Token.';
    } else {
    switch ($_POST['action']) {
        case 'add':
        case 'edit':
            $page_data = [
                'slug' => trim($_POST['slug'] ?? ''),
                'title' => trim($_POST['title'] ?? ''),
                'content' => trim($_POST['content'] ?? ''),
                'meta_title' => trim($_POST['meta_title'] ?? ''),
                'meta_description' => trim($_POST['meta_description'] ?? ''),
                'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $errors = [];
            if (!$page_data['title']) {
                $errors[] = $admin_lang === 'de' ? 'Titel ist erforderlich' : 'Title is required';
            }

            if (!$page_data['slug']) {
                $page_data['slug'] = generateSlug($page_data['title']);
            }

            if (!$page_data['slug']) {
                $errors[] = $admin_lang === 'de' ? 'Slug ist erforderlich' : 'Slug is required';
            }

            if (empty($errors)) {
                if ($_POST['action'] === 'edit') {
                    $page_data['id'] = $page_id;
                }

                if (savePage($page_data)) {
                    $message = $admin_lang === 'de' ? 'Seite gespeichert' : 'Page saved';
                    if ($_POST['action'] === 'add') {
                        $page_id = $db->lastInsertId();
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
            if ($page_id && deletePage($page_id)) {
                $message = $admin_lang === 'de' ? 'Seite gelöscht' : 'Page deleted';
                $action = 'list';
            } else {
                $error = $admin_lang === 'de' ? 'Fehler beim Löschen' : 'Error deleting';
            }
            break;
    }
    }
}

$page = null;
if (in_array($action, ['edit', 'delete']) && $page_id) {
    $page = getPageById($page_id);
    if (!$page && $action !== 'delete') {
        $action = 'list';
    }
}

$pages = getAllPages();

$translations = [
    'de' => [
        'pages' => 'Seiten',
        'add_page' => 'Neue Seite hinzufügen',
        'edit_page' => 'Seite bearbeiten',
        'page_list' => 'Seitenliste',
        'slug' => 'Slug',
        'title' => 'Titel',
        'active' => 'Aktiv',
        'actions' => 'Aktionen',
        'save' => 'Speichern',
        'cancel' => 'Abbrechen',
        'edit' => 'Bearbeiten',
        'delete' => 'Löschen',
        'content' => 'Inhalt',
        'meta_title' => 'Meta-Titel',
        'meta_description' => 'Meta-Beschreibung',
        'meta_keywords' => 'Meta-Schlüsselwörter',
        'confirm_delete' => 'Seite wirklich löschen?',
        'no_pages' => 'Keine Seiten gefunden'
    ],
    'en' => [
        'pages' => 'Pages',
        'add_page' => 'Add New Page',
        'edit_page' => 'Edit Page',
        'page_list' => 'Page List',
        'slug' => 'Slug',
        'title' => 'Title',
        'active' => 'Active',
        'actions' => 'Actions',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'content' => 'Content',
        'meta_title' => 'Meta Title',
        'meta_description' => 'Meta Description',
        'meta_keywords' => 'Meta Keywords',
        'confirm_delete' => 'Delete this page?',
        'no_pages' => 'No pages found'
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
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    <?php echo $t['pages']; ?>
                </h1>
                <div class="btn-toolbar">
                    <?php if ($action === 'list'): ?>
                        <a href="?action=add" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i><?php echo $t['add_page']; ?>
                        </a>
                    <?php else: ?>
                        <a href="pages.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i><?php echo $t['page_list']; ?>
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
                                    <th><?php echo $t['slug']; ?></th>
                                    <th><?php echo $t['active']; ?></th>
                                    <th><?php echo $t['actions']; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($pages): ?>
                                    <?php foreach ($pages as $p): ?>
                                    <tr>
                                        <td><?php echo escape($p['title']); ?></td>
                                        <td><?php echo escape($p['slug']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $p['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $p['is_active'] ? 'Yes' : 'No'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="?action=edit&id=<?php echo $p['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-danger" href="?action=delete&id=<?php echo $p['id']; ?>" onclick="return confirm('<?php echo $t['confirm_delete']; ?>')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center"><?php echo $t['no_pages']; ?></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php elseif (in_array($action, ['add', 'edit'])): ?>
                <div class="card admin-card">
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $action; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['title']; ?> *</label>
                                <input type="text" class="form-control" name="title" value="<?php echo escape($page['title'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['slug']; ?></label>
                                <input type="text" class="form-control" name="slug" value="<?php echo escape($page['slug'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['content']; ?></label>
                                <textarea class="form-control" name="content" rows="6"><?php echo escape($page['content'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['meta_title']; ?></label>
                                <input type="text" class="form-control" name="meta_title" value="<?php echo escape($page['meta_title'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['meta_description']; ?></label>
                                <textarea class="form-control" name="meta_description" rows="3"><?php echo escape($page['meta_description'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold"><?php echo $t['meta_keywords']; ?></label>
                                <input type="text" class="form-control" name="meta_keywords" value="<?php echo escape($page['meta_keywords'] ?? ''); ?>">
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?php echo ($page['is_active'] ?? 1) ? 'checked' : ''; ?>>
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

