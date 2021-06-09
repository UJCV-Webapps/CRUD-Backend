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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    global $connection;
    if (!isset($_POST['employee_id'])) {
        http_response_code(400);
        $response['error'] = "Se necesita especificar el ID del usuario.";
        echo json_encode($response);
        return;
    }

    $employee_id = $_POST['employee_id'];
    $active = 1;

    $query = "UPDATE employees SET active=? WHERE employee_id=?";

    $stmt = $connection->prepare($query);
    $stmt->bind_param('ii', $active, $employee_id);
    if ($stmt->execute()) {
        $response["msg"] = "Empleado activado correctamente.";
        echo json_encode($response);
    }
}
