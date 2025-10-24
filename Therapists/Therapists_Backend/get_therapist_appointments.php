<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$host="127.0.0.1";
$user="root";
$pass="";
$db="healhubnew";
$port=3307;

$conn = new mysqli($host,$user,$pass,$db,$port);
if ($conn->connect_error) {
    die(json_encode(['success'=>false,'message'=>'DB Connection failed']));
}

$therapist_id = intval($_GET['therapist_id'] ?? 0);
if (!$therapist_id) {
    echo json_encode(['success'=>false,'message'=>'therapist_id required']); 
    exit;
}

$sql = "SELECT a.id, a.appointment_date, a.appointment_time, a.issue, a.status,
               u.id AS customer_id, u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        WHERE a.therapist_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $therapist_id);
$stmt->execute();
$res = $stmt->get_result();
$appointments = [];
while ($row = $res->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode(['success'=>true,'data'=>$appointments]);
$stmt->close();
$conn->close();
?>