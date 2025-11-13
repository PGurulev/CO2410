<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

$conn = db_connect();

// Getting emails
$sqlMails = "SELECT id, subject, body, sender_name, sender_email FROM mails ORDER BY id ASC";
$resultMails = $conn->query($sqlMails);
if ($resultMails === false) {
	http_response_code(500);
	echo json_encode(['error' => 'Query failed', 'details' => $conn->error], JSON_UNESCAPED_UNICODE);
	$conn->close();
	exit;
}

// Gathering the mail's ID number 
$mailIds = [];
$mails = [];
while ($row = $resultMails->fetch_assoc()) {
	$mailIds[] = (int)$row['id'];
	$mails[(int)$row['id']] = $row;
}

$recipientsByMail = [];
if (count($mailIds) > 0) {
	$idsList = implode(',', array_map('intval', $mailIds));
	$sqlRecipients = "SELECT mail_id, name, email FROM mail_recipients WHERE mail_id IN ($idsList) ORDER BY id ASC";
	$resultRecipients = $conn->query($sqlRecipients);
	if ($resultRecipients === false) {
		http_response_code(500);
		echo json_encode(['error' => 'Recipients query failed', 'details' => $conn->error], JSON_UNESCAPED_UNICODE);
		$conn->close();
		exit;
	}
	while ($r = $resultRecipients->fetch_assoc()) {
		$mid = (int)$r['mail_id'];
		if (!isset($recipientsByMail[$mid])) $recipientsByMail[$mid] = [];
		$recipientsByMail[$mid][] = [
			'name' => $r['name'],
			'email' => $r['email'],
		];
	}
}

$rows = [];
foreach ($mails as $mail) {
	$mid = (int)$mail['id'];
	$recievers = $recipientsByMail[$mid] ?? [];
	$rows[] = [
		'id' => strval($mid),
		'content' => [
			'subject' => $mail['subject'],
			'recievers' => array_values($recievers),
			'sender' => [
				'name' => $mail['sender_name'],
				'email' => $mail['sender_email'],
			],
		],
		'body' => $mail['body'],
	];
}

echo json_encode(['mails' => $rows], JSON_UNESCAPED_UNICODE);

$conn->close();


