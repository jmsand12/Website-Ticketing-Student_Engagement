<?php
require '../db/db.php';

$interval = $_GET['interval'] ?? 'monthly';
$validIntervals = ['daily', 'weekly', 'monthly'];
if (!in_array($interval, $validIntervals)) $interval = 'monthly';

// === Trend chart ===
switch ($interval) {
    case 'daily':
        $trendQuery = "SELECT DATE(created_at) AS periode, COUNT(*) AS total
                       FROM laporan
                       GROUP BY DATE(created_at)
                       ORDER BY MIN(created_at)";
        break;
    case 'weekly':
        $trendQuery = "SELECT CONCAT('Minggu ', WEEK(created_at), ' ', YEAR(created_at)) AS periode, COUNT(*) AS total
                       FROM laporan
                       GROUP BY YEAR(created_at), WEEK(created_at)
                       ORDER BY MIN(created_at)";
        break;
    case 'monthly':
    default:
        $trendQuery = "SELECT DATE_FORMAT(created_at, '%M %Y') AS periode, COUNT(*) AS total
                       FROM laporan
                       GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                       ORDER BY MIN(created_at)";
        break;
}

$stmtTrend = $pdo->prepare($trendQuery);
$stmtTrend->execute();
$trendData = $stmtTrend->fetchAll(PDO::FETCH_ASSOC);

// === Distribusi Jenis Laporan (berdasarkan periode yg sama) ===
switch ($interval) {
    case 'daily':
        $groupField = "DATE(created_at)";
        break;
    case 'weekly':
        $groupField = "CONCAT(YEAR(created_at), '-', WEEK(created_at))";
        break;
    default:
        $groupField = "DATE_FORMAT(created_at, '%Y-%m')";
        break;
}

$reportTypeQuery = "
  SELECT report_type, COUNT(*) AS total
  FROM laporan
  WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
  GROUP BY report_type
  ORDER BY total DESC
";
$stmtType = $pdo->prepare($reportTypeQuery);
$stmtType->execute();
$typeData = $stmtType->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  'trend' => $trendData,
  'reportType' => $typeData
]);
