<?php
require_once __DIR__ . '/../../config/auth.php';
roles_allowed(['customer','admin']);
require_once __DIR__ . '/../../config/config.php';
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

// --- Delete ---
if (isset($_GET['delete'])) {
    $fid = (int) $_GET['delete'];
    if ($role === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM feedback WHERE id=?");
        $stmt->execute([$fid]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM feedback WHERE id=? AND user_id=?");
        $stmt->execute([$fid, $user_id]);
    }
    header("Location: feedback.php?success=Deleted successfully!");
    exit;
}

// --- Edit fetch ---
$editing = null;
if (isset($_GET['edit'])) {
    $fid = (int) $_GET['edit'];
    if ($role === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM feedback WHERE id=?");
        $stmt->execute([$fid]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM feedback WHERE id=? AND user_id=?");
        $stmt->execute([$fid, $user_id]);
    }
    $editing = $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- Save (Insert/Update) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    if (!empty($_POST['fid'])) {
        $fid = (int) $_POST['fid'];
        if ($role === 'admin') {
            $stmt = $pdo->prepare("UPDATE feedback SET message=? WHERE id=?");
            $stmt->execute([trim($_POST['message']), $fid]);
        } else {
            $stmt = $pdo->prepare("UPDATE feedback SET message=? WHERE id=? AND user_id=?");
            $stmt->execute([trim($_POST['message']), $fid, $user_id]);
        }
        $msg = "Feedback updated successfully!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, message) VALUES (?, ?)");
        $stmt->execute([$user_id, trim($_POST['message'])]);
        $msg = "Feedback submitted successfully!";
    }
    header("Location: feedback.php?success=" . urlencode($msg));
    exit;
}

// --- Load feedbacks ---
if ($role === 'admin') {
    $stmt = $pdo->query("
        SELECT f.*, u.name 
        FROM feedback f 
        JOIN users u ON f.user_id = u.id 
        ORDER BY f.created_at DESC
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT f.*, u.name 
        FROM feedback f 
        JOIN users u ON f.user_id=u.id
        WHERE f.user_id=? ORDER BY f.created_at DESC
    ");
    $stmt->execute([$user_id]);
}
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-green-700">Feedback</h1>

    <?php if (!empty($_GET['success'])): ?>
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-6 shadow">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <!-- Feedback form -->
    <form method="POST" class="mb-8 bg-white shadow-lg rounded-xl p-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            <?= $editing ? "✏️ Edit Feedback" : "➕ New Feedback" ?>
        </label>
        <textarea name="message" rows="3" required
            class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-300 focus:border-green-400 p-3"><?= $editing ? htmlspecialchars($editing['message']) : "" ?></textarea>
        <?php if ($editing): ?>
            <input type="hidden" name="fid" value="<?= $editing['id'] ?>">
        <?php endif; ?>
        <div class="mt-3 flex items-center gap-3">
            <button type="submit"
                class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 shadow">
                <?= $editing ? "Update" : "Submit" ?>
            </button>
            <?php if ($editing): ?>
                <a href="feedback.php" class="text-gray-500 hover:underline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Feedback list -->
    <div class="space-y-4">
        <?php if (count($feedbacks) === 0): ?>
            <p class="text-gray-500">No feedback yet.</p>
        <?php else: ?>
            <?php foreach ($feedbacks as $fb): ?>
                <div class="bg-white shadow rounded-xl p-5">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-green-700">
                            <?= htmlspecialchars($fb['name']) ?>
                        </span>
                        <span class="text-xs text-gray-400"><?= $fb['created_at'] ?></span>
                    </div>
                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($fb['message'])) ?></p>
                    <?php if ($role === 'admin' || $fb['user_id'] == $user_id): ?>
                        <div class="mt-3 flex gap-4 text-sm">
                            <a href="feedback.php?edit=<?= $fb['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="feedback.php?delete=<?= $fb['id'] ?>"
                               onclick="return confirm('Delete this feedback?')"
                               class="text-red-600 hover:underline">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
