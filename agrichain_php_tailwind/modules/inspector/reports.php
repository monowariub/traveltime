<?php
// Fix relative paths
require_once __DIR__ . '/../../config/auth.php';
require_login();
roles_allowed(['inspector','admin']); // Only inspector & admin

require_once __DIR__ . '/../../config/config.php';

include __DIR__.'/../../includes/header.php';
include __DIR__.'/../../includes/navbar.php';
?>

<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-green-700">📄 Generate Inspection Reports</h1>
    <p class="text-gray-600 mb-6">
        Download or generate PDF reports for inspection records. (Stub functionality)
    </p>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Example: Generate PDF Report -->
        <div class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
            <h2 class="text-lg font-semibold mb-2">All Inspections</h2>
            <p class="text-sm text-gray-500 mb-4">Generate PDF for all inspection records.</p>
            <a href="#" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Generate PDF</a>
        </div>

        <div class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
            <h2 class="text-lg font-semibold mb-2">Batch Reports</h2>
            <p class="text-sm text-gray-500 mb-4">Generate PDF for selected batch inspections.</p>
            <a href="#" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Generate PDF</a>
        </div>

        <div class="p-6 rounded-xl bg-white border shadow hover:shadow-lg transition">
            <h2 class="text-lg font-semibold mb-2">Photo Reports</h2>
            <p class="text-sm text-gray-500 mb-4">Include inspection photos in the report.</p>
            <a href="#" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Generate PDF</a>
        </div>
    </div>
</div>

<?php include __DIR__.'/../../includes/footer.php'; ?>
