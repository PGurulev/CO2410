<?php
declare(strict_types=1);

require __DIR__ . '/db.php'; // requiring the db.php file

/*
 * parser to sync the data between the json files and the database
 * reads real_email.json and fake_emails.json and syncs the data with the database
 */

function parseJsonFile(string $filePath, bool $isFake): array {
	if (!file_exists($filePath)) {
		throw new RuntimeException("File not found: $filePath");
	}
	
	$jsonContent = file_get_contents($filePath);
	if ($jsonContent === false) {
		throw new RuntimeException("Can't read file: $filePath");
	}
	
	$data = json_decode($jsonContent, true);
	if ($data === null) {
		throw new RuntimeException("There's no the array 'mails' in the JSON file: $filePath. " . json_last_error_msg());
	}
	
	if (!isset($data['mails']) || !is_array($data['mails'])) {
		throw new RuntimeException("Wrong structure of the JSON file: the array 'mails' is missing");
	}
	
	foreach ($data['mails'] as &$mail) {
		$mail['isFake'] = $isFake;
	}
	
	return $data['mails'];
}

function calculateMailHash(array $mail): string {
	// creating a hash to determine the changes in the email
	$content = [
		$mail['id'] ?? '',
		$mail['isFake'] ?? false,
		$mail['content']['subject'] ?? '',
		$mail['body'] ?? '',
		$mail['content']['sender']['name'] ?? '',
		$mail['content']['sender']['email'] ?? '',
	];
 
	if (isset($mail['content']['recievers']) && is_array($mail['content']['recievers'])) {// adding the recipients to the content
		foreach ($mail['content']['recievers'] as $receiver) {
			$content[] = $receiver['name'] ?? '';
			$content[] = $receiver['email'] ?? '';
		}
	}
	
	return md5(json_encode($content));
}

function syncMailsToDatabase(mysqli $conn, array $mails): array {
	$stats = [
		'inserted' => 0,
		'updated' => 0,
		'errors' => []
	];
	
	
	$existingMails = []; // getting all the existing emails from the database
	$sql = "SELECT id, subject, body, sender_name, sender_email, is_fake FROM mails";
	$result = $conn->query($sql);
	if ($result !== false) {
		while ($row = $result->fetch_assoc()) {
			$existingMails[(int)$row['id']] = $row;
		}
	}
	
	$existingRecipients = [];// basically, the same as the existingMails array, but for the recipients
	if (count($existingMails) > 0) {
		$ids = implode(',', array_map('intval', array_keys($existingMails)));
		$sqlRecipients = "SELECT mail_id, name, email FROM mail_recipients WHERE mail_id IN ($ids)";
		$resultRecipients = $conn->query($sqlRecipients);
		if ($resultRecipients !== false) {
			while ($row = $resultRecipients->fetch_assoc()) {
				$mailId = (int)$row['mail_id'];
				if (!isset($existingRecipients[$mailId])) {
					$existingRecipients[$mailId] = [];
				}
				$existingRecipients[$mailId][] = [
					'name' => $row['name'],
					'email' => $row['email']
				];
			}
		}
	}
	
    
	$jsonMailsMap = [];
	foreach ($mails as $mail) {// mapping the emails from the json file to the database which we use later
		$mailId = isset($mail['id']) ? (int)$mail['id'] : null;// getting the id of the email
		if ($mailId !== null) {
			$jsonMailsMap[$mailId] = $mail;
		}
	}
	

	foreach ($mails as $mail) { //processing each email from the json file
		$mailId = isset($mail['id']) ? (int)$mail['id'] : null;
		$isFake = isset($mail['isFake']) ? (bool)$mail['isFake'] : false;
		
		if (!isset($mail['content']['subject']) || !isset($mail['body']) || 
		    !isset($mail['content']['sender']['name']) || !isset($mail['content']['sender']['email'])) {
			$stats['errors'][] = "Missed email with incomplete data (ID: " . ($mailId ?? 'unknown') . ")"; // displaying that the email is missing some data
			continue;
		}
		
		$subject = $conn->real_escape_string($mail['content']['subject']);
		$body = $conn->real_escape_string($mail['body']);
		$senderName = $conn->real_escape_string($mail['content']['sender']['name']);
		$senderEmail = $conn->real_escape_string($mail['content']['sender']['email']);
		$isFakeValue = $isFake ? 1 : 0;
		
		if ($mailId !== null && isset($existingMails[$mailId])) { // if the email exists in the database
			$sql = "UPDATE mails SET                    -- updating the email
					subject = '$subject',
					body = '$body',
					sender_name = '$senderName',
					sender_email = '$senderEmail',
					is_fake = $isFakeValue
					WHERE id = $mailId";
			
			if ($conn->query($sql) === true) {
				$stats['updated']++;
			} else {
				$stats['errors'][] = "Error updating email ID $mailId: " . $conn->error;
			}
			
			if (isset($mail['content']['recievers']) && is_array($mail['content']['recievers'])) {

				$deleteSql = "DELETE FROM mail_recipients WHERE mail_id = $mailId";
				$conn->query($deleteSql);
			
				foreach ($mail['content']['recievers'] as $receiver) {
					if (isset($receiver['name']) && isset($receiver['email'])) {
						$recName = $conn->real_escape_string($receiver['name']);
						$recEmail = $conn->real_escape_string($receiver['email']);
						$insertRecSql = "INSERT INTO mail_recipients (mail_id, name, email) VALUES ($mailId, '$recName', '$recEmail')";
						if ($conn->query($insertRecSql) === false) {
							$stats['errors'][] = "Error adding recipient for email ID $mailId: " . $conn->error;
						}
					}
				}
			}
		} else { // if the email doesn't exist in the database
			// insert new email
			$sql = "INSERT INTO mails (subject, body, sender_name, sender_email, is_fake) 
					VALUES ('$subject', '$body', '$senderName', '$senderEmail', $isFakeValue)";
			
			if ($conn->query($sql) === true) {
				$newMailId = $conn->insert_id;
				$stats['inserted']++;
				
				if (isset($mail['content']['recievers']) && is_array($mail['content']['recievers'])) {// if the email has recipients
					foreach ($mail['content']['recievers'] as $receiver) {
						if (isset($receiver['name']) && isset($receiver['email'])) {
							$recName = $conn->real_escape_string($receiver['name']);
							$recEmail = $conn->real_escape_string($receiver['email']);
							$insertRecSql = "INSERT INTO mail_recipients (mail_id, name, email) VALUES ($newMailId, '$recName', '$recEmail')";
							if ($conn->query($insertRecSql) === false) {
								$stats['errors'][] = "Error adding recipient for new email ID $newMailId: " . $conn->error;
							}
						}
					}
				}
			} else {
				$stats['errors'][] = "Error inserting email: " . $conn->error;
			}
		}
	}
	

	// Note: We only add and update emails, we don't delete emails that are not in JSON files
	
	return $stats;
}

