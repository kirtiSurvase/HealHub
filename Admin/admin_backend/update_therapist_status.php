<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "healhubnew";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Log input for debugging
file_put_contents("debug_update.txt", print_r($input, true));

if (!$input || !isset($input['id']) || !isset($input['status'])) {
    echo json_encode(["success" => false, "message" => "Invalid data. id and status are required."]);
    exit();
}

$therapist_id = intval($input['id']);
$status = $conn->real_escape_string($input['status']);

// Check if therapist exists
$check = $conn->query("SELECT * FROM new_therapists WHERE id=$therapist_id");
if ($check->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Therapist not found with id $therapist_id"]);
    exit();
}

// Use prepared statement to update status
$stmt = $conn->prepare("UPDATE new_therapists SET status=? WHERE id=?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    exit();
}
$stmt->bind_param("si", $status, $therapist_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Therapist status updated successfully to $status."]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed: " . $stmt->error]);
}

$conn->close();
?>
