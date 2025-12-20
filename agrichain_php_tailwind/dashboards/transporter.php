<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
// Allow transporter + admin
roles_allowed(['transporter','admin']);

include __DIR__.'/../includes/header.php';
include __DIR__.'/../includes/navbar.php';
?>

<div class="flex flex-col md:flex-row max-w-7xl mx-auto mt-6 gap-6">
    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-white shadow rounded-xl p-4">
        <h2 class="text-xl font-bold mb-4 text-green-700">Transporter Dashboard</h2>
        <ul class="space-y-2 text-gray-700">
            <li><a href="/agrichain_php_tailwind/modules/transport/map.php" class="block px-3 py-2 rounded hover:bg-green-100">🗺 Live Map & GPS</a></li>
            <li><a href="/agrichain_php_tailwind/modules/transport/routes.php" class="block px-3 py-2 rounded hover:bg-green-100">🛣 Route Optimization</a></li>
            <li><a href="/agrichain_php_tailwind/modules/transport/shipments.php" class="block px-3 py-2 rounded hover:bg-green-100">📦 Shipment Tracking</a></li>
            <li><a href="/agrichain_php_tailwind/modules/transport/notifications.php" class="block px-3 py-2 rounded hover:bg-green-100">🔔 Notifications</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-white shadow rounded-xl p-6">
        <h1 class="text-3xl font-bold text-green-700 mb-6">
            Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Transporter') ?>!
        </h1>
        <p class="text-gray-600 mb-6">
            Use the quick links below to view live maps, optimize routes, track shipments, and receive notifications for your deliveries.
        </p>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="/agrichain_php_tailwind/modules/transport/map.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                🗺 <span class="font-semibold">Live Map & GPS</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/transport/routes.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                🛣 <span class="font-semibold">Route Optimization</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/transport/shipments.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                📦 <span class="font-semibold">Shipment Tracking</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/transport/notifications.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                🔔 <span class="font-semibold">Notifications</span>
            </a>
        </div>
    </main>
</div>

<?php include __DIR__.'/../includes/footer.php'; ?>
