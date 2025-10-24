<?php
header("Access-Control-Allow-Origin: *");
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

$sql = "SELECT id, name, title, bio, specialties, education, experience, approach, availability, location, price, photo_url, status, created_at 
        FROM new_therapists WHERE status = 'Pending' ORDER BY created_at DESC";

$result = $conn->query($sql);

$therapists = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $therapists[] = $row;
    }
}

if (empty($therapists)) {
    echo json_encode(["success" => false, "message" => "No pending therapists"]);
} else {
    echo json_encode(["success" => true, "data" => $therapists]);
}

$conn->close();
?>
