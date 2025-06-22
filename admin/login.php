<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

// Redirect if already logged in
if (check_admin_auth()) {
    redirect('dashboard.php');
}

$error_message = '';
$success_message = '';

// Check for logout message
if (isset($_GET['logged_out'])) {
    $success_message = $admin_lang === 'de' ? 'Sie wurden erfolgreich abgemeldet.' : 'You have been successfully logged out.';
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Ungültiger Sicherheits-Token.';
    } elseif ($_POST['action'] === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if (empty($username) || empty($password)) {
            $error_message = 'Bitte geben Sie Benutzername und Passwort ein.';
        } else {
            $db = Database::getInstance();
            $admin = $db->selectOne(
                "SELECT * FROM admins WHERE username = ? OR email = ?",
                [$username, $username]
            );
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_language'] = $admin['language'];
                $_SESSION['login_time'] = time();
                
                redirect('dashboard.php');
            } else {
                $error_message = 'Ungültige Anmeldedaten.';
            }
        }
    }
}

// Language detection
$admin_lang = $_SESSION['admin_language'] ?? 'de';

// Translations
$translations = [
    'de' => [
        'title' => 'Admin Login - Crew of Experts',
        'heading' => 'Administrator Login',
        'subtitle' => 'Melden Sie sich an, um auf das Admin-Panel zuzugreifen',
        'username' => 'Benutzername oder E-Mail',
        'password' => 'Passwort',
        'login' => 'Anmelden',
        'remember' => 'Angemeldet bleiben',
        'forgot_password' => 'Passwort vergessen?',
        'back_to_site' => 'Zurück zur Website'
    ],
    'en' => [
        'title' => 'Admin Login - Crew of Experts',
        'heading' => 'Administrator Login',
        'subtitle' => 'Please sign in to access the admin panel',
        'username' => 'Username or Email',
        'password' => 'Password',
        'login' => 'Sign In',
        'remember' => 'Remember me',
        'forgot_password' => 'Forgot password?',
        'back_to_site' => 'Back to website'
    ]
];

$t = $translations[$admin_lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $admin_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    
    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0066cc;
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 102, 204, 0.3);
        }
        
        .login-footer {
            background: #f8f9fa;
            padding: 1.5rem 2rem;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .language-selector {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body>
    <!-- Language Selector -->
    <div class="language-selector">
        <div class="dropdown">
            <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-globe me-1"></i>
                <?php echo strtoupper($admin_lang); ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?lang=de">🇩🇪 Deutsch</a></li>
                <li><a class="dropdown-item" href="?lang=en">🇺🇸 English</a></li>
            </ul>
        </div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-shield-alt fa-2x"></i>
                </div>
                <h2 class="h4 fw-bold mb-2"><?php echo $t['heading']; ?></h2>
                <p class="mb-0 opacity-75"><?php echo $t['subtitle']; ?></p>
            </div>
            
            <!-- Body -->
            <div class="login-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo escape($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo escape($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold"><?php echo $t['username']; ?></label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo escape($_POST['username'] ?? ''); ?>" 
                                   required autofocus>
                            <div class="invalid-feedback">
                                Bitte geben Sie Ihren Benutzernamen ein.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold"><?php echo $t['password']; ?></label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordToggle"></i>
                            </button>
                            <div class="invalid-feedback">
                                Bitte geben Sie Ihr Passwort ein.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            <?php echo $t['remember']; ?>
                        </label>
                    </div>
                    
                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary btn-login w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <?php echo $t['login']; ?>
                    </button>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="login-footer">
                <div class="row align-items-center">
                    <div class="col-6">
                        <a href="#" class="text-decoration-none text-muted small">
                            <i class="fas fa-question-circle me-1"></i>
                            <?php echo $t['forgot_password']; ?>
                        </a>
                    </div>
                    <div class="col-6 text-end">
                        <a href="../" class="text-decoration-none text-primary small">
                            <i class="fas fa-arrow-left me-1"></i>
                            <?php echo $t['back_to_site']; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Form validation
        (function() {
            'use strict';
            
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        
        // Auto-focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input[autofocus]');
            if (firstInput) {
                firstInput.focus();
            }
        });
        
        // Language selector
        document.querySelectorAll('[href*="lang="]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(window.location.href);
                const lang = this.href.split('lang=')[1];
                url.searchParams.set('lang', lang);
                window.location.href = url.toString();
            });
        });
    </script>
</body>
</html>