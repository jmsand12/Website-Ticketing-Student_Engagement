<?php
require_once __DIR__ . '/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, note, created_by AS officer_id, created_at 
    FROM report_notes 
    WHERE report_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$id]);
$notes = $stmt->fetchAll();

// Menambahkan nama officer per note (join users)
$ids = array_column($notes, 'officer_id');
$nameMap = [];
if (!empty($ids)) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt2 = $pdo->prepare("SELECT id, full_name, email FROM users WHERE id IN ($placeholders)");
    $stmt2->execute($ids);
    $rows = $stmt2->fetchAll();
    foreach ($rows as $r) $nameMap[$r['id']] = $r['full_name'] ?: $r['email'];
}

foreach ($notes as &$n) {
    $n['officer_name'] = $nameMap[$n['officer_id']] ?? null;
}

header('Content-Type: application/json');
echo json_encode($notes);
