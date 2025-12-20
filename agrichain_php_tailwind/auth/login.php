<?php
require_once __DIR__ . '/../config/config.php';
$msg = isset($_GET['registered']) ? 'Registration successful. Please log in.' : '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) { $errors[]='Invalid CSRF token.'; }
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :e");
  $stmt->execute([':e'=>$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($user && password_verify($pass, $user['password_hash'])) {
    $_SESSION['user'] = ['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email'],'role'=>$user['role']];
    header('Location: /agrichain_php_tailwind/dashboard.php'); exit;
  } else {
    $errors[] = 'Invalid credentials.';
  }
}
?>
<?php include __DIR__.'/../includes/header.php'; ?>
<?php include __DIR__.'/../includes/navbar.php'; ?>
<div class="max-w-md mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Login</h1>
  <?php if($msg): ?><div class="mb-2 p-3 bg-green-100 text-green-700 rounded"><?=$msg?></div><?php endif; ?>
  <?php foreach($errors as $e): ?>
    <div class="mb-2 p-3 bg-red-100 text-red-700 rounded"><?=htmlspecialchars($e)?></div>
  <?php endforeach; ?>
  <form method="post" class="space-y-4">
    <input type="hidden" name="csrf" value="<?=csrf_token()?>">
    <input class="w-full border rounded p-2" name="email" type="email" placeholder="Email">
    <input class="w-full border rounded p-2" name="password" type="password" placeholder="Password">
    <button class="w-full bg-green-600 text-white p-2 rounded">Login</button>
  </form>
</div>
<?php include __DIR__.'/../includes/footer.php'; ?>
