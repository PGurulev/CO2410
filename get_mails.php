<?php
declare(strict_types=1); //  strict types declaration
 
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php'; // 

$conn = db_connect();

// Get mails
$sqlMails = "SELECT id, subject, body, sender_name, sender_email, is_fake FROM mails ORDER BY id ASC";
$resultMails = $conn->query($sqlMails);//  select the mails from the mails table
if ($resultMails === false) {
	http_response_code(500);
	// json_encode() - Encodes PHP array as JSON string. JSON_UNESCAPED_UNICODE prevents Unicode character escaping
	// Reference: https://www.geeksforgeeks.org/php-json_encode-function/
	echo json_encode(['error' => 'Query failed', 'details' => $conn->error], JSON_UNESCAPED_UNICODE);
	$conn->close();
	exit;
}

// Собираем id писем
$mailIds = [];
$mails = [];
while ($row = $resultMails->fetch_assoc()) {
	$mailIds[] = (int)$row['id'];
	$mails[(int)$row['id']] = $row;
}

$recipientsByMail = [];
if (count($mailIds) > 0) {
	$idsList = implode(',', array_map('intval', $mailIds)); // list of mail ids which are in the mails table
	$sqlRecipients = "SELECT mail_id, name, email FROM mail_recipients WHERE mail_id IN ($idsList) ORDER BY id ASC";
	$resultRecipients = $conn->query($sqlRecipients);
	if ($resultRecipients === false) {
		http_response_code(500);
		// json_encode() - Transforms PHP associative array into JSON format for API response
		// Reference: https://www.geeksforgeeks.org/php-json_encode-function/
		echo json_encode(['error' => 'Recipients query failed', 'details' => $conn->error], JSON_UNESCAPED_UNICODE);
		$conn->close();
		exit;
	}
	while ($r = $resultRecipients->fetch_assoc()) {
		$mid = (int)$r['mail_id']; // mail id which is in the mail_recipients table
		if (!isset($recipientsByMail[$mid])) $recipientsByMail[$mid] = []; // if the mail id is not in the recipientsByMail array, then add it
		$recipientsByMail[$mid][] = [
			'name' => $r['name'],
			'email' => $r['email'],
		];
	}
}

$rows = [];
foreach ($mails as $mail) {
	$mid = (int)$mail['id']; // basically the same as the mail id which is in the mails table
	$recievers = $recipientsByMail[$mid] ?? []; // if the mail id is not in the recipientsByMail array, then add it
	$rows[] = [
		'id' => strval($mid),
		'content' => [
			'subject' => $mail['subject'],
			'recievers' => array_values($recievers),
			'sender' => [
				'name' => $mail['sender_name'],
				'email' => $mail['sender_email'],
			], // all this is the content of the email
		],
		'body' => $mail['body'], // which gets the body of the email
		'is_fake' => (int)$mail['is_fake'] // 0 = real, 1 = phishing
	];
}

// json_encode() - Converts PHP array containing mail data to JSON string. JSON_UNESCAPED_UNICODE maintains proper character encoding
// Reference: https://www.geeksforgeeks.org/php-json_encode-function/
echo json_encode(['mails' => $rows], JSON_UNESCAPED_UNICODE);

$conn->close();


