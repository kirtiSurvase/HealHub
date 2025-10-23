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
    echo json_encode(["success" => false, "message" => $conn->connect_error]);
    exit();
}

$sql = "SELECT therapist_id, name, title, bio, specialties, rating, photo_url, location, price
        FROM new_therapists
        WHERE status='approved'
        ORDER BY therapist_id DESC";

$result = $conn->query($sql);
$therapists = [];

$baseUrl = "http://localhost/HEALHUB2/Therapists/Therapists_Backend/";

while ($row = $result->fetch_assoc()) {
    // ✅ Full image URL
    if (!empty($row['photo_url'])) {
        // Check if the path already contains 'uploads/therapists'
        if (strpos($row['photo_url'], 'uploads/therapists') === false) {
            $row['photo_url'] = $baseUrl . "uploads/therapists/Therapists_Backend/uploads/" . $row['photo_url'];
        } else {
            $row['photo_url'] = $baseUrl . $row['photo_url'];
        }
    } else {
        $row['photo_url'] = ""; // empty if no image
    }

    // ✅ Convert comma-separated specialties to array
    $row['specializations'] = !empty($row['specialties'])
        ? explode(',', $row['specialties'])
        : [];

    $therapists[] = $row;
}

echo json_encode(["success" => true, "data" => $therapists]);

$conn->close();
?>
