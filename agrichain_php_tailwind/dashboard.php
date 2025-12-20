<?php
require_once __DIR__ . '/config/auth.php';
require_login();
$role = current_user()['role'];
$map = [
  'admin' => '/agrichain_php_tailwind/dashboards/admin.php',
  'farmer' => '/agrichain_php_tailwind/dashboards/farmer.php',
  'inspector' => '/agrichain_php_tailwind/modules/inspector/inspections.php',
  'transporter' => '/agrichain_php_tailwind/dashboards/transporter.php',
  'packaging' => '/agrichain_php_tailwind/dashboards/packaging.php',
  'customer' => '/agrichain_php_tailwind/dashboards/customer.php',
];
header('Location: ' . ($map[strtolower($role)] ?? '/agrichain_php_tailwind/index.php'));
exit;
