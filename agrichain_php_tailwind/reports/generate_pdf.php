<?php
require_once __DIR__ . '/../config/auth.php'; roles_allowed(['admin','inspector']);
$type = $_GET['type'] ?? 'inspections';
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html><html><head><meta charset="utf-8"><title>Report</title>
<style>body{font-family:Arial;margin:24px;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #ddd;padding:8px;font-size:12px;} th{background:#f3f4f6;}</style>
</head><body>
<h1>AgriChain Report: <?=htmlspecialchars($type)?></h1>
<p>Generated at <?=date('Y-m-d H:i:s')?>. Print this page as PDF (Ctrl/Cmd+P) or integrate dompdf for automatic PDF generation.</p>
<table>
  <thead><tr><th>#</th><th>Data</th></tr></thead><tbody>
<?php
$allowed=['inspections','crops','batches','orders'];
if(!in_array($type,$allowed)) $type='inspections';
try {
  $stmt=$pdo->query("SELECT * FROM $type ORDER BY id DESC LIMIT 500");
  while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr><td>'.htmlspecialchars($row['id']).'</td><td><pre>'.htmlspecialchars(json_encode($row, JSON_PRETTY_PRINT)).'</pre></td></tr>';
  }
} catch(Exception $e) {
  echo '<tr><td colspan="2">No data or table missing.</td></tr>';
}
?>
</tbody></table>
</body></html>
