<?php
require_once __DIR__ . '/config.php';

function current_user() { return $_SESSION['user'] ?? null; }
function is_logged_in() { return !empty($_SESSION['user']); }
function require_login() {
    if (!is_logged_in()) { header('Location: /auth/login.php'); exit; }
}
function has_role($role) {
    $u = current_user();
    return $u && strtolower($u['role']) === strtolower($role);
}
function require_role($role) {
    require_login();
    if (!has_role($role)) { http_response_code(403); echo "Forbidden"; exit; }
}
function roles_allowed($roles = []) {
    require_login();
    $u = current_user();
    if (!$u || !in_array(strtolower($u['role']), array_map('strtolower', $roles))) {
        http_response_code(403); echo "Forbidden"; exit;
    }
}

function log_access($module, $action) {
    global $pdo;
    $uid = current_user()['id'] ?? null;
    $stmt = $pdo->prepare("INSERT INTO access_logs (user_id, module, action) VALUES (:uid,:m,:a)");
    $stmt->execute([':uid'=>$uid, ':m'=>$module, ':a'=>$action]);
}
?>
