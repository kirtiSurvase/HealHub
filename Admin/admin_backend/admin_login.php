<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection
$host = "127.0.0.1";
$user = "root";        
$pass = "";            
$db   = "healhubnew";  
$port = 3307;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $conn->connect_error
    ]));
}

// Read JSON data from React
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

// ⚠️ Use correct column (username or email)
$stmt = $conn->prepare("SELECT * FROM admins WHERE username=?"); 
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// ✅ Compare directly (plain text, for testing only!)
if ($admin && $password === $admin['password']) {
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "token"   => bin2hex(random_bytes(16))
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}
?>
