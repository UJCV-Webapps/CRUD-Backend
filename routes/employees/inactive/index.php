<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require('../../../database/connection.php');
require('../../../helpers/utils.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    global $connection;

    $query = "SELECT e.employee_id, e.first_name, e.last_name, e.email, e.phone_number, e.profile, e.hire_date, e.salary, j.job_id, j.job_title
             FROM employees AS e
             JOIN jobs AS j on e.job_id=j.job_id
             WHERE active=0";

    $stmt = $connection->prepare($query);
    if ($stmt->execute()) {
        $response = fromArrToJSON($stmt->get_result());
        echo json_encode($response);
    }
}
