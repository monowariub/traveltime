<?php
require_once __DIR__ . '/../../config/auth.php'; // fixed path
require_login();
roles_allowed(['inspector']); // ensure only inspectors access
?>
<?php include __DIR__.'/../../includes/header.php'; ?>
<?php include __DIR__.'/../../includes/navbar.php'; ?>

<div class="flex max-w-7xl mx-auto mt-6 gap-6">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow rounded-xl p-4 hidden md:block">
        <h2 class="text-xl font-bold mb-4 text-green-700">Inspector Dashboard</h2>
        <ul class="space-y-2 text-gray-700">
            <li>
                <a href="/agrichain_php_tailwind/modules/inspector/inspections.php" class="block px-3 py-2 rounded hover:bg-green-100 transition">
                    📝 Inspection Records
                </a>
            </li>
            <li>
                <a href="/agrichain_php_tailwind/modules/inspector/batches.php" class="block px-3 py-2 rounded hover:bg-green-100 transition">
                    📦 Batch Verification
                </a>
            </li>
            <li>
                <a href="/agrichain_php_tailwind/modules/inspector/reports.php" class="block px-3 py-2 rounded hover:bg-green-100 transition">
                    📄 Generate Reports (PDF)
                </a>
            </li>
            <li>
                <a href="/agrichain_php_tailwind/modules/inspector/photos.php" class="block px-3 py-2 rounded hover:bg-green-100 transition">
                    📸 Inspection Photos
                </a>
            </li>
            <li>
                <a href="/agrichain_php_tailwind/modules/inspector/notifications.php" class="block px-3 py-2 rounded hover:bg-green-100 transition">
                    🔔 Notifications
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
            Use the links below to view inspection records, verify batches, upload inspection photos, and generate PDF reports.
        </p>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="/agrichain_php_tailwind/modules/inspector/inspections.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                📝 <span class="font-semibold">Inspection Records</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/inspector/batches.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                📦 <span class="font-semibold">Batch Verification</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/inspector/reports.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                📄 <span class="font-semibold">Generate Reports (PDF)</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/inspector/photos.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                📸 <span class="font-semibold">Inspection Photos</span>
            </a>
            <a href="/agrichain_php_tailwind/modules/inspector/notifications.php" class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
                🔔 <span class="font-semibold">Notifications</span>
            </a>
        </div>
    </main>
</div>

<?php include __DIR__.'/../../includes/footer.php'; ?>
