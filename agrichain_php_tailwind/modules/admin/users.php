<?php
require_once __DIR__ . '/../../config/auth.php';
roles_allowed(['admin']); // only admins
log_access('admin', 'users_view');

// handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check($_POST['csrf'] ?? '')) {
    if (isset($_POST['create'])) {
        // basic validation
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? 'changeme';
        $role = $_POST['role'] ?? 'customer';

        if ($name && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $pdo->prepare("
                INSERT INTO users (name,email,password_hash,role,created_at) 
                VALUES (:n,:e,:p,:r,NOW())
            ");
            $stmt->execute([
                ':n' => $name,
                ':e' => $email,
                ':p' => password_hash($password, PASSWORD_DEFAULT),
                ':r' => $role
            ]);
        }
    }

    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=:id");
        $stmt->execute([':id' => intval($_POST['id'] ?? 0)]);
    }
}

$users = $pdo->query("SELECT id,name,email,role,created_at FROM users ORDER BY id DESC")->fetchAll();
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4 text-green-700">👥 Manage Users</h1>

  <!-- Create User -->
  <form method="post" class="grid md:grid-cols-5 gap-2 mb-6 bg-white p-4 rounded-xl border shadow">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input class="border rounded p-2" name="name" placeholder="Name" required>
    <input class="border rounded p-2" name="email" type="email" placeholder="Email" required>
    <input class="border rounded p-2" name="password" type="password" placeholder="Password" required>
    <select class="border rounded p-2" name="role">
      <option value="admin">Admin</option>
      <option value="farmer">Farmer</option>
      <option value="inspector">Inspector</option>
      <option value="transporter">Transporter</option>
      <option value="packaging">Packaging</option>
      <option value="customer" selected>Customer</option>
    </select>
    <button name="create" class="bg-green-600 text-white rounded p-2 hover:bg-green-700 transition">Create</button>
  </form>

  <!-- Users Table -->
  <div class="bg-white rounded-xl border shadow overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-100 text-gray-700">
        <tr>
          <th class="p-2 text-left">ID</th>
          <th class="p-2">Name</th>
          <th class="p-2">Email</th>
          <th class="p-2">Role</th>
          <th class="p-2">Created</th>
          <th class="p-2">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="p-2"><?= intval($u['id']) ?></td>
            <td class="p-2"><?= htmlspecialchars($u['name']) ?></td>
            <td class="p-2"><?= htmlspecialchars($u['email']) ?></td>
            <td class="p-2 font-semibold text-green-700"><?= htmlspecialchars($u['role']) ?></td>
            <td class="p-2"><?= htmlspecialchars($u['created_at']) ?></td>
            <td class="p-2">
              <form method="post" onsubmit="return confirm('Delete user?')" class="inline">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= intval($u['id']) ?>">
                <button name="delete" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
          <tr><td colspan="6" class="p-4 text-center text-gray-500">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
