<?php
//  User registration page

// Load required components
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/helpers.php';

//  Initialize state variables
$errors  = [];
$success = false;

//  Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm'] ?? '';
    $contact   = trim($_POST['contact'] ?? '');

    //  Validate form fields
    if ($full_name === '') $errors[] = "Full name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    //  Check if email already exists
    if (!$errors) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = "Email is already registered.";
    }

    //  Insert new user into database
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            INSERT INTO users (full_name, email, password_hash, role, contact_number) 
            VALUES (?, ?, ?, 'customer', ?)
        ");
        $stmt->execute([$full_name, $email, $hash, $contact]);
        $success = true;
    }
}

//  Include registration header layout
include __DIR__ . '/../includes/register_header.php';
?>

<section class="container">
  <h2>Register</h2>

  <?php if ($success): ?>
    <p>
      Registration successful. 
      <a class = "btn-blue" href="<?= base_url('/auth/login.php') ?>">Login here</a>.
    </p>
  <?php else: ?>

    <?php if ($errors): ?>
      <ul style="color:#e74c3c">
        <?php foreach ($errors as $e): ?>
          <li><?= h($e) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <!-- Registration form -->
    <form method="post" class="contact-form" style="max-width:600px">
      <div class="form-group">
        <label>Full Name *</label>
        <input name="full_name" value="<?= h($_POST['full_name'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Contact Number</label>
        <input name="contact" value="<?= h($_POST['contact'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Password *</label>
        <input type="password" name="password" id="password" required>
      </div>

      <div class="form-group">
        <label>Confirm Password *</label>
        <input type="password" name="confirm" id="confirm" required>
      </div>

      <button class="submit-btn" type="submit">Create Account</button>
    </form>

  <?php endif; ?>
</section>

<?php
//  Include global footer
include __DIR__ . '/../includes/footer.php';
?>
