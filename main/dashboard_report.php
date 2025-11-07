<?php
session_start();
require '../db/db.php';

$isLoggedIn = isset($_SESSION['user_email']);

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

// Ambil user role dari session
$role = strtolower(trim($_SESSION['user_role'] ?? ''));

// Mapping role ke division
$roleDivision = [
  'studentservice'     => 'studentservice',
  'studentsupport'     => 'studentsupport',
  'studentdevelopment' => 'studentdevelopment'
];

// Fungsi untuk mapping nama division ke tampilan
function mapDivisionName($division) {
  switch (strtolower($division)) {
    case 'studentservice': return 'Service';
    case 'studentsupport': return 'Support';
    case 'studentdevelopment': return 'Development';
    default: return '-';
  }
}

// Query dasar laporan
$sql = "SELECT 
            l.id,
            l.nim,
            COALESCE(m.nama, '-') AS nama,
            COALESCE(m.jurusan, '-') AS jurusan,
            COALESCE(m.angkatan, '-') AS angkatan,
            l.log_type,
            l.report_type,
            l.sub_type,
            l.deskripsi,
            l.division,
            l.lampiran,
            l.status,
            l.created_at
        FROM laporan l
        LEFT JOIN mahasiswa m ON l.nim = m.nim";

$params = [];

// Filter sesuai role divisi
if ($role && !in_array($role, ['superadmin', 'admin'])) {
    if (isset($roleDivision[$role])) {
        $div = strtolower($roleDivision[$role]);
        $sql .= "
            WHERE (
                LOWER(l.division) = :division
                OR l.division IS NULL
                OR TRIM(l.division) = ''
            )
        ";
        $params[':division'] = $div;
    } else {
        $sql .= " WHERE (l.division IS NULL OR TRIM(l.division) = '')";
    }
} else {
    $sql .= " WHERE 1=1"; // Admin/Superadmin lihat semua
}


$sql .= " ORDER BY l.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung summary box
$pending = $assign = $resolved = 0;
$total = count($laporan);

foreach ($laporan as $row) {
  if ($row['status'] === 'pending') $pending++;
  if ($row['status'] === 'assign') $assign++;
  if ($row['status'] === 'resolved') $resolved++;
}

// === DATA UNTUK TREND CHART ===
// Default interval: bulanan
$trendQuery = "
  SELECT DATE_FORMAT(created_at, '%M %Y') AS periode, COUNT(*) AS total
  FROM laporan
  GROUP BY DATE_FORMAT(created_at, '%Y-%m')
  ORDER BY MIN(created_at)
";
$trendStmt = $pdo->prepare($trendQuery);
$trendStmt->execute();
$trendData = $trendStmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$totals = [];
foreach ($trendData as $d) {
  $labels[] = $d['periode'];
  $totals[] = (int)$d['total'];
}

// === DATA UNTUK BAR CHART: Distribusi Jenis Laporan ===
$reportTypeQuery = "
  SELECT report_type, COUNT(*) AS total
  FROM laporan
  GROUP BY report_type
  ORDER BY total DESC
";
$reportTypeStmt = $pdo->prepare($reportTypeQuery);
$reportTypeStmt->execute();
$reportTypeData = $reportTypeStmt->fetchAll(PDO::FETCH_ASSOC);

$reportLabels = [];
$reportTotals = [];
foreach ($reportTypeData as $r) {
  $reportLabels[] = $r['report_type'] ?: 'Tidak Diketahui';
  $reportTotals[] = (int)$r['total'];
}

// KPI Query
$totalMahasiswa = $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();

