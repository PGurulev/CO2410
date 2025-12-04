<?php
header("Content-Type: application/json");
require_once "db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

$name  = $data["name"] ?? null;
$score = $data["score"] ?? null;

if (!$name || !$score) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO leaderboard (name, score) VALUES (?, ?)");
$stmt->bind_param("si", $name, $score);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Score saved"]);
} 
else {
    echo json_encode(["success" => false, "message" => "Database error"]);
}

$stmt->close();
$conn->close();