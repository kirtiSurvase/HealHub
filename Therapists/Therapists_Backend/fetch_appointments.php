<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "healhubnew";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed"]);
    exit();
}

// Get therapist ID from frontend
$data = json_decode(file_get_contents("php://input"), true);
$therapist_id = $data["therapist_id"] ?? "";

if (empty($therapist_id)) {
    echo json_encode(["success" => false, "message" => "Therapist ID missing"]);
    exit();
}

// Fetch only that therapist's bookings
$sql = "SELECT booking_id, user_id, therapist_id, booking_date, status, concerns, session_type
        FROM bookings
        WHERE therapist_id = ?
        ORDER BY booking_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $therapist_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

if (empty($bookings)) {
    echo json_encode(["success" => false, "message" => "No bookings found"]);
} else {
    echo json_encode(["success" => true, "data" => $bookings]);
}

$conn->close();
?>
