<?php
session_start();

// Proteksi halaman: wajib login
if (!isset($_SESSION['user_email'])) {
    header("Location: ../main/login.php");
    exit();
}

// Ambil info user dari session
$email = $_SESSION['user_email'];
$role = $_SESSION['user_role'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin - UMN Ticketing</title>
  <link rel="stylesheet" href="../css/admin_page.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="../js/admin_page.js" defer></script>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar collapsed" id="sidebar">
    <div class="sidebar-top">
      <button id="toggleSidebar" class="menu-btn" aria-label="Toggle Sidebar">
        <i class="fas fa-bars"></i>
      </button>
      <img src="../Logo/logo student engagement-abu.png" alt="Logo" class="sidebar-logo hidden" id="sidebarLogo" />
    </div>

    <ul class="menu">
      <li class="active" id="dashboardBtn">
        <span class="icon"><i class="fas fa-home"></i></span>
        <span class="label">Home</span>
      </li>
      <li id="reportRequestBtn">
        <span class="icon"><i class="fas fa-file-alt"></i></span>
        <span class="label">Pengajuan Laporan</span>
      </li>
      <li id="reportStatusBtn">
        <span class="icon"><i class="fas fa-check-square"></i></span>
        <span class="label">Status Laporan</span>
      </li>
    </ul>

    <div class="sidebar-bottom">
      <button id="backBtn" class="collapse-btn" aria-label="Kembali">
        <i class="fas fa-arrow-left"></i>
        <span class="label">Kembali</span>
      </button>
    </div>
  </aside>

  <!-- Main -->
  <div class="main">
    <!-- Topbar -->
    <header class="topbar">
      <div class="logo-topbar">
        <img src="../Logo/Logo Student Engagement.png" alt="Logo" />
      </div>
      <div class="login">
        <span class="welcome-msg"><?= htmlspecialchars($email) ?></span>
        <a href="../db/logout.php" class="login-btn">Logout</a>
      </div>
    </header>

    <!-- Content -->
    <main class="admin-main">
      <h2>Pilih Salah Satu Divisi untuk Menampilkan Dashboard</h2>
      <div class="division-container">
        <!-- Student Service -->
        <div class="division-card <?= ($role === 'superadmin' || $role === 'studentservice') ? '' : 'disabled'; ?>" data-division="Student Service" data-href="student_service_report.php">
          <img src="../Logo/logo student_service.png" alt="Student Service" class="logo-service" />
          <h3>Student Service</h3>
        </div>

        <!-- Student Support -->
        <div class="division-card <?= ($role === 'superadmin' || $role === 'studentsupport') ? '' : 'disabled'; ?>" data-division="Student Support" data-href="student_support_report.php">
          <img src="../Logo/logo student_support.png" alt="Student Support" class="logo-support" />
          <h3>Student Support</h3>
        </div>

        <!-- Student Development -->
        <div class="division-card <?= ($role === 'superadmin' || $role === 'studentdevelopment') ? '' : 'disabled'; ?>" data-division="Student Development" data-href="student_development_report.php">
          <img src="../Logo/logo student_development.png" alt="Student Development" class="logo-development" />
          <h3>Student Development</h3>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
