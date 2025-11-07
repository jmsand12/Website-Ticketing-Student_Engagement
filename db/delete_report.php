<?php
require '../db/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "error: invalid_method";
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo "error: invalid_id";
    exit;
}

try {
    // Hapus semua data terkait laporan ini di tabel relasi terlebih dahulu
    $pdo->beginTransaction();

    // Hapus catatan di report_notes
    $stmtNotes = $pdo->prepare("DELETE FROM report_notes WHERE report_id = ?");
    $stmtNotes->execute([$id]);

    // Hapus activity log (meskipun sudah ada ON DELETE CASCADE di salah satu FK, tapi aman double)
    $stmtLog = $pdo->prepare("DELETE FROM activity_log WHERE laporan_id = ?");
    $stmtLog->execute([$id]);

    // Hapus laporan utamanya
    $stmt = $pdo->prepare("DELETE FROM laporan WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        $pdo->commit();
        echo "success";
    } else {
        $pdo->rollBack();
        echo "error: not_found";
    }
} catch (Exception $e) {
    $pdo->rollBack();
    echo "error: " . $e->getMessage();
}
