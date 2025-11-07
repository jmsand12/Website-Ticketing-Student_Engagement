<?php
session_start();
$isLoggedIn = isset($_SESSION['user_email']);
$userEmail = $_SESSION['user_email'] ?? '';
$role = strtolower(trim($_SESSION['user_role'] ?? ''));

$userFullName = ''; // default

if ($isLoggedIn) {
  require_once '../db/db.php'; // koneksi pakai PDO

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
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Request</title>
  <link rel="stylesheet" href="../css/report_request.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

    <!-- Main Content -->
    <div class="main-content">
      <h2>Form Laporan - <?= ucfirst($role) ?></h2>
      <form action="../db/submit_report.php" method="POST" enctype="multipart/form-data" class="report-form">
        <!-- Left Column -->
        <div class="form-left">
          <label for="nim">NIM <span style="color:red">*</span></label>
          <div style="position: relative; width: 100%;">
            <input type="text" id="nim" name="nim" autocomplete="off" required>
            <div id="nim-results" class="nim-results"></div>
          </div>

          <label for="nama">Nama <span style="color:red">*</span></label>
          <input type="text" id="nama" name="nama" required>

          <label for="program">Jurusan <span style="color:red">*</span></label>
          <input type="text" id="program" name="program" required>

          <label for="class_of">Angkatan <span style="color:red">*</span></label>
          <input type="text" id="class_of" name="class_of" required>
        </div>

        <!-- Right Column -->
        <div class="form-right">

          <!-- Log Type -->
          <?php if ($role === 'studentservice'): ?>
            <fieldset class="log-type">
              <legend>Log Type <span style="color:red">*</span></legend>
              <label><input type="radio" name="log_type" value="harian" required> Log Harian</label>
              <label><input type="radio" name="log_type" value="khusus" required> Log Khusus</label>
            </fieldset>
          <?php else: ?>
            <!-- Default log_type untuk role lain -->
            <input type="hidden" name="log_type" value="khusus">
          <?php endif; ?>

          <!-- Report Type -->
          <label for="report_type">Report Type <span style="color:red">*</span></label>
          <select id="report_type" name="report_type" required>
            <option value="">-- Select Report Type --</option>
          </select>

          <!-- Sub Type -->
          <label for="sub_type" id="subtype_label" style="display:none;">Sub Type <span style="color:red">*</span></label>
          <select id="sub_type" name="sub_type" style="display:none;" required>
            <option value="">-- Pilih Sub Type --</option>
            <!-- Default subtype bawaan -->
            <option value="cuti">Cuti</option>
            <option value="undur_diri">Undur Diri</option>
            <option value="pindah_prodi">Pindah Prodi</option>
            <option value="aktif_nim">Aktif NIM Kembali</option>
            <option value="masa_studi_habis">Masa Studi Habis</option>
          </select>

          <label for="deskripsi">Deskripsi Masalah <span style="color:red">*</span></label>
          <textarea id="deskripsi" name="deskripsi" placeholder="Ketik deskripsi disini" required></textarea>

          <label for="division">Diteruskan ke divisi (Opsional)</label>
          <select id="division" name="division">
            <option value="" class="default">None</option>
            <option value="service">Student Service</option>
            <option value="support">Student Support</option>
            <option value="development">Student Development</option>
          </select>

          <label for="lampiran">Lampiran (Opsional)</label>
          <input type="file" id="lampiran" name="lampiran" accept=".jpg,.png,.pdf">
          <p class="file-info">Format: JPG, PNG, PDF (max. 10MB)</p>

          <button type="submit" class="submit-btn">Submit</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Status -->
  <div id="statusModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2 id="modalTitle" style="text-align:center; margin-bottom:10px;"></h2>
      <p id="modalMessage" style="text-align:center;"></p>
      <div style="text-align:center; margin-top:20px;">
        <button id="closeModalBtn" class="submit-btn">OK</button>
      </div>
    </div>
  </div>

  <!-- Modal NIM -->
  <div id="nimModal" class="modal">
    <div class="modal-content">
      <span class="close-nim">&times;</span>
      <h3>Hasil Pencarian Mahasiswa</h3>
      <table border="1" width="100%">
        <thead>
          <tr>
            <th>NIM</th>
            <th>Nama</th>
            <th>Jurusan</th>
            <th>Angkatan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="nimResult">
          <!-- Hasil pencarian  -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Konfirmasi Submit -->
  <div id="confirmModal" class="modal">
    <div class="modal-content">
      <h3>Konfirmasi Pengiriman</h3>
      <p>Apakah Anda yakin ingin mengirim laporan ini?</p>
      <div style="text-align:center; margin-top:20px;">
        <button id="confirmYes" class="submit-btn">Ya, Kirim</button>
        <button id="confirmNo" class="submit-btn" style="background-color:#aaa;">Batal</button>
      </div>
    </div>
  </div>


  <script>
    // Role ke JS agar bisa tentukan tipe report
    const role = "<?= htmlspecialchars($role) ?>";
  </script>

  <script src="../js/report_request.js"></script>
</body>
</html>
