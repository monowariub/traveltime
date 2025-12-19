<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($_GET['action'])) {
    echo json_encode(['success' => false, 'error' => 'No action specified']);
    exit();
}

$action = $_GET['action'];

if ($action === 'register') {
    if (!isset($data->name) || !isset($data->email) || !isset($data->password)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit();
    }

    $name = htmlspecialchars(strip_tags($data->name));
    $email = htmlspecialchars(strip_tags($data->email));
    $password = password_hash($data->password, PASSWORD_DEFAULT);

    // Check if email exists
    $check_query = "SELECT id FROM users WHERE email = :email";
    $stmt = $conn->prepare($check_query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'error' => 'Email already exists']);
        exit();
    }

    $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        $user_id = $conn->lastInsertId();
        echo json_encode([
            'success' => true, 
            'user' => [
                'id' => $user_id,
                'uid' => $user_id, // For compatibility
                'name' => $name,
                'email' => $email,
                'role' => 'user'
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Registration failed']);
    }

} elseif ($action === 'login') {
    if (!isset($data->email) || !isset($data->password)) {
        echo json_encode(['success' => false, 'error' => 'Missing email or password']);
        exit();
    }

    $email = htmlspecialchars(strip_tags($data->email));
    $password = $data->password;

    $query = "SELECT id, name, email, password, role, created_at FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $row['password'])) {
            unset($row['password']); // Don't send password back
            $row['uid'] = $row['id']; // For compatibility
            echo json_encode(['success' => true, 'user' => $row]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