// KPI Query berdasarkan role
if ($role && !in_array($role, ['superadmin', 'admin'])) {
    // Jika role spesifik studentservice/support/development
    if (isset($roleDivision[$role])) {
        $div = strtolower($roleDivision[$role]);

        // KPI In Progress
        $stmtInProgress = $pdo->prepare("
            SELECT COUNT(*) 
            FROM laporan 
            WHERE status IN ('pending', 'assign') 
              AND (LOWER(division) = :division OR division IS NULL OR TRIM(division) = '')
        ");
        $stmtInProgress->execute([':division' => $div]);
        $totalInProgress = $stmtInProgress->fetchColumn();

        // KPI Resolved
        $stmtResolved = $pdo->prepare("
            SELECT COUNT(*) 
            FROM laporan 
            WHERE status IN ('resolved', 'done') 
              AND (LOWER(division) = :division OR division IS NULL OR TRIM(division) = '')
        ");
        $stmtResolved->execute([':division' => $div]);
        $totalResolved = $stmtResolved->fetchColumn();

    } else {
        // Role tidak dikenali
        $totalInProgress = $totalResolved = 0;
    }
} elseif ($role === 'superadmin' || $role === 'admin') {
    // Superadmin/Admin melihat semua termasuk division kosong
    $totalInProgress = $pdo->query("
        SELECT COUNT(*) 
        FROM laporan 
        WHERE status IN ('pending', 'assign')
    ")->fetchColumn();

    $totalResolved = $pdo->query("
        SELECT COUNT(*) 
        FROM laporan 
        WHERE status IN ('resolved', 'done')
    ")->fetchColumn();
} else {
    // Jika belum login
    $totalInProgress = $totalResolved = 0;
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Report</title>
  <link rel="stylesheet" href="../css/dashboard_report.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/file-saver/dist/FileSaver.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  
  <!-- Sidebar -->
  <aside class="sidebar collapsed" id="sidebar">
    <div class="sidebar-top">
      <button id="toggleSidebar" class="menu-btn"><i class="fas fa-bars"></i></button>
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

    <div class="dashboard-main">
      <h2>Dashboard Student Report</h2>

<!-- KPI + Pie Chart -->
<div class="summary-wrapper">
  <div class="summary-boxes">
    <div class="box progress">
      <span>Laporan dalam Proses</span>
      <span class="count"><?= $totalInProgress ?></span>
    </div>

    <div class="box resolved">
      <span>Laporan Selesai</span>
      <span class="count"><?= $totalResolved ?></span>
    </div>

    <div class="box total">
      <span>Total Laporan Mahasiswa</span>
      <span class="count"><?= $total ?></span>
    </div>
  </div>

  <div class="chart-container">
    <canvas id="ticketChart"></canvas>
  </div>
</div>

    
<!-- Trend Chart Laporan -->
<div class="chart-section">
  <div class="chart-header">
    <div>
      <label for="chartInterval">Interval:</label>
      <select id="chartInterval">
        <option value="daily">Harian</option>
        <option value="weekly">Mingguan</option>
        <option value="monthly" selected>Bulanan</option>
      </select>
    </div>
  </div>

  <div class="charttrend-wrapper" style="display: flex; gap: 20px;">
    <div class="charttrend-container" style="flex: 1;">
      <h4 style="text-align:center;">Tren Laporan per Periode</h4>
      <canvas id="trendChart"></canvas>
    </div>
    <div class="chartbar-container" style="flex: 1;">
      <h4 style="text-align:center;">Distribusi Jenis Laporan</h4>
      <canvas id="reportTypeChart"></canvas>
    </div>
  </div>
</div>

      <!-- Search + Show Entries + Export -->
      <div class="table-controls">
        <input type="text" id="searchInput" class="search-bar" placeholder="Search Box">
        <div class="table-actions">
          <label for="entriesSelect">Show</label>
          <select id="entriesSelect">
            <option value="10">10</option>
            <option value="30">30</option>
            <option value="50">50</option>
          </select>
          <span>entries</span>

          <button id="exportExcelBtn" class="export-btn">
            <i class="fas fa-file-excel"></i> Export
          </button>
        </div>
      </div>

      <!-- Data Table -->
      <table id="laporanTable" class="ticket-table">
        <thead>
          <tr>
            <th>Action</th>
            <th id="timestampHeader" style="cursor:pointer;">
              Timestamp 
              <i id="sortIcon" class="fas fa-sort"></i>
            </th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>Report type</th>
            <th>Forward to</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($laporan)): ?>
          <?php foreach ($laporan as $row): ?>
            <tr
              data-id="<?= $row['id'] ?>"
              data-timestamp="<?= htmlspecialchars($row['created_at']) ?>"
              data-nim="<?= htmlspecialchars($row['nim']) ?>"
              data-nama="<?= htmlspecialchars($row['nama']) ?>"
              data-report="<?= htmlspecialchars($row['report_type']) ?>"
              data-subtype="<?= htmlspecialchars($row['sub_type'] ?? '') ?>"
              data-division="<?= htmlspecialchars($row['division']) ?>"
              data-status="<?= htmlspecialchars($row['status']) ?>"
              data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>"
              data-notes="<?= htmlspecialchars($row['notes'] ?? '-') ?>">
              <td class="action-cell">
                <button class="action-btn edit-btn">Edit</button>
                <button class="action-btn delete-btn">Delete</button>
              </td>
              <td><?= htmlspecialchars($row['created_at']) ?></td>
              <td><?= htmlspecialchars($row['nim']) ?></td>
              <td><?= htmlspecialchars($row['nama']) ?></td>
              <td><?= htmlspecialchars($row['report_type']) ?></td>
              <td><?= htmlspecialchars(mapDivisionName($row['division'])) ?></td>
              <td class="status"><?= htmlspecialchars($row['status']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align:center;">Tidak ada data laporan</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div id="paginationControls" class="pagination-wrapper"></div>
  </div>


<!-- Detail Modal -->
<div id="detailModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Detail Laporan</h3>
    </div>

    <div class="modal-body">
      <p><strong>Timestamp:</strong> <span id="detail-timestamp">-</span></p>
      <p><strong>NIM:</strong> <span id="detail-nim">-</span></p>
      <p><strong>Nama:</strong> <span id="detail-nama">-</span></p>
      <p><strong>Jenis Laporan:</strong> <span id="detail-report">-</span></p>

      <!-- Tambahan untuk Sub Type -->
      <p id="detail-subtype-wrapper" style="display:none;">
        <strong>Sub Type:</strong> <span id="detail-subtype">-</span>
      </p>

      <p><strong>Forward to:</strong> <span id="detail-division">-</span></p>
      <p><strong>Status:</strong> <span id="detail-status">-</span></p>
      <p><strong>Deskripsi:</strong> <span id="detail-deskripsi">-</span></p>

      <div class="notes-section">
        <p><strong>Riwayat Catatan:</strong></p>
        <ul id="detail-notes-list" class="notes-list">
          <li>-</li>
        </ul>
      </div>
    </div>

    <div class="modal-footer">
      <button id="closeDetailBtn" onclick="closeModal('detailModal')">Tutup</button>
    </div>
  </div>
</div>


<!-- Edit Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Laporan</h3>
    </div>

    <form id="editForm" class="modal-body">
      <input type="hidden" id="editId" name="id">

      <label for="editStatus">Status</label>
      <select id="editStatus" name="status" required>
        <option value="pending">Pending</option>
        <option value="assign">Assign</option>
        <option value="resolved">Resolved</option>
        <option value="done">Done</option>
      </select>

      <label for="editDivision">Forward to</label>
      <select id="editDivision" name="division">
        <option value="">-- Pilih Divisi --</option>
        <option value="service">Service</option>
        <option value="support">Support</option>
        <option value="development">Development</option>
      </select>

      <label for="editNotes">Notes</label>
      <textarea id="editNotes" name="notes" placeholder="Tambahkan catatan jika perlu..."></textarea>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Batal</button>
        <button type="submit" class="btn-save">Simpan</button>
      </div>
    </form>
  </div>
</div>


<!-- Delete Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">Hapus Laporan</div>
    <p>Apakah Anda yakin ingin menghapus laporan ini?</p>
    <div class="modal-footer">
      <button id="confirmDeleteBtn" class="btn-delete">Hapus</button>
      <button id="cancelDeleteBtn" class="btn-cancel">Batal</button>
    </div>
  </div>
</div>


  <script>
    const ticketData = {
      pending: <?= $pending ?>,
      assign: <?= $assign ?>,
      resolved: <?= $resolved ?>
    };

    const trendLabels = <?= json_encode($labels) ?>;
    const trendTotals = <?= json_encode($totals) ?>;

    const reportLabels = <?= json_encode($reportLabels) ?>;
    const reportTotals = <?= json_encode($reportTotals) ?>;

  </script>

  <script src="../js/dashboard_report.js"></script>
</body>
</html>
