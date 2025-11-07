<?php
require_once __DIR__ . '/db.php';

$nim = trim($_GET['nim'] ?? '');
if ($nim === '') {
    echo json_encode([]);
    exit;
}

$term = "%$nim%";
$stmt = $pdo->prepare("SELECT nim, nama, jurusan, angkatan FROM mahasiswa WHERE nim LIKE ? OR nama LIKE ? LIMIT 50");
$stmt->execute([$term, $term]);
$rows = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($rows);
