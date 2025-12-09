<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

$conn = db_connect();

//Import departments and employees from corporate_structure.json

$possiblePaths = [//Try multiple possible paths
	'/home/tklochko-shemiakin/public_html/CO2410-main/AssetsAndExamples/JsonFiles/corporate_structure.json',
	__DIR__ . '/../../AssetsAndExamples/JsonFiles/corporate_structure.json',
	__DIR__ . '/../AssetsAndExamples/JsonFiles/corporate_structure.json',
	__DIR__ . '/../../../AssetsAndExamples/JsonFiles/corporate_structure.json'
];

$corporateFile = null; 
foreach ($possiblePaths as $path) { // the possible paths to the corporate file
	if (file_exists($path)) {
		$corporateFile = $path; // setting the corporate file path to the first existing path(corporateFile)
		break;
	}
}

if (!$corporateFile) {
	die("Error: corporate_structure.json not found. Tried paths:\n" . implode("\n", $possiblePaths) . "\n"); //die method is to stop
}

// json_decode() - Converts JSON string to PHP array/object. Second parameter (true) returns associative array instead of object
// Reference: https://www.geeksforgeeks.org/php-json_decode-function/
$corporateData = json_decode(file_get_contents($corporateFile), true);
if (!$corporateData) {
	die("Error: Failed to parse corporate_structure.json\n"); //stops the whole script
}

echo "Importing departments and employees...\n";

//Import departments
$departmentsMap = []; //map department name to ID
foreach ($corporateData['company']['departments'] as $dept) {
	$deptName = $conn->real_escape_string($dept['name']);
	$sql = "INSERT INTO departments (name) VALUES ('$deptName') ON DUPLICATE KEY UPDATE name = VALUES(name)";
	if ($conn->query($sql)) {
		$deptId = $conn->insert_id;
		if ($deptId == 0) {
			//get existing ID
			$result = $conn->query("SELECT id FROM departments WHERE name = '$deptName' LIMIT 1");
			if ($result && $row = $result->fetch_assoc()) {
				$deptId = (int)$row['id'];
			}
		}
		$departmentsMap[$dept['name']] = $deptId;
		echo "  Department: {$dept['name']} (ID: $deptId)\n";
	} else 
	{
		echo "  Error inserting department {$dept['name']}: {$conn->error}\n";
	}
}

//import employees (first pass: employees without managers, second pass: with managers)
$employeesToInsert = [];
$Emails = [];

foreach ($corporateData['company']['employees'] as $emp) {
	// Check for duplicate emails in JSON file itself
	if (isset($seenEmails[$emp['email']])) {
		echo "  Warning: Duplicate email in JSON file: {$emp['email']} (skipping duplicate)\n";
		continue;
	}
	$seenEmails[$emp['email']] = true;
	$employeesToInsert[] = $emp;
}

//Sort: employees with manager=null first, then others
usort($employeesToInsert, function($a, $b)  { //sorting the employees with manager=null first, then others
	if ($a['manager'] === null && $b['manager'] !== null) return -1;
	if ($a['manager'] !== null && $b['manager'] === null) return 1;
	return 0; //if the manager is null, return -1, if the manager is not null then return 1, if the manager is the same- 0
});

foreach ($employeesToInsert as $emp) { //foreach loop to insert the employees
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
	
	// Check if employee already exists by email before inserting
	$checkSql = "SELECT id FROM employees WHERE email = '$email' LIMIT 1";
	$checkResult = $conn->query($checkSql);
	$employeeExists = $checkResult && $checkResult->num_rows > 0;
	
	if ($employeeExists) {
		//Update existing employee
		$updateSql = "UPDATE employees SET --this query makes update the employee with the new data
			name = '$name', 
			password = '$password', 
			position = '$position', 
			department_id = $deptId, 
			manager_email = $manager 
			WHERE email = '$email'"; 
		
		if ($conn->query($updateSql)) {
			echo " Employee updated: {$emp['name']} ({$emp['email']})\n";
		} else {
			echo "Error updating employee {$emp['name']}: {$conn->error}\n";
		}
	} else {
		// Insert new employee
		$sql = "INSERT INTO employees (name, password, email, position, department_id, manager_email) 
				VALUES ('$name', '$password', '$email', '$position', $deptId, $manager)";
		
		if ($conn->query($sql)) {
			echo "  Employee: {$emp['name']} ({$emp['email']})\n";
		} else {
			echo "  Error inserting employee {$emp['name']}: {$conn->error}\n";
		}
	}
}

echo "\n";

