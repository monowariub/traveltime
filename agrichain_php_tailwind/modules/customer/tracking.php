<?php
require_once __DIR__ . '/../../config/auth.php';
roles_allowed(['customer','admin']);
require_once __DIR__ . '/../../config/config.php';
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

// Load orders
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
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

// ---------------------------
// Progress Step Helper
// ---------------------------
function progressSteps($status) {
    $steps = ["pending", "shipped", "delivered"];
    $statusLower = strtolower($status);

    if ($statusLower === "cancelled") {
        return "<div class='flex items-center mt-2 space-x-2 text-red-600 font-semibold'>❌ Cancelled</div>";
    }

    $html = "<div class='flex items-center mt-2 space-x-2 md:space-x-4'>";
    foreach ($steps as $i => $step) {
        $active = array_search($statusLower, $steps) >= $i;
        $circleClass = $active ? "bg-green-600 text-white" : "bg-gray-200 text-gray-500";
        $labelClass = $active ? "text-green-700 font-semibold" : "text-gray-400";

        $html .= "<div class='flex flex-col items-center w-12 md:w-16'>
                    <div class='w-8 h-8 flex items-center justify-center rounded-full {$circleClass}'>
                        ".($i+1)."
                    </div>
                    <span class='text-xs mt-1 {$labelClass}'>".ucfirst($step)."</span>
                  </div>";

        if ($i < count($steps) - 1) {
            $html .= "<div class='flex-1 h-1 ".($active ? "bg-green-500" : "bg-gray-200")."'></div>";
        }
    }
    $html .= "</div>";
    return $html;
}
?>

<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-green-700">📦 Order Tracking</h1>

    <?php if (empty($orders)): ?>
        <p class="text-gray-500">No orders found.</p>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($orders as $order): ?>
                <div class="bg-white shadow rounded-xl p-6 hover:shadow-lg transition">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg font-semibold text-gray-800">Order #<?= htmlspecialchars($order['id']) ?></h2>
                        <span class="text-sm text-gray-500"><?= htmlspecialchars($order['created_at']) ?></span>
                    </div>
                    <?php if ($role === 'admin'): ?>
                        <p class="text-sm text-gray-600">👤 Customer: <b><?= htmlspecialchars($order['customer_name']) ?></b></p>
                    <?php endif; ?>
                    <p class="text-sm text-gray-600">Batch Code: <b><?= htmlspecialchars($order['batch_code']) ?></b></p>
                    <p class="text-sm text-gray-600">Quantity: <b><?= htmlspecialchars($order['quantity']) ?></b></p>
                    <div class="mt-4">
                        <?= progressSteps($order['status']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
