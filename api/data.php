<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'destinations':
        handleDestinations($conn, $method);
        break;
    case 'messages':
        handleMessages($conn, $method);
        break;
    case 'recommendations':
        handleRecommendations($conn, $method);
        break;
    case 'users':
        handleUsers($conn, $method);
        break;
    case 'bookings':
        handleBookings($conn, $method);
        break;
    case 'calculate_cost':
        handleCostCalculation($conn);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action ' . $action]);
        break;
}

function handleDestinations($conn, $method) {
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                 // Format metadata for compatibility
                 $result['metadata'] = [
                     'rating' => $result['rating'],
                     'type' => $result['traveler_type']
                 ];
                 // CamelCase for frontend compatibility
                 $result['imageUrl'] = $result['image_url'];
                 $result['bestTime'] = $result['best_time'];
                 $result['travelerType'] = $result['traveler_type'];
                 $result['basePrice'] = $result['base_price'];
                 echo json_encode(['success' => true, 'data' => $result]);
            } else {
                 echo json_encode(['success' => false, 'error' => 'Not found']);
            }
        } else {
            $stmt = $conn->query("SELECT * FROM destinations");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $processed = array_map(function($row) {
                 $row['metadata'] = ['rating' => $row['rating'], 'type' => $row['traveler_type']];
                 $row['imageUrl'] = $row['image_url'];
                 $row['bestTime'] = $row['best_time'];
                 $row['travelerType'] = $row['traveler_type'];
                 $row['basePrice'] = $row['base_price'];
                 return $row;
            }, $results);
            echo json_encode(['success' => true, 'data' => $processed]);
        }
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        // Admin only check should be here in real app
        
        if (isset($data->id)) {
            // Update
            $sql = "UPDATE destinations SET name=?, description=?, image_url=?, best_time=?, traveler_type=?, rating=?, base_price=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $rating = isset($data->metadata->rating) ? $data->metadata->rating : (isset($data->rating) ? $data->rating : 0);
            $stmt->execute([
                $data->name, $data->description, $data->imageUrl, $data->bestTime, $data->travelerType, $rating, isset($data->basePrice)?$data->basePrice:0, $data->id
            ]);
        } else {
            // Create
            $sql = "INSERT INTO destinations (name, description, image_url, best_time, traveler_type, rating, base_price) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $rating = isset($data->metadata->rating) ? $data->metadata->rating : (isset($data->rating) ? $data->rating : 0);
            $stmt->execute([
                $data->name, $data->description, $data->imageUrl, $data->bestTime, $data->travelerType, $rating, isset($data->basePrice)?$data->basePrice:0
            ]);
        }
        echo json_encode(['success' => true]);
    }
}

function handleMessages($conn, $method) {
    if ($method === 'GET') {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
        $stmt = $conn->prepare("SELECT * FROM messages ORDER BY created_at DESC LIMIT ?");
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format for frontend
        $processed = array_map(function($row) {
            return [
                'id' => $row['id'],
                'userId' => $row['user_id'],
                'userName' => $row['user_name'],
                'messageText' => $row['message_text'],
                'messageType' => $row['message_type'],
                'timestamp' => ['seconds' => strtotime($row['created_at'])]
            ];
        }, array_reverse($results));
        
        echo json_encode(['success' => true, 'data' => $processed]);
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        $stmt = $conn->prepare("INSERT INTO messages (user_id, user_name, message_text, message_type) VALUES (?, ?, ?, ?)");
        // Handle potentially string logic for userId 'bot'
        $uId = is_numeric($data->userId) ? $data->userId : null;
        $stmt->execute([$uId, $data->userName, $data->messageText, $data->messageType]);
        echo json_encode(['success' => true]);
    } elseif ($method === 'DELETE') {
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
}

function handleRecommendations($conn, $method) {
    if ($method === 'GET') {
        $userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
        if ($userId) {
            $stmt = $conn->prepare("SELECT * FROM recommendations WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
        } else {
            $stmt = $conn->query("SELECT * FROM recommendations ORDER BY created_at DESC");
        }
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $processed = array_map(function($row) {
            return [
                'id' => $row['id'],
                'userId' => $row['user_id'],
                'destination' => $row['destination_name'],
                'reason' => $row['reason'],
                'rating' => $row['rating'],
                'timestamp' => ['seconds' => strtotime($row['created_at'])]
            ];
        }, $results);
        
        echo json_encode(['success' => true, 'data' => $processed]);
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        $stmt = $conn->prepare("INSERT INTO recommendations (user_id, destination_name, reason, rating) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->userId, $data->destination, $data->reason, isset($data->rating)?$data->rating:0]);
        echo json_encode(['success' => true]);
    }
}

function handleUsers($conn, $method) {
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id) {
            $stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                // Compatibility mapping
                $user['uid'] = $user['id'];
                $user['createdAt'] = ['seconds' => strtotime($user['created_at'])];
                $user['preferences'] = []; // Basic implementation
                echo json_encode(['success' => true, 'data' => $user]);
            } else {
                echo json_encode(['success' => false, 'error' => 'User not found']);
            }
        } else {
            // All users (Admin)
            $stmt = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $processed = array_map(function($u) {
                $u['uid'] = $u['id'];
                $u['createdAt'] = ['seconds' => strtotime($u['created_at'])];
                return $u;
            }, $users);
            echo json_encode(['success' => true, 'data' => $processed]);
        }
    } // Edit/Delete implemented similarly if needed
}

