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

// Adjust table + column names to match your database
$sql = "SELECT id, name, title, bio, specialties,created_at
        FROM new_therapists
        ORDER BY id DESC";


$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Query failed: " . $conn->error
    ]);
    $conn->close();
    exit();
}

$therapists = [];
while ($row = $result->fetch_assoc()) {
    $therapists[] = $row;
}

if (empty($therapists)) {
    echo json_encode([
        "success" => false,
        "message" => "No therapists found"
    ]);
} else {
    echo json_encode([
        "success" => true,
        "data" => $therapists
    ]);
}

$conn->close();
?>
