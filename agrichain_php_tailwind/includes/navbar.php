<?php require_once __DIR__.'/../config/auth.php'; ?>
<nav class="bg-white border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
    <a href="/index.php" class="font-bold text-xl">🌾 AgriChain</a>
    <div class="flex items-center gap-4">
      <?php if (is_logged_in()): $u=current_user(); ?>
        <span class="text-sm text-gray-600">Hello, <?=htmlspecialchars($u['name'])?> (<?=htmlspecialchars(ucfirst($u['role']))?>)</span>
        <a class="px-3 py-1 rounded-lg bg-gray-100 hover:bg-gray-200" href="/agrichain_php_tailwind/dashboard.php">Dashboard</a>
        <a class="px-3 py-1 rounded-lg bg-red-500 text-white" href="/agrichain_php_tailwind/auth/logout.php">Logout</a>
      <?php else: ?>
        <a class="px-3 py-1 rounded-lg bg-green-600 text-white" href="/agrichain_php_tailwind/auth/login.php">Login</a>
        <a class="px-3 py-1 rounded-lg bg-gray-800 text-white" href="/agrichain_php_tailwind/auth/register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
