<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tklochko-shemiakin"; //Database connection

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>