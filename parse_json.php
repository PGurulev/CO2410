<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

$conn = db_connect();



$corporateFile = null;
foreach ($possiblePaths as $path) {
	if (file_exists($path)) {
		$corporateFile = $path;
		break;
	}
}

// json_decode() - Converts JSON string to PHP array/object. Second parameter (true) returns associative array instead of object
// Reference: https://www.geeksforgeeks.org/php-json_decode-function/
$corporateData = json_decode(file_get_contents($corporateFile), true);
if (!$corporateData) {
	die("Error: Failed to parse corporate_structure.json\n");
}

echo "Importing departments and employees...\n";

// Import departments
$departmentsMap = []; // Map department name to ID
foreach ($corporateData['company']['departments'] as $dept) {
	$deptName = $conn->real_escape_string($dept['name']);
	$sql = "INSERT INTO departments (name) VALUES ('$deptName') ON DUPLICATE KEY UPDATE name = VALUES(name)";
	if ($conn->query($sql)) {
		$deptId = $conn->insert_id;
		if ($deptId == 0) {
			// Get existing ID
			$result = $conn->query("SELECT id FROM departments WHERE name = '$deptName' LIMIT 1");
			if ($result && $row = $result->fetch_assoc()) {
				$deptId = (int)$row['id'];
			}
		}
		$departmentsMap[$dept['name']] = $deptId;
		echo "  Department: {$dept['name']} (ID: $deptId)\n";
	} else {
		echo "  Error inserting department {$dept['name']}: {$conn->error}\n";
	}
}

// Import employees (first pass: employees without managers, second pass: with managers)
$employeesToInsert = [];
foreach ($corporateData['company']['employees'] as $emp) {
	$employeesToInsert[] = $emp;
}

// Sort: employees with manager=null first, then others
usort($employeesToInsert, function($a, $b) {
	if ($a['manager'] === null && $b['manager'] !== null) return -1;
	if ($a['manager'] !== null && $b['manager'] === null) return 1;
	return 0;
});

foreach ($employeesToInsert as $emp) {
	if (!isset($departmentsMap[$emp['department']])) {
		echo "  Warning: Department '{$emp['department']}' not found for employee {$emp['name']}\n";
		continue;
	}
	
	$deptId = $departmentsMap[$emp['department']];
	$name = $conn->real_escape_string($emp['name']);
	$password = $conn->real_escape_string($emp['password']);
	$email = $conn->real_escape_string($emp['email']);
	$position = $conn->real_escape_string($emp['position']);
	$manager = $emp['manager'] !== null ? "'" . $conn->real_escape_string($emp['manager']) . "'" : 'NULL';
	
	$sql = "INSERT INTO employees (name, password, email, position, department_id, manager_email) 
			VALUES ('$name', '$password', '$email', '$position', $deptId, $manager)
			ON DUPLICATE KEY UPDATE name = VALUES(name), password = VALUES(password), position = VALUES(position), department_id = VALUES(department_id), manager_email = VALUES(manager_email)";
	
	if ($conn->query($sql)) {
		echo "  Employee: {$emp['name']} ({$emp['email']})\n";
	} else {
		echo "  Error inserting employee {$emp['name']}: {$conn->error}\n";
	}
}

echo "\n";

// 2. Import mails from real_emails.json and phishing_emails.json
$basePath = dirname($corporateFile);
$mailFiles = [
	$basePath . '/real_emails.json',
	$basePath . '/phishing_emails.json'
];

foreach ($mailFiles as $mailFile) {
	if (!file_exists($mailFile)) {
		echo "Warning: File not found: $mailFile\n";
		continue;
	}
	
	// json_decode() - Parses JSON string from file into PHP associative array (true = associative array, false = object)
	// Reference: https://www.geeksforgeeks.org/php-json_decode-function/
	$mailData = json_decode(file_get_contents($mailFile), true);
	if (!$mailData || !isset($mailData['mails'])) {
		echo "Warning: Invalid JSON or no 'mails' key in: $mailFile\n";
		continue;
	}
	
	echo "Importing mails from: " . basename($mailFile) . "\n";
	
	foreach ($mailData['mails'] as $mail) {
		$subject = $conn->real_escape_string($mail['content']['subject']);
		$body = $conn->real_escape_string($mail['body']);
		$senderName = $conn->real_escape_string($mail['content']['sender']['name']);
		$senderEmail = $conn->real_escape_string($mail['content']['sender']['email']);
		
		// Insert mail
		$sql = "INSERT INTO mails (subject, body, sender_name, sender_email) 
				VALUES ('$subject', '$body', '$senderName', '$senderEmail')";
		
		if ($conn->query($sql)) {
			$mailId = $conn->insert_id;
			echo "  Mail: $subject (ID: $mailId)\n";
			
			// Insert recipients
			if (isset($mail['content']['recievers']) && is_array($mail['content']['recievers'])) {
				foreach ($mail['content']['recievers'] as $recipient) {
					$recName = $conn->real_escape_string($recipient['name']);
					$recEmail = $conn->real_escape_string($recipient['email']);
					
					$sqlRec = "INSERT INTO mail_recipients (mail_id, name, email) 
							   VALUES ($mailId, '$recName', '$recEmail')";
					
					if (!$conn->query($sqlRec)) {
						echo "    Error inserting recipient: {$conn->error}\n";
					}
				}
			}
		} else {
			echo "  Error inserting mail: {$conn->error}\n";
		}
	}
	
	echo "\n";
}

echo "Import completed!\n";
$conn->close();

