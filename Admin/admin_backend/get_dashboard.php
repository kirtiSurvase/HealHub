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
    echo json_encode([
        "success" => false,
        "message" => "Connection failed: " . $conn->connect_error
    ]);
    exit();
}

// Fetch counts
$userResult = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$therapistResult = $conn->query("SELECT COUNT(*) AS total_therapists FROM therapists");
 $appointmentResult = $conn->query("SELECT COUNT(*) AS total_appointments FROM bookings");

$users = $userResult->fetch_assoc();
$therapists = $therapistResult->fetch_assoc();
 $appointments = $appointmentResult->fetch_assoc();

// Return JSON
echo json_encode([
    "success" => true,
    "data" => [
        "total_users" => $users['total_users'],
        "total_therapists" => $therapists['total_therapists'],
         "total_appointments" => $appointments['total_appointments']
    ]
]);

$conn->close();
?>
