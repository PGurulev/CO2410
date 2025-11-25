header("Content-Type: application/json");

//Include database connection
require_once "db_connect.php";

//Query all players from leaderboard table
$sql = "SELECT name, score FROM leaderboard ORDER BY score DESC";
$result = $conn->query($sql);

$players = [];

//Fetch each row and add to the array
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $players[] = [
            "name"  => $row["name"],
            "score" => (int)$row["score"]
        ];
    }
}

//Output JSON
echo json_encode(["players" => $players], JSON_PRETTY_PRINT);

$conn->close();