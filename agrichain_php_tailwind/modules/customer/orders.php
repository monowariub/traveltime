<?php
require_once __DIR__ . '/../../config/auth.php';
roles_allowed(['customer','admin']);
require_once __DIR__ . '/../../config/config.php';
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

// ---------------------------
// Customer: Place New Order
// ---------------------------
if ($role === 'customer' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batch_code'])) {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, batch_code, quantity, status) VALUES (?,?,?,?)");
    $stmt->execute([$user_id, $_POST['batch_code'], $_POST['quantity'], "pending"]);
    header("Location: orders.php");
    exit;
}

// ---------------------------
// Customer: Delete Order
// ---------------------------
if ($role === 'customer' && isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete'], $user_id]);
    header("Location: orders.php");
    exit;
}

// ---------------------------
// Admin: Update Order Status
// ---------------------------
if ($role === 'admin' && isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header("Location: orders.php");
    exit;
}

// ---------------------------
// Load Orders
// ---------------------------
if ($role === 'admin') {
    $stmt = $pdo->query("
        SELECT o.*, u.name AS customer_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT o.*, u.name AS customer_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user_id]);
}
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------
// Load Batches (for new orders)
// ---------------------------
$batchStmt = $pdo->query("SELECT code, product FROM batches ORDER BY created_at DESC");
$batches = $batchStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-green-700">📑 Orders</h1>

    <?php if ($role === 'customer'): ?>
    <!-- New Order Form -->
    <form method="post" class="bg-white shadow rounded-xl p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">➕ Place New Order</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium">Batch</label>
                <select name="batch_code" class="w-full border rounded p-2" required>
                    <option value="">-- Select Batch --</option>
                    <?php foreach ($batches as $batch): ?>
                        <option value="<?= htmlspecialchars($batch['code']) ?>">
                            <?= htmlspecialchars($batch['code']) ?> (<?= htmlspecialchars($batch['product']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Quantity</label>
                <input type="number" name="quantity" class="w-full border rounded p-2" required>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Place Order
                </button>
            </div>
        </div>
    </form>
    <?php endif; ?>

    <!-- Orders Table -->
    <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">📦 Orders</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">ID</th>
                        <?php if ($role === 'admin'): ?>
                            <th class="px-4 py-2 border">Customer</th>
                        <?php endif; ?>
                        <th class="px-4 py-2 border">Batch</th>
                        <th class="px-4 py-2 border">Quantity</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Date</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) === 0): ?>
                        <tr><td colspan="<?= $role==='admin'?7:6 ?>" class="text-center py-4 text-gray-500">No orders found</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?= $o['id'] ?></td>
                            <?php if ($role === 'admin'): ?>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($o['customer_name']) ?></td>
                            <?php endif; ?>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($o['batch_code']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($o['quantity']) ?></td>
                            <td class="px-4 py-2 border">
                                <?php if ($role === 'admin'): ?>
                                    <form method="post" class="flex space-x-2">
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <select name="status" class="border rounded p-1 text-sm">
                                            <?php foreach (["pending","shipped","delivered","cancelled"] as $s): ?>
                                                <option value="<?= $s ?>" <?= $o['status']===$s?"selected":"" ?>>
                                                    <?= ucfirst($s) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="update_status" class="bg-blue-500 text-white px-2 rounded">✔</button>
                                    </form>
                                <?php else: ?>
                                    <span class="px-2 py-1 rounded text-xs 
                                        <?= $o['status']=="pending"?"bg-yellow-200 text-yellow-800":"" ?>
                                        <?= $o['status']=="shipped"?"bg-blue-200 text-blue-800":"" ?>
                                        <?= $o['status']=="delivered"?"bg-green-200 text-green-800":"" ?>
                                        <?= $o['status']=="cancelled"?"bg-red-200 text-red-800":"" ?>">
                                        <?= ucfirst($o['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 border"><?= $o['created_at'] ?></td>
                            <td class="px-4 py-2 border">
                                <?php if ($role === 'customer'): ?>
                                    <a href="orders.php?delete=<?= $o['id'] ?>" 
                                       onclick="return confirm('Delete this order?')" 
                                       class="text-red-600 hover:underline">Delete</a>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
