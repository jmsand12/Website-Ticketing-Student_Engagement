<?php
require '../db/db.php';

$reportId = intval($_GET['id'] ?? 0);

// Ambil notes
$stmt = $pdo->prepare("
    SELECT rn.note, rn.created_at, u.full_name AS created_by
    FROM report_notes rn
    LEFT JOIN users u ON rn.created_by = u.id
    WHERE rn.report_id = ?
    ORDER BY rn.created_at DESC
");
$stmt->execute([$reportId]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil division dari laporan
$stmt2 = $pdo->prepare("
    SELECT division
    FROM laporan
    WHERE id = ?
    LIMIT 1
");
$stmt2->execute([$reportId]);
$division = $stmt2->fetchColumn();

// Gabungkan hasil
$response = [
    "division" => $division ?: "-",
    "notes"    => $notes
];

header('Content-Type: application/json');
echo json_encode($response);
