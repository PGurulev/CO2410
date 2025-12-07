<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

$conn = db_connect();


// Get all departments with employees through JOIN
// ORDER BY is used to sort the results by the department name and the employee name
$sql = "SELECT 
    d.id AS department_id,
    d.name AS department_name,
    e.id AS employee_id,
    e.name AS employee_name,
    e.password,
    e.email AS employee_email,
    e.position,
    e.manager_email
FROM departments d
LEFT JOIN employees e ON d.id = e.department_id
ORDER BY d.name ASC, e.name ASC";

$result = $conn->query($sql);
if ($result === false) {
	http_response_code(500);
	// json_encode() - Converts PHP array to JSON string. JSON_UNESCAPED_UNICODE preserves Unicode characters without escaping
	// Reference: https://www.geeksforgeeks.org/php-json_encode-function/
	echo json_encode(['error' => 'Query failed', 'details' => $conn->error], JSON_UNESCAPED_UNICODE);
	$conn->close();
	exit;
}

// Group employees by departments
$departments = [];
while ($row = $result->fetch_assoc()) {
	$deptName = $row['department_name'];
	
	// Initialize department if it doesn't exist
	if (!isset($departments[$deptName])) {
		$departments[$deptName] = [
			'name' => $deptName,
			'employees' => []
		];
	}
	
	// Add employee if it exists (LEFT JOIN may return NULL)
	if ($row['employee_id'] !== null) {
		$manager = $row['manager_email'] ?? null;
		$departments[$deptName]['employees'][] = [
			'name' => $row['employee_name'],
			'password' => $row['password'],
			'email' => $row['employee_email'],
			'position' => $row['position'],
			'department' => $deptName,
			'manager' => $manager !== null ? $manager : 'none'
		];
	}
}

// Convert the array to indexed array
$departmentsArray = array_values($departments);

// json_encode() - Converts PHP array to JSON string format. JSON_UNESCAPED_UNICODE flag ensures proper Unicode character encoding
// Reference: https://www.geeksforgeeks.org/php-json_encode-function/
echo json_encode(['departments' => $departmentsArray], JSON_UNESCAPED_UNICODE);

$conn->close();