function handleBookings($conn, $method) {
    if ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        
        // Calculate cost server-side for security
        $stmt = $conn->prepare("SELECT base_price FROM destinations WHERE id = ?");
        $stmt->execute([$data->destinationId]);
        $dest = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$dest) {
            echo json_encode(['success' => false, 'error' => 'Destination not found']);
            return;
        }
        
        $basePrice = $dest['base_price'];
        $travelers = intval($data->travelers);
        $mode = $data->travelMode;
        $hotel = isset($data->hotelType) ? $data->hotelType : 'standard';
        
        // Cost Multipliers
        // Travel Mode
        $travelMultiplier = 1.0;
        if ($mode === 'plane') $travelMultiplier = 5.0;
        elseif ($mode === 'train') $travelMultiplier = 1.5;
        
        // Hotel Type
        $hotelMultiplier = 1.0;
        if ($hotel === 'luxury') $hotelMultiplier = 2.5;
        elseif ($hotel === 'standard') $hotelMultiplier = 1.5;
        else $hotelMultiplier = 1.0; // budget
        
        // Formula: (Base * Travelers) * TravelMode + (Base * Travelers * 0.5 * HotelMultiplier)
        // Let's simplify: Base Price is per person per trip fundamental cost.
        // Let's say Base Price covers the destination activity/value.
        // We will sum separate components:
        // Transport Cost = Base * 0.2 * TravelMultiplier * Travelers
        // Stay Cost = Base * 0.5 * HotelMultiplier * Travelers
        // Activity Cost = Base * Travelers
        // Total = Base * Travelers * (1 + 0.2*TravelMult + 0.5*HotelMult)
        
        // Or simpler for this demo:
        // Total = Base * Travelers * TravelMultiplier * HotelMultiplier
        // Let's use:
        $totalMultiplier = $travelMultiplier * $hotelMultiplier;
        
        $totalCost = $basePrice * $travelers * $totalMultiplier;
        
        $sql = "INSERT INTO bookings (user_id, destination_id, travel_date, travelers, travel_mode, hotel_type, total_cost, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$data->userId, $data->destinationId, $data->travelDate, $travelers, $mode, $hotel, $totalCost])) {
             echo json_encode(['success' => true, 'totalCost' => $totalCost, 'bookingId' => $conn->lastInsertId()]);
        } else {
             echo json_encode(['success' => false, 'error' => 'Booking failed']);
        }
    }
}

function handleCostCalculation($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        
        $price = 0;
        
        // Get price by ID or Name
        if (isset($data->destinationId) && $data->destinationId) {
            $stmt = $conn->prepare("SELECT base_price FROM destinations WHERE id = ?");
            $stmt->execute([$data->destinationId]);
            $dest = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dest) $price = $dest['base_price'];
        } elseif (isset($data->destinationName)) {
            $stmt = $conn->prepare("SELECT base_price FROM destinations WHERE name LIKE ? LIMIT 1");
            $stmt->execute(["%" . $data->destinationName . "%"]);
            $dest = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dest) $price = $dest['base_price'];
            else $price = 100; // Fallback
        }
        
        $travelers = intval($data->travelers);
        $mode = $data->travelMode;
        $hotel = isset($data->hotelType) ? $data->hotelType : 'standard';
        
        $travelMultiplier = 1.0;
        if ($mode === 'plane') $travelMultiplier = 5.0;
        elseif ($mode === 'train') $travelMultiplier = 1.5;
        
        $hotelMultiplier = 1.0;
        if ($hotel === 'luxury') $hotelMultiplier = 2.5;
        elseif ($hotel === 'standard') $hotelMultiplier = 1.5;
        
        $totalMultiplier = $travelMultiplier * $hotelMultiplier;
        
        $estimatedCost = $price * $travelers * $totalMultiplier;
        
        echo json_encode(['success' => true, 'estimatedCost' => $estimatedCost, 'basePrice' => $price]);
    }
}
?>
