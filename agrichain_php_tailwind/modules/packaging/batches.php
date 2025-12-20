<?php
require_once __DIR__ . '/../../config/auth.php';
roles_allowed(['packaging','admin']);
require_once __DIR__ . '/../../config/config.php';

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

// Handle batch creation (Packaging user)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_batch'])) {
    $stmt = $pdo->prepare("INSERT INTO batches (code, product, quantity, status) VALUES (?,?,?,?)");
    $stmt->execute([
        $_POST['code'],
        $_POST['product'],
        $_POST['quantity'],
        'pending'
    ]);
    header("Location: batches.php");
    exit;
}

// Handle batch deletion
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM batches WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: batches.php");
    exit;
}

// Fetch all packaging batches
$stmt = $pdo->query("SELECT * FROM batches ORDER BY created_at DESC");
$batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-green-700 mb-4">📦 Batches (Packaging)</h1>
    <p class="text-sm text-gray-600 mb-6">
        Create and manage packaging batches. No dependency on Farmer data.
    </p>

    <!-- Add Batch Form -->
    <form method="post" class="bg-white shadow rounded-xl p-6 mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" name="code" placeholder="Batch Code" class="border rounded p-2" required>
        <input type="text" name="product" placeholder="Product Name" class="border rounded p-2" required>
        <input type="number" name="quantity" placeholder="Quantity" class="border rounded p-2" required>
        <button type="submit" name="add_batch" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Batch</button>
    </form>

    <!-- Batches Table -->
    <div class="bg-white shadow rounded-xl p-6 overflow-x-auto">
        <table class="w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">Batch Code</th>
                    <th class="px-4 py-2 border">Product</th>
                    <th class="px-4 py-2 border">Quantity</th>
                    <th class="px-4 py-2 border">Status</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($batches)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">No batches found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($batches as $b): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?= $b['id'] ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($b['code']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($b['product']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($b['quantity']) ?></td>
                            <td class="px-4 py-2 border"><?= ucfirst($b['status']) ?></td>
                            <td class="px-4 py-2 border">
                                <a href="batches.php?delete=<?= $b['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this batch?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
