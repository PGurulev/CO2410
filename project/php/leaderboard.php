<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php'; // 

$conn = db_connect();
$sql = "SELECT name, score, timestamp FROM leaderboard ORDER BY score DESC LIMIT 15";
$result = $conn->query($sql);

$players = [];

if ($result && $result->num_rows > 0) { //If result is not null and values more than 0
    while ($row = $result->fetch_assoc()) {
        //Add values for leaderboard table which contain information about player's data 
        $players[] = [
            "name"  => $row["name"],
            "score" => (int)$row["score"],
            "timestamp"=> $row["timestamp"]
        ];
    }
}

echo json_encode(["players" => $players], JSON_PRETTY_PRINT); //For display JSON data more neatly

$conn->close();
?>