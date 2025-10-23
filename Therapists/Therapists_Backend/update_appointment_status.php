<?php
// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    exit(0);
}

// CORS headers for main request
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// DB connection
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "healhubnew";
$port=3307;

$conn = new mysqli($host,$user,$pass,$db,$port);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get request data
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? 0;
$status = $data['status'] ?? '';

if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

// Update appointment
$stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update']);
}

$stmt->close();
$conn->close();
?>