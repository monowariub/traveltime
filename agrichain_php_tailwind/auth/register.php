<?php
require_once __DIR__ . '/../config/config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) { $errors[]='Invalid CSRF token.'; }
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $role = $_POST['role'] ?? 'customer';
  if (!$name || !$email || !$pass) { $errors[] = 'All fields are required.'; }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Invalid email.'; }
  if (!$errors) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :e");
    $stmt->execute([':e'=>$email]);
    if ($stmt->fetch()) {
      $errors[] = 'Email is already registered.';
    } else {
      $hash = password_hash($pass, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role,created_at) VALUES (:n,:e,:p,:r,NOW())");
      $stmt->execute([':n'=>$name, ':e'=>$email, ':p'=>$hash, ':r'=>$role]);
      header('Location: /agrichain_php_tailwind/auth/login.php?registered=1'); exit;
    }
  }
}
?>
<?php include __DIR__.'/../includes/header.php'; ?>
<?php include __DIR__.'/../includes/navbar.php'; ?>
<div class="max-w-md mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Create account</h1>
  <?php foreach($errors as $e): ?>
    <div class="mb-2 p-3 bg-red-100 text-red-700 rounded"><?=htmlspecialchars($e)?></div>
  <?php endforeach; ?>
  <form method="post" class="space-y-4">
    <input type="hidden" name="csrf" value="<?=csrf_token()?>">
    <input class="w-full border rounded p-2" name="name" placeholder="Full name">
    <input class="w-full border rounded p-2" name="email" type="email" placeholder="Email">
    <input class="w-full border rounded p-2" name="password" type="password" placeholder="Password">
    <select class="w-full border rounded p-2" name="role">
      <option value="customer">Customer</option>
      <option value="farmer">Farmer</option>
      <option value="inspector">Inspector</option>
      <option value="transporter">Transporter</option>
      <option value="packaging">Packaging</option>
      <option value="admin">Admin</option>
    </select>
    <button class="w-full bg-green-600 text-white p-2 rounded">Register</button>
  </form>
</div>
<?php include __DIR__.'/../includes/footer.php'; ?>
