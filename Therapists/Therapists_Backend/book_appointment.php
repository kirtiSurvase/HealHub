<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "healhubnew";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die(json_encode(['success'=>false,'message'=>'DB Connection failed']));
}

$data = json_decode(file_get_contents("php://input"), true);

$therapist_id = intval($data['therapist_id'] ?? 0);
$user_id = intval($data['user_id'] ?? 0);
$date = $data['appointment_date'] ?? '';
$time = $data['appointment_time'] ?? '';
$issue = $data['issue'] ?? '';

if (!$therapist_id || !$user_id || !$date || !$time) {
    echo json_encode(['success'=>false,'message'=>'Missing required fields']);
    exit;
}

$sql = "INSERT INTO appointments 
        (therapist_id, user_id, appointment_date, appointment_time, issue, status) 
        VALUES (?, ?, ?, ?, ?, 'Pending')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $therapist_id, $user_id, $date, $time, $issue);

if ($stmt->execute()) {
    echo json_encode(['success'=>true,'message'=>'Appointment booked successfully']);
} else {
    echo json_encode(['success'=>false,'message'=>'Booking failed: '.$stmt->error]);
}

$stmt->close();
$conn->close();
?>
