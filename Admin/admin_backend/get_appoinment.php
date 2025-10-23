<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "healhubnew";  // your DB

$port = 3307;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Connection failed: " . $conn->connect_error
    ]);
    exit();
}

// Adjust table + column names to match your DB
$sql = "SELECT booking_id, user_id, therapist_id, booking_date, status ,concerns,session_type
        FROM bookings 
        ORDER BY booking_id DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Query failed: " . $conn->error
    ]);
    $conn->close();
    exit();
}

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

if (empty($bookings)) {
    echo json_encode([
        "success" => false,
        "message" => "No bookings found"
    ]);
} else {
    echo json_encode([
        "success" => true,
        "data" => $bookings
    ]);
}

$conn->close();
?>
