<?php
require '../db/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// ==========================
// Ambil input dari form
// ==========================
$nim = trim($_POST['nim'] ?? '');
$log_type = trim($_POST['log_type'] ?? 'khusus');
$report_type = trim($_POST['report_type'] ?? '');
$sub_type = trim($_POST['sub_type'] ?? null);
$deskripsi = trim($_POST['deskripsi'] ?? '');

// Division terisi
$division = null;
if (!empty($_POST['division'])) {
    $division = trim($_POST['division']);
} elseif (!empty($_SESSION['division'])) {
    $division = trim($_SESSION['division']); // fallback dari session
}

if ($nim === '' || $report_type === '' || $deskripsi === '') {
    header("Location: ../main/report_request.php?status=error&msg=" . urlencode("NIM, jenis laporan, dan deskripsi wajib diisi."));
    exit;
}

// ==========================
// Upload Lampiran (jika ada)
// ==========================
$lampiran = null;
if (!empty($_FILES['lampiran']['name'])) {
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    $fn = $_FILES['lampiran']['name'];
    $ext = strtolower(pathinfo($fn, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed) || $_FILES['lampiran']['size'] > $maxSize) {
        header("Location: ../main/report_request.php?status=error&msg=" . urlencode("Format lampiran tidak diizinkan atau ukuran terlalu besar."));
        exit;
    }

    $targetDir = __DIR__ . '/../uploads/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (!move_uploaded_file($_FILES['lampiran']['tmp_name'], $targetDir . $newName)) {
        header("Location: ../main/report_request.php?status=error&msg=" . urlencode("Gagal mengunggah lampiran."));
        exit;
    }

    $lampiran = $newName;
}

// ==========================
// Simpan ke Database
// ==========================
try {
    $pdo->beginTransaction();

    // Trigger akan otomatis generate ticket_code
    $stmt = $pdo->prepare("
        INSERT INTO laporan (nim, log_type, report_type, sub_type, deskripsi, division, lampiran, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->execute([$nim, $log_type, $report_type, $sub_type, $deskripsi, $division, $lampiran]);

    $laporan_id = $pdo->lastInsertId();

    // Tambahkan catatan aktivitas awal
    $log = $pdo->prepare("
        INSERT INTO activity_log (laporan_id, action, old_status, new_status, created_at)
        VALUES (?, 'Report Submitted', NULL, 'pending', NOW())
    ");
    $log->execute([$laporan_id]);

    $pdo->commit();

    header("Location: ../main/report_request.php?status=success");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../main/report_request.php?status=error&msg=" . urlencode("Gagal menyimpan data: " . $e->getMessage()));
    exit;
}
?>
