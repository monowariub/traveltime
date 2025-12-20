<?php
// AgriChain config: uses SQLite by default for ease of local dev.
// If you want to use MySQL (XAMPP), set the environment variables below and the config will try MySQL first.
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// MySQL settings (optional - set these in your environment or edit here)
$MYSQL_HOST = getenv('AGRICHAIN_DB_HOST') ?: '127.0.0.1';
$MYSQL_NAME = getenv('AGRICHAIN_DB_NAME') ?: 'agrichain';
$MYSQL_USER = getenv('AGRICHAIN_DB_USER') ?: 'root';
$MYSQL_PASS = getenv('AGRICHAIN_DB_PASS') ?: '';

$pdo = null;
// Try MySQL (useful for XAMPP), otherwise fall back to SQLite bundled with the project.
try {
    // Attempt MySQL connection
    $pdo = new PDO("mysql:host={$MYSQL_HOST};dbname={$MYSQL_NAME};charset=utf8mb4", $MYSQL_USER, $MYSQL_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    // Fallback to SQLite
    $dbPath = __DIR__ . '/../database.db';
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (Exception $e2) {
        die('DB Connection failed: ' . htmlspecialchars($e2->getMessage()));
    }
}

// csrf helper
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
function csrf_token() { return $_SESSION['csrf']; }
function csrf_check($token) { return hash_equals($_SESSION['csrf'] ?? '', $token ?? ''); }
?>