// main logic 
try {
	$conn = db_connect();
	

	$baseDir = dirname(__DIR__);
	$realEmailsPath = $baseDir . '/AssetsAndExamples/JsonFiles/real_emails.json';
	$fakeEmailsPath = $baseDir . '/AssetsAndExamples/JsonFiles/phishing_emails.json';
	
// checking if the fake_emails.json file exists, if not, we use the phishing_emails.json file
	if (!file_exists($baseDir . '/AssetsAndExamples/JsonFiles/fake_emails.json')) {
		$fakeEmailsPath = $baseDir . '/AssetsAndExamples/JsonFiles/phishing_emails.json';// here the file is used as fake_emails.json to avoid the error
	} else {
		$fakeEmailsPath = $baseDir . '/AssetsAndExamples/JsonFiles/fake_emails.json';
	}
	

	$allMails = [];
	
	if (file_exists($realEmailsPath)) {
		$realMails = parseJsonFile($realEmailsPath, false);
		$allMails = array_merge($allMails, $realMails);
	} else {
		echo "Warning: File real_emails.json not found\n";
	}
	
	if (file_exists($fakeEmailsPath)) {
		$fakeMails = parseJsonFile($fakeEmailsPath, true);
		$allMails = array_merge($allMails, $fakeMails);
	} else {
		echo "Warning: File fake_emails.json/phishing_emails.json is not found\n";
	}
	
	if (empty($allMails)) {
		echo "Error: No emails found for processing\n";
		exit(1);
	}
	
	$stats = syncMailsToDatabase($conn, $allMails);
	
	echo "Added new emails: " . $stats['inserted'] . "\n";
	echo "Updated emails: " . $stats['updated'] . "\n";
	
	if (!empty($stats['errors'])) {
		echo "\nErrors:\n";
		foreach ($stats['errors'] as $error) {
			echo "- $error\n";
		}
	}
	
	$conn->close();
	
} catch (Exception $e) {
	echo "Critical error: " . $e->getMessage() . "\n";
	exit(1);
}

