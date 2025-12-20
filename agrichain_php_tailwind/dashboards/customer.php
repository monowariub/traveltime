<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
roles_allowed(['customer']); // Ensure only customers can access
?>
<?php include __DIR__.'/../includes/header.php'; ?>
<?php include __DIR__.'/../includes/navbar.php'; ?>

<div class="flex max-w-7xl mx-auto mt-6 gap-6">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow rounded-xl p-4 hidden md:block">
        <h2 class="text-xl font-bold mb-4 text-green-700">Customer Dashboard</h2>
        <ul class="space-y-2 text-gray-700">
            <li>
                <a href="/agrichain_php_tailwind/modules/customer/orders.php" class="block px-3 py-2 rounded hover:bg-green-100">
                    📦 My Orders
                </a>
            </li>
            <li>
                <a href="/agrichain_php_tailwind/modules/customer/tracking.php" class="block px-3 py-2 rounded hover:bg-green-100">
                    🚚 Track Shipments
                </a>
            </li>
            <li>
                <a href="/agrichain_php_tailwind/modules/customer/feedback.php" class="block px-3 py-2 rounded hover:bg-green-100">
                    💬 Give Feedback
                </a>
            </li>
            <li>
                <a href="/agrichain_php_tailwind/modules/customer/notifications.php" class="block px-3 py-2 rounded hover:bg-green-100">
                    🔔 Notifications
                </a>
            </li>
            <li>
                <a href="/agrichain_php_tailwind/modules/customer/profile.php" class="block px-3 py-2 rounded hover:bg-green-100">
                    👤 Profile & Settings
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-white shadow rounded-xl p-6">
        <h1 class="text-3xl font-bold text-green-700 mb-6">
            Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?>!
        </h1>
        <p class="text-gray-600 mb-6">
            Use the quick links below to place orders, track shipments, provide feedback, and manage your profile and notifications.
        </p>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="/agrichain_php_tailwind/modules/customer/orders.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                📦 <span class="font-semibold">My Orders</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/customer/tracking.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                🚚 <span class="font-semibold">Track Shipments</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/customer/feedback.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                💬 <span class="font-semibold">Give Feedback</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/customer/notifications.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                🔔 <span class="font-semibold">Notifications</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/customer/profile.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                👤 <span class="font-semibold">Profile & Settings</span>
            </a>
        </div>
    </main>
</div>

<?php include __DIR__.'/../includes/footer.php'; ?>