//Import mails from real_emails.json and phishing_emails.json
$basePath = dirname($corporateFile);// getting the base path of the corporate file
$mailFiles = [
	['file' => $basePath . '/real_emails.json', 'is_fake' => false], // false = real email
	['file' => $basePath . '/phishing_emails.json', 'is_fake' => true] // true = phishing email
];

foreach ($mailFiles as $mailFileInfo) {
	$mailFile = $mailFileInfo['file'];
	$isFake = $mailFileInfo['is_fake'];
	
	if (!file_exists($mailFile)) {
		echo "Warning: File not found: $mailFile\n";
		continue;
	}
	
	//json_decode() - Parses JSON string from file into PHP associative array (true = associative array, false = object)
	//Reference: https://www.geeksforgeeks.org/php-json_decode-function/
	$mailData = json_decode(file_get_contents($mailFile), true);
	if (!$mailData || !isset($mailData['mails'])) {
		echo "Warning: Invalid JSON or no 'mails' key in: $mailFile\n";
		continue;
	}
	
	$mailType = $isFake ? 'PHISHING' : 'REAL'; // true = PHISHING, false = REAL
	echo "Importing $mailType mails from: " . basename($mailFile) . "\n";
	
	foreach ($mailData['mails'] as $mail) {
		$subject = $conn->real_escape_string($mail['content']['subject']);
		$body = $conn->real_escape_string($mail['body']);
		$senderName = $conn->real_escape_string($mail['content']['sender']['name']);
		$senderEmail = $conn->real_escape_string($mail['content']['sender']['email']);
		
		//Check if mail already exists to avoid duplicates and get existing ID
		$checkSql = "SELECT id FROM mails WHERE subject = '$subject' AND sender_email = '$senderEmail' LIMIT 1";
		$checkResult = $conn->query($checkSql);
		$mailId = null;
		
		//If query executed successfully and found at least one row, mail already exists in database
		if ($checkResult && $checkResult->num_rows > 0) {
			//the mail already exists, get its ID and update is_fake flag
			$existingRow = $checkResult->fetch_assoc();
			$mailId = (int)$existingRow['id'];
			//update is_fake flag in case it was wrong, also update body and sender_name in case they changed
			//Convert boolean to integer for MySQL: true = 1, false = 0
			$isFakeInt = $isFake ? 1 : 0;
			$updateSql = "UPDATE mails SET is_fake = $isFakeInt, body = '$body', sender_name = '$senderName' WHERE id = $mailId";
			$conn->query($updateSql);
			echo "  Mail exists (updated): $subject (ID: $mailId)\n";
		} 
		else
		 {
			//insert new mail with is_fake flag to distinguish real emails (false) from phishing emails (true)
			//Convert boolean to integer for MySQL: true = 1, false = 0
			$isFakeInt = $isFake ? 1 : 0;
			$sql = "INSERT INTO mails (subject, body, sender_name, sender_email, is_fake) 
					VALUES ('$subject', '$body', '$senderName', '$senderEmail', $isFakeInt)";
			
			//if INSERT query executed then get the auto-generated ID
			if($conn->query($sql)) 
			{
				$mailId = $conn->insert_id;
				echo "  Mail: $subject (ID: $mailId)\n";
			}
			 else 
			{
				// If INSERT failed, output error message and skip to next mail (don't process recipients)
				echo "  Error inserting mail: {$conn->error}\n";
				continue; // Skip recipients if mail insertion failed
			}
		}
		
		//Insert recipients (delete old ones first if mail existed, then insert fresh)
		//Only process recipients if mailId was successfully gotten
		if ($mailId !== null) {
			//Delete existing recipients for this mail to avoid duplicates 
			$conn->query("DELETE FROM mail_recipients WHERE mail_id = $mailId");
			

			//Checks if recievers array exists in mail data and is actually an array before processing
			if (isset($mail['content']['recievers']) && is_array($mail['content']['recievers'])) {
				foreach ($mail['content']['recievers'] as $recipient) {
					$recName = $conn->real_escape_string($recipient['name']);
					$recEmail = $conn->real_escape_string($recipient['email']);
					
					$sqlRec = "INSERT INTO mail_recipients (mail_id, name, email) 
							   VALUES ($mailId, '$recName', '$recEmail')";
					
					//if its failed, output error but continue with other recipients
					if (!$conn->query($sqlRec)) {
						echo "Error inserting recipient: {$conn->error}\n";
					}
				}
			}
		}
	}
	
	echo "\n";
}

echo "Import completed!\n";
$conn->close();

