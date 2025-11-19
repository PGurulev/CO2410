<?php
//Set the response type to JSON
header("Content-Type: application/json");

//Connect to the database
require_once "db_connect.php";

//Array that will store all departments with their employees
$departments = [];

//Query all departments from the database
$deptQuery = $conn->query("SELECT * FROM departments");

//Loop through each department
while ($dept = $deptQuery->fetch_assoc()) {

    //Query employees that belong to the current department
    $employeeQuery = $conn->query("
        SELECT * FROM employees
        WHERE department_id = {$dept['id']}
    ");

    //Array to store employees for this department
    $employees = [];

    //Loop through all employees in this department
    while ($emp = $employeeQuery->fetch_assoc()) {

        //Add each employee's data to the employees array
        $employees[] = [
            "name" => $emp["name"],
            "password" => $emp["password"],
            "email" => $emp["email"],
            "position" => $emp["position"],
            "department" => $dept["name"], //The department name from departments table
            "manager" => $emp["manager_email"] ?? "none" //Use "none" if manager_email is NULL
        ];
    }

    //Add the department and its employees to the main array
    $departments[] = [
        "name" => $dept["name"],
        "employees" => $employees
    ];
}
