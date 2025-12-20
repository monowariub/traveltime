<?php
// modules/farmer/crop.php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/auth.php";

// Allow only farmer & admin roles
roles_allowed(['farmer','admin']);

// Handle form submissions (CRUD)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Crop
    if (isset($_POST['add_crop'])) {
        $stmt = $pdo->prepare("INSERT INTO crops (farmer_id, name, planting_date, harvest_date, fertilizer) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user']['id'], 
            trim($_POST['name']), 
            $_POST['planting_date'], 
            $_POST['harvest_date'], 
            trim($_POST['fertilizer'])
        ]);
        header("Location: crop.php"); exit;
    }

    // Delete Crop
    if (isset($_POST['delete_crop'])) {
        $stmt = $pdo->prepare("DELETE FROM crops WHERE id = ? AND farmer_id = ?");
        $stmt->execute([$_POST['id'], $_SESSION['user']['id']]);
        header("Location: crop.php"); exit;
    }

    // Update Crop
    if (isset($_POST['update_crop'])) {
        $stmt = $pdo->prepare("UPDATE crops 
                               SET name=?, planting_date=?, harvest_date=?, fertilizer=? 
                               WHERE id=? AND farmer_id=?");
        $stmt->execute([
            trim($_POST['name']), 
            $_POST['planting_date'], 
            $_POST['harvest_date'], 
            trim($_POST['fertilizer']), 
            $_POST['id'], 
            $_SESSION['user']['id']
        ]);
        header("Location: crop.php"); exit;
    }
}

// Fetch crops for this farmer
$stmt = $pdo->prepare("SELECT * FROM crops WHERE farmer_id = ? ORDER BY planting_date DESC");
$stmt->execute([$_SESSION['user']['id']]);
$crops = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . "/../../includes/header.php";
include __DIR__ . "/../../includes/navbar.php";
?>

<div class="max-w-5xl mx-auto mt-10 p-6 bg-white shadow rounded-2xl">
    <h1 class="text-2xl font-bold mb-4">🌱 Crops</h1>
    <p class="text-sm text-gray-600 mb-6">Manage your crops, fertilizer usage, and harvest dates.</p>

    <!-- Add Crop Form -->
    <form method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 bg-gray-50 p-4 rounded-xl mb-6">
        <input type="text" name="name" placeholder="Crop Name" required class="p-2 border rounded">
        <input type="date" name="planting_date" required class="p-2 border rounded">
        <input type="date" name="harvest_date" required class="p-2 border rounded">
        <input type="text" name="fertilizer" placeholder="Fertilizer" class="p-2 border rounded">
        <button type="submit" name="add_crop" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">➕ Add</button>
    </form>

    <!-- Crop List -->
    <div class="overflow-x-auto">
        <table class="min-w-full border rounded-xl">
            <thead class="bg-green-100">
                <tr>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Planting Date</th>
                    <th class="p-2 border">Harvest Date</th>
                    <th class="p-2 border">Fertilizer</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($crops as $crop): ?>
                    <tr class="hover:bg-gray-50">
                        <form method="POST">
                            <td class="p-2 border">
                                <input type="text" name="name" value="<?= htmlspecialchars($crop['name']) ?>" class="p-2 border rounded w-full">
                            </td>
                            <td class="p-2 border">
                                <input type="date" name="planting_date" value="<?= htmlspecialchars($crop['planting_date']) ?>" class="p-2 border rounded w-full">
                            </td>
                            <td class="p-2 border">
                                <input type="date" name="harvest_date" value="<?= htmlspecialchars($crop['harvest_date']) ?>" class="p-2 border rounded w-full">
                            </td>
                            <td class="p-2 border">
                                <input type="text" name="fertilizer" value="<?= htmlspecialchars($crop['fertilizer']) ?>" class="p-2 border rounded w-full">
                            </td>
                            <td class="p-2 border flex gap-2">
                                <input type="hidden" name="id" value="<?= $crop['id'] ?>">
                                <button type="submit" name="update_crop" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Update</button>
                                <button type="submit" name="delete_crop" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700" onclick="return confirm('Delete this crop?')">Delete</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($crops)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 p-4">No crops added yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
