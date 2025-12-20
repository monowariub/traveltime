<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
roles_allowed(['farmer']); // ensure only farmers access
?>
<?php include __DIR__.'/../includes/header.php'; ?>
<?php include __DIR__.'/../includes/navbar.php'; ?>

<div class="flex max-w-7xl mx-auto mt-6 gap-6">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow rounded-xl p-4 hidden md:block">
        <h2 class="text-xl font-bold mb-4 text-green-700">Farmer Dashboard</h2>
        <ul class="space-y-2 text-gray-700">
            <li><a href="/agrichain_php_tailwind/modules/farmer/crops.php" class="block px-3 py-2 rounded hover:bg-green-100">🌱 Crops & Fields</a></li>
            <li><a href="/agrichain_php_tailwind/modules/farmer/fertilizer.php" class="block px-3 py-2 rounded hover:bg-green-100">🧪 Fertilizer Plans</a></li>
            <li><a href="/agrichain_php_tailwind/modules/farmer/harvest.php" class="block px-3 py-2 rounded hover:bg-green-100">📊 Harvest Calendar & Analytics</a></li>
            <li><a href="/agrichain_php_tailwind/modules/farmer/batches.php" class="block px-3 py-2 rounded hover:bg-green-100">📦 My Harvest → Batches</a></li>
            <li><a href="/agrichain_php_tailwind/modules/farmer/inspections.php" class="block px-3 py-2 rounded hover:bg-green-100">✅ Inspection Reports</a></li>
            <li><a href="/agrichain_php_tailwind/modules/farmer/earnings.php" class="block px-3 py-2 rounded hover:bg-green-100">💰 My Earnings / Sales</a></li>
            <li><a href="/agrichain_php_tailwind/modules/farmer/transport.php" class="block px-3 py-2 rounded hover:bg-green-100">🚚 Transport Status</a></li>
            <li><a href="/agrichain_php_tailwind/modules/farmer/notifications.php" class="block px-3 py-2 rounded hover:bg-green-100">🔔 Notifications</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-white shadow rounded-xl p-6">
        <h1 class="text-3xl font-bold text-green-700 mb-6">
            Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Farmer') ?>!
        </h1>
        <p class="text-gray-600 mb-6">
            Use the quick links below to manage your farming activities, monitor your crops, harvests, and batches, and check your inspections and earnings.
        </p>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="p-4 bg-green-50 rounded-xl text-center">
                <div class="text-2xl font-bold text-green-700">8</div>
                <div class="text-gray-600 text-sm">Active Crops</div>
            </div>
            <div class="p-4 bg-yellow-50 rounded-xl text-center">
                <div class="text-2xl font-bold text-yellow-700">3</div>
                <div class="text-gray-600 text-sm">Upcoming Harvests</div>
            </div>
            <div class="p-4 bg-blue-50 rounded-xl text-center">
                <div class="text-2xl font-bold text-blue-700">5</div>
                <div class="text-gray-600 text-sm">Pending Inspections</div>
            </div>
            <div class="p-4 bg-red-50 rounded-xl text-center">
                <div class="text-2xl font-bold text-red-700">৳12,500</div>
                <div class="text-gray-600 text-sm">Total Earnings</div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="/agrichain_php_tailwind/modules/farmer/crops.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                🌱 <span class="font-semibold">Crops & Fields</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/farmer/fertilizer.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                🧪 <span class="font-semibold">Fertilizer Plans</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/farmer/harvest.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                📊 <span class="font-semibold">Harvest Calendar & Analytics</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/farmer/batches.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                📦 <span class="font-semibold">My Harvest → Batches</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/farmer/inspections.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                ✅ <span class="font-semibold">Inspection Reports</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/farmer/earnings.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                💰 <span class="font-semibold">My Earnings / Sales</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/farmer/transport.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                🚚 <span class="font-semibold">Transport Status</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/farmer/notifications.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                🔔 <span class="font-semibold">Notifications</span>
            </a>
        </div>
    </div>
</div>

<?php include __DIR__.'/../includes/footer.php'; ?>
