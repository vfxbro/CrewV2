<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!check_admin_auth()) {
    redirect('login.php');
}

$admin_id = $_SESSION['admin_id'];
$db = Database::getInstance();
$admin = $db->selectOne('SELECT * FROM admins WHERE id = ?', [$admin_id]);
$admin_lang = $_SESSION['admin_language'] ?? 'de';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid token';
    } else {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        if ($username && $email) {
            $params = [$username, $email];
            $query = 'UPDATE admins SET username = ?, email = ?';
            if ($password) {
                $query .= ', password = ?';
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }
            $query .= ' WHERE id = ?';
            $params[] = $admin_id;
            $db->execute($query, $params);
            $message = $admin_lang === 'de' ? 'Profil aktualisiert' : 'Profile updated';
            $_SESSION['admin_username'] = $username;
            $admin = $db->selectOne('SELECT * FROM admins WHERE id = ?', [$admin_id]);
        } else {
            $error = 'Username and email required';
        }
    }
}

include 'includes/admin_header.php';
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 fw-bold"><i class="fas fa-user text-primary me-2"></i>Profile</h1>
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
            <div class="card">
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" value="<?php echo escape($admin['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo escape($admin['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password">
                            <div class="form-text">Leave blank to keep current password.</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
