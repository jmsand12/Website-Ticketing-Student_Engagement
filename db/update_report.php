<?php
require '../db/db.php';
session_start();
header('Content-Type: application/json');

// Hindari output error HTML
error_reporting(0);
ini_set('display_errors', 0);

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu.']);
  exit;
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';

$reportId = $_POST['report_id'] ?? $_POST['id'] ?? null;
$status = $_POST['status'] ?? '';
$division = $_POST['division'] ?? '';
$notes = trim($_POST['notes'] ?? '');

if (!$reportId) {
  echo json_encode(['success' => false, 'message' => 'ID laporan tidak ditemukan.']);
  exit;
}

try {
  // Ambil data lama
  $stmt = $pdo->prepare("SELECT division, status FROM laporan WHERE id = ?");
  $stmt->execute([$reportId]);
  $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$oldData) throw new Exception("Laporan tidak ditemukan.");

  $oldDivision = $oldData['division'];
  $oldStatus = $oldData['status'];

  // Mapping division (jika frontend kirim short code)
  $divisionMap = [
    'service'     => 'studentservice',
    'support'     => 'studentsupport',
    'development' => 'studentdevelopment',
    ''            => null
  ];
  $divisionFull = $divisionMap[$division] ?? $division;

  // Pastikan division tidak kosong
  if (empty($divisionFull)) {
    $divisionFull = $oldDivision;
  }

  // Update laporan
  $stmt = $pdo->prepare("
    UPDATE laporan 
    SET status = ?, division = ?, updated_at = NOW() 
    WHERE id = ?
  ");
  $stmt->execute([$status, $divisionFull, $reportId]);

  // Jika ada notes baru → simpan di tabel report_notes
  if (!empty($notes)) {
    $stmt = $pdo->prepare("
      INSERT INTO report_notes (report_id, note, created_by, created_at)
      VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$reportId, $notes, $userId]);
  }

  // Catat aktivitas ke activity_log
  $stmt = $pdo->prepare("
    INSERT INTO activity_log (laporan_id, action, old_status, new_status, changed_by, created_at)
    VALUES (?, 'Forwarded/Updated', ?, ?, ?, NOW())
  ");
  $stmt->execute([$reportId, $oldStatus, $status, $userId]);

  echo json_encode([
    'success' => true,
    'message' => 'Laporan berhasil diperbarui dan diteruskan.',
    'newDivision' => $divisionFull,
  ]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
