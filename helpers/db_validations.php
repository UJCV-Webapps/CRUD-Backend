<?php
require('database/connection.php');

function existEmail($email)
{
    global $connection;

    $stmt = $connection->prepare("SELECT * FROM employees WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_array();

    return (isset($row)) ? true : false;
}
