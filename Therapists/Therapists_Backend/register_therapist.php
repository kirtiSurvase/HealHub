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
    echo json_encode(["success" => false, "message" => "DB Connection Failed"]);
    exit();
}

// Handle file upload (photo)
$photo_url = "";
if (!empty($_FILES['photo']['name'])) {
    $targetDir = "uploads/therapists/";
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
    $targetFile = $targetDir . time() . "_" . basename($_FILES["photo"]["name"]);
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
        $photo_url = $targetFile;
    }
}

// Collect form data
$name        = $_POST['name'];
$email       = $_POST['email'];
$password = $_POST['password'];

$title       = $_POST['title'];
$bio         = $_POST['bio'];
$specialties = $_POST['specialties'];
$education   = $_POST['education'];
$experience  = $_POST['experience'];
$approach    = $_POST['approach'];
$availability= $_POST['availability'];
$location    = $_POST['location'];
$price       = $_POST['price'];

// Insert into DB with Pending status
$sql = "INSERT INTO new_therapists 
(name, email, password, title, bio, specialties, education, experience, approach, availability, location, price, photo_url, status, created_at) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssssssssss", 
    $name, $email, $password, $title, $bio, $specialties, 
    $education, $experience, $approach, $availability, $location, $price, $photo_url
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registration submitted. Waiting for admin approval."]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}

$conn->close();
?>
