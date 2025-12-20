<?php
require_once __DIR__ . '/../../config/auth.php';
roles_allowed(['admin']);

$logs = $pdo->query("
    SELECT al.id, u.email, al.module, al.action, al.created_at
    FROM access_logs al
    LEFT JOIN users u ON u.id = al.user_id
    ORDER BY al.id DESC
    LIMIT 200
")->fetchAll();
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4 text-green-700">📜 Module Access Logs</h1>

  <div class="bg-white rounded-xl border shadow overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-100 text-gray-700">
        <tr>
          <th class="p-2 text-left">#</th>
          <th class="p-2 text-left">User</th>
          <th class="p-2 text-left">Module</th>
          <th class="p-2 text-left">Action</th>
          <th class="p-2 text-left">At</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($logs)): ?>
          <?php foreach ($logs as $row): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="p-2"><?= intval($row['id']) ?></td>
              <td class="p-2"><?= htmlspecialchars($row['email'] ?? 'System') ?></td>
              <td class="p-2 font-semibold text-green-700"><?= htmlspecialchars($row['module']) ?></td>
              <td class="p-2"><?= htmlspecialchars($row['action']) ?></td>
              <td class="p-2"><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="p-4 text-center text-gray-500">No logs found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
