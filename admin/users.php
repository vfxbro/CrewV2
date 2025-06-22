<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!check_admin_auth()) {
    redirect('login.php');
}

$db = Database::getInstance();
$admin_lang = $_SESSION['admin_language'] ?? 'de';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token';
    } else {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if ($username && $email) {
            try {
                if (isset($_POST['id']) && $_POST['id']) {
                    $params = [$username, $email, $_POST['id']];
                    $query = "UPDATE admins SET username = ?, email = ?";
                    if ($password) {
                        $query .= ", password = ?";
                        $params = [$username, $email, password_hash($password, PASSWORD_DEFAULT), $_POST['id']];
                    }
                    $query .= " WHERE id = ?";
                    array_push($params, $_POST['id']);
                    $db->execute($query, $params);
                    $message = $admin_lang === 'de' ? 'Benutzer aktualisiert' : 'User updated';
                } else {
                    $db->execute(
                        "INSERT INTO admins (username, email, password) VALUES (?, ?, ?)",
                        [$username, $email, password_hash($password ?: 'changeme', PASSWORD_DEFAULT)]
                    );
                    $message = $admin_lang === 'de' ? 'Benutzer hinzugefügt' : 'User added';
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = 'Username and email required';
        }
    }
}

$admins = $db->select("SELECT * FROM admins ORDER BY id");

include 'includes/admin_header.php';
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 fw-bold"><i class="fas fa-users text-primary me-2"></i>Users</h1>
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
            <div class="card mb-4">
                <div class="card-header"><strong>User List</strong></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                        <tr><th>ID</th><th>Username</th><th>Email</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td><?php echo $admin['id']; ?></td>
                                <td><?php echo escape($admin['username']); ?></td>
                                <td><?php echo escape($admin['email']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editUser(<?php echo $admin['id']; ?>,'<?php echo escape($admin['username']); ?>','<?php echo escape($admin['email']); ?>')"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><strong id="formTitle">Add User</strong></div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="id" id="userId">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                            <div class="form-text">Leave blank to keep current password.</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
function editUser(id, username, email){
    document.getElementById('formTitle').innerText='Edit User';
    document.getElementById('userId').value=id;
    document.getElementById('username').value=username;
    document.getElementById('email').value=email;
}
</script>
<?php include 'includes/admin_footer.php'; ?>
