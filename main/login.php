<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SSO Login</title>
  <link rel="stylesheet" href="../css/login.css">
  <script src="../js/login.js" defer></script>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <!-- Header -->
      <div class="login-header">
        <img src="../Logo/sso logo.png" alt="SSO Logo" class="login-logo">
        <div class="login-title">
        </div>
      </div>

<!-- Error Messages -->
<?php
$message = '';
if (isset($_GET['error'])) {
  $messages = [
    'empty_fields' => 'Username dan password wajib diisi.',
    'wrong_password' => 'Password salah.',
    'user_not_found' => 'User tidak ditemukan.'
  ];
  $error = $_GET['error'];
  if (array_key_exists($error, $messages)) {
    $message = $messages[$error];
  }
}
?>

  <?php if (!empty($message)): ?>
  <div class="alert">
    <i class="fas fa-exclamation-circle"></i>
    <?= htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

      <!-- Form -->
      <form id="loginForm" method="POST" action="../db/login_db.php">
        <label for="username">Username (E-mail address):</label>
        <input type="text" id="username" name="username" autocomplete="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>

        <button type="submit" class="login-btn">Login</button>
        <a href="#" class="forgot">Forget your password?</a>

        <label class="info-text" for="info-text">
          <p>For security reasons, please log out and exit your web browser when you are done accessing services that require authentication!</p>
        </label>
      </form>

    </div>
  </div>
</body>
</html>
