<?php
require_once __DIR__.'/../../config/auth.php';
require_once __DIR__.'/../../config/config.php';

roles_allowed(['inspector','admin']);

// Fetch batches (stub) – later replace with actual DB query
$batches = [
    ['code'=>'BATCH001','product'=>'Tomatoes','quantity'=>'200 kg','harvest_date'=>'2025-09-01','status'=>'Pending Verification'],
    ['code'=>'BATCH002','product'=>'Potatoes','quantity'=>'150 kg','harvest_date'=>'2025-09-05','status'=>'Verified'],
];

include __DIR__.'/../../includes/header.php';
include __DIR__.'/../../includes/navbar.php';
?>

<div class="max-w-5xl mx-auto p-6 bg-white shadow rounded-xl">
    <h1 class="text-2xl font-bold mb-4 text-green-700">📦 Batch Verification</h1>
    <p class="text-sm text-gray-600 mb-6">
        Manage your batches below. Future integration will allow CRUD operations and photo uploads.
    </p>

    <div class="overflow-x-auto">
        <table class="min-w-full border rounded-xl text-sm">
            <thead class="bg-green-100">
                <tr>
                    <th class="p-2 border">Batch Code</th>
                    <th class="p-2 border">Product</th>
                    <th class="p-2 border">Quantity</th>
                    <th class="p-2 border">Harvest Date</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($batches as $batch): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-2"><?= htmlspecialchars($batch['code']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($batch['product']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($batch['quantity']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($batch['harvest_date']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($batch['status']) ?></td>
                    <td class="p-2 flex gap-2">
                        <button class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Verify</button>
                        <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($batches)): ?>
                <tr>
                    <td colspan="6" class="text-center text-gray-500 p-4">No batches found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__.'/../../includes/footer.php'; ?>
