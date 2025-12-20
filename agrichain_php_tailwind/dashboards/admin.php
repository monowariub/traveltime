<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
roles_allowed(['admin']); // ensure only admins access
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="flex max-w-7xl mx-auto mt-6 gap-6">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow rounded-xl p-4 hidden md:block">
        <h2 class="text-xl font-bold mb-4 text-green-700">Admin Dashboard</h2>
        <ul class="space-y-2 text-gray-700">
            <li><a href="/agrichain_php_tailwind/modules/admin/users.php" class="block px-3 py-2 rounded hover:bg-green-100">👤 Manage Users</a></li>
            <li><a href="/agrichain_php_tailwind/modules/admin/roles.php" class="block px-3 py-2 rounded hover:bg-green-100">🔑 Roles & Permissions</a></li>
            <li><a href="/agrichain_php_tailwind/modules/admin/logs.php" class="block px-3 py-2 rounded hover:bg-green-100">📜 Module Access Logs</a></li>
            <li><a href="/agrichain_php_tailwind/modules/admin/reports.php" class="block px-3 py-2 rounded hover:bg-green-100">📊 System Reports</a></li>
            <li><a href="/agrichain_php_tailwind/modules/admin/inspections.php" class="block px-3 py-2 rounded hover:bg-green-100">✅ Inspection Overview</a></li>
            <li><a href="/agrichain_php_tailwind/modules/admin/transport.php" class="block px-3 py-2 rounded hover:bg-green-100">🚚 Transport Overview</a></li>
            <li><a href="/agrichain_php_tailwind/modules/admin/batches.php" class="block px-3 py-2 rounded hover:bg-green-100">📦 Packaging Inventory</a></li>
            <li><a href="/agrichain_php_tailwind/modules/admin/orders.php" class="block px-3 py-2 rounded hover:bg-green-100">📑 Customer Orders</a></li>
            <li><a href="/agrichain_php_tailwind/reports/export_excel.php" class="block px-3 py-2 rounded hover:bg-green-100">📊 Export CSV/Excel</a></li>
            <li><a href="/agrichain_php_tailwind/modules/admin/notifications.php" class="block px-3 py-2 rounded hover:bg-green-100">🔔 Notifications</a></li>
            <li><a href="/agrichain_php_tailwind/modules/admin/settings.php" class="block px-3 py-2 rounded hover:bg-green-100">⚙️ Settings</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-white shadow rounded-xl p-6">
        <h1 class="text-3xl font-bold text-green-700 mb-6">
            Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?>!
        </h1>
        <p class="text-gray-600 mb-6">
            Use the quick links below to manage users, monitor module access logs, handle orders, check inspections, and export reports.
        </p>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="/agrichain_php_tailwind/modules/admin/users.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">👤 <span class="font-semibold">Manage Users</span></a>
            <a href="/agrichain_php_tailwind/modules/admin/roles.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">🔑 <span class="font-semibold">Roles & Permissions</span></a>
            <a href="/agrichain_php_tailwind/modules/admin/logs.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">📜 <span class="font-semibold">Module Access Logs</span></a>
            <a href="/agrichain_php_tailwind/modules/admin/reports.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">📊 <span class="font-semibold">System Reports</span></a>
            <a href="/agrichain_php_tailwind/modules/admin/inspections.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">✅ <span class="font-semibold">Inspection Overview</span></a>
            <a href="/agrichain_php_tailwind/modules/admin/transport.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">🚚 <span class="font-semibold">Transport Overview</span></a>
            <a href="/agrichain_php_tailwind/modules/admin/batches.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">📦 <span class="font-semibold">Packaging Inventory</span></a>
            <a href="/agrichain_php_tailwind/modules/admin/orders.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">📑 <span class="font-semibold">Customer Orders</span></a>
            <a href="/agrichain_php_tailwind/reports/export_excel.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">📊 <span class="font-semibold">Export CSV/Excel</span></a>
            <a href="/agrichain_php_tailwind/modules/admin/notifications.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">🔔 <span class="font-semibold">Notifications</span></a>
            <a href="/agrichain_php_tailwind/modules/admin/settings.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">⚙️ <span class="font-semibold">Settings</span></a>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
