<?php
session_start();
$isLoggedIn = isset($_SESSION['user_email']);
$role = $_SESSION['user_role'] ?? '';

$userFullName = ''; // default

if ($isLoggedIn) {
  require_once '../db/db.php'; 

  $email = $_SESSION['user_email'];
  $stmt = $pdo->prepare("SELECT full_name FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $row = $stmt->fetch();

  if ($row) {
    $userFullName = $row['full_name'];
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Engagement Report</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script src="../js/script.js" defer></script>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar collapsed" id="sidebar">
  <div class="sidebar-top">
    <button id="toggleSidebar" class="menu-btn">
      <i class="fas fa-bars"></i>
    </button>
    <img src="../Logo/logo student engagement-abu.png" alt="Logo" class="sidebar-logo hidden" />
  </div>

  <ul class="menu">
    <li class="active" onclick="window.location.href='index.php'">
      <span class="icon"><i class="fas fa-home"></i></span>
      <span class="label">Home</span>
    </li>

    <?php if ($isLoggedIn): ?>
      <li onclick="window.location.href='report_request.php'">
        <span class="icon"><i class="fas fa-file-alt"></i></span>
        <span class="label">Pengajuan Laporan</span>
      </li>
      <li onclick="window.location.href='dashboard_report.php'">
        <span class="icon"><i class="fas fa-user-cog"></i></span>
        <span class="label">Dashboard Report</span>
      </li>
    <?php endif; ?>
  </ul>

  <div class="sidebar-spacer"></div>

  <div class="sidebar-bottom">
    <div class="collapse-btn" onclick="window.location.href='../main/index.php'">
      <i class="fas fa-arrow-left"></i>
      <span class="label">Kembali</span>
    </div>
  </div>
</aside>

<!-- Main -->
<div class="main">
  <header class="topbar">
    <div class="logo-topbar">
      <img src="../Logo/Logo Student Engagement.png" alt="Logo" />
    </div>

    <div class="login">
      <?php if ($isLoggedIn): ?>
        <div class="dropdown">
          <button class="dropbtn">Welcome, <?= htmlspecialchars($userFullName ?: $_SESSION['user_email']) ?> ▼</button>
          <div class="dropdown-content">
            <a href="../db/logout.php">Logout <i class="fas fa-sign-out-alt"></i></a>
          </div>
        </div>
      <?php else: ?>
        <a href="../main/login.php" class="login-btn">LOGIN</a>
      <?php endif; ?>
    </div>
  </header>

  <section class="content">
    <h1>Student Engagement Report</h1>
    <h3>UNIVERSITAS MULTIMEDIA NUSANTARA</h3>

    <div class="feature-boxes <?= $isLoggedIn ? '' : 'hidden' ?>">
      <div class="box" onclick="window.location.href='report_request.php'">
        📄 Report Request
      </div>
      <div class="box" onclick="window.location.href='dashboard_report.php'">
        🛠️ Dashboard Report
      </div>
    </div>
  </section>
</div>

</body>
</html>
