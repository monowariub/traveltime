<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
roles_allowed(['packaging']); // ensure only packaging role access

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="flex max-w-7xl mx-auto mt-6 gap-6">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow rounded-xl p-4 hidden md:block">
        <h2 class="text-xl font-bold mb-4 text-green-700">Packaging Dashboard</h2>
        <ul class="space-y-2 text-gray-700">
            <li><a href="/agrichain_php_tailwind/modules/packaging/batches.php" class="block px-3 py-2 rounded hover:bg-green-100 transition">📦 Batch Management</a></li>
            <li><a href="/agrichain_php_tailwind/modules/packaging/inventory.php" class="block px-3 py-2 rounded hover:bg-green-100 transition">📊 Inventory & Labels</a></li>
            <li><a href="/agrichain_php_tailwind/modules/packaging/qr_codes.php" class="block px-3 py-2 rounded hover:bg-green-100 transition">🔗 QR / Barcode Generation</a></li>
            <li><a href="/agrichain_php_tailwind/modules/packaging/shipments.php" class="block px-3 py-2 rounded hover:bg-green-100 transition">🚚 Shipment Tracking</a></li>
            <li><a href="/agrichain_php_tailwind/modules/packaging/notifications.php" class="block px-3 py-2 rounded hover:bg-green-100 transition">🔔 Notifications</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-white shadow rounded-xl p-6">
        <h1 class="text-3xl font-bold text-green-700 mb-6">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</h1>
        <p class="text-gray-600 mb-6">
            Use the quick links below to manage batches, inventory, labels, generate QR/barcodes, track shipments, and receive notifications.
        </p>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="/agrichain_php_tailwind/modules/packaging/batches.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">📦 <span class="font-semibold">Batch Management</span></a>
            <a href="/agrichain_php_tailwind/modules/packaging/inventory.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">📊 <span class="font-semibold">Inventory & Labels</span></a>
            <a href="/agrichain_php_tailwind/modules/packaging/qr_codes.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">🔗 <span class="font-semibold">QR / Barcode Generation</span></a>
            <a href="/agrichain_php_tailwind/modules/packaging/shipments.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">🚚 <span class="font-semibold">Shipment Tracking</span></a>
            <a href="/agrichain_php_tailwind/modules/packaging/notifications.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">🔔 <span class="font-semibold">Notifications</span></a>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
