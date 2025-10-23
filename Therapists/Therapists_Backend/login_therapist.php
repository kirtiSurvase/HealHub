<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

// DB connection
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "healhubnew";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB Connection Failed"]);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$email    = $data['email'] ?? '';
$password = $data['password'] ?? '';

if(empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Email and password are required"]);
    exit();
}

// Check therapist credentials and status
$sql = "SELECT id, name, email, title, status FROM new_therapists WHERE email=? AND password=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $password); // ⚠️ Use hashed passwords in production
$stmt->execute();
$result = $stmt->get_result();

if($therapist = $result->fetch_assoc()) {
    if($therapist['status'] === 'Approved') {
        echo json_encode([
            "success" => true, 
            "message" => "Login successful",
            "therapist" => $therapist
        ]);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "Your account is still pending approval by admin"
        ]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid email or password"]);
}

$conn->close();
?>
