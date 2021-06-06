<?php

//Obtener los jobs para rellenar select en el frontend

require('database/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    http_response_code(200);
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM jobs");

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        //Convertir en un JSON con farmato, adaptar similar para los empleados al momento de consultarlos
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $response[] = $row;
        }
    }

    $stmt->close();
    $connection->close();

    echo json_encode($response);
}
