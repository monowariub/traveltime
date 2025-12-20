<?php
require_once __DIR__ . '/../../config/auth.php';
roles_allowed(['packaging','admin']);

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-green-700 mb-4">📊 Inventory & Labels</h1>
    <p class="text-sm text-gray-600 mb-6">
        Manage inventory and generate labels for packaged products. (Stub — implement CRUD here)
    </p>

    <!-- Example Inventory Table Stub -->
    <div class="bg-white shadow rounded-xl p-6 overflow-x-auto">
        <table class="w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">Product</th>
                    <th class="px-4 py-2 border">Quantity</th>
                    <th class="px-4 py-2 border">Label</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-4">No inventory records found.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
