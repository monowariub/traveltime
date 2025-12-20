<?php
require_once __DIR__ . '/../config/auth.php'; roles_allowed(['admin']);
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="agrichain_export.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['Type','ID','Meta','Created']);
foreach (['users','crops','fertilizers','harvests','inspections','batches','orders','feedback'] as $table) {
  try {
    $stmt=$pdo->query("SELECT * FROM $table");
    while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
      fputcsv($out, [$table, $row['id'] ?? '', json_encode($row), $row['created_at'] ?? '']);
    }
  } catch(Exception $e) { /* skip missing tables */ }
}
fclose($out);
exit;
