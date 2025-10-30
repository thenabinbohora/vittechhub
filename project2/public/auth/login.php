<?php
//  User login page

// Load required components
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/helpers.php';

//  Initialize error message
$error = '';

//  Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    //  Fetch user details by email
    $stmt = $pdo->prepare("
        SELECT user_id, full_name, password_hash, role 
        FROM users 
        WHERE email = ? 
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    //  Verify password and authenticate user
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name']    = $user['full_name'];
        $_SESSION['role']    = $user['role'];

        //  Redirect based on user role
        if ($user['role'] === 'admin') {
            header("Location: " . base_url('/admin/dashboard.php'));
        } else {
            header("Location: " . base_url('/index.php#products'));
        }
        exit;
    } else {
        $error = "Login failed. Please check your email or password.";
    }
}

//  Include login header layout
include __DIR__ . '/../includes/login_header.php';
?>

<section class="container">
  <h2>Login</h2>

  <?php if ($error): ?>
    <p style="color:#e74c3c"><?= h($error) ?></p>
  <?php endif; ?>

  <!-- Login form -->
  <form method="post" class="contact-form" style="max-width:600px">
    <div class="form-group">
      <label>Email *</label>
      <input type="email" name="email" required>
    </div>

    <div class="form-group">
      <label>Password *</label>
      <input type="password" name="password" id="passwordInput" required>
    </div>

    <button class="submit-btn" type="submit">Login</button>
  </form>
</section>

<?php
//  Include global footer
include __DIR__ . '/../includes/footer.php';
?>