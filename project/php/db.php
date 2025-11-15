<?php
declare(strict_types=1); //  strict types declaration

function db_connect(): mysqli {
	$host = 'localhost';
	$user = 'root';
	$pass = '';
	$db   = 'cyber_game';

	$mysqli = new mysqli($host, $user, $pass, $db);
	if ($mysqli->connect_errno) {
		http_response_code(500);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['error' => 'DB connection failed', 'details' => $mysqli->connect_error], JSON_UNESCAPED_UNICODE);
		exit;
	}
	$mysqli->set_charset('utf8mb4');
	return $mysqli;
}


