<?php

require('./database/connection.php');

// Aqui iran las funciones que se ejecutaran dependiendo de la petición

//Se debe obtener unicamente un empleado, puede ser por id o email, el empleado debe de estar activo
//Debe de retornar el trabajo 'JOIN jobs'
//METHOD: GET
function getEmployee($query)
{
    $response = array();
    $response['query'] = $query;
    return $response;
};

//Se deben retornar todos los empleados activos con su respectivo JOIN y en base a la paginación
//El resultado devuelto por pagina debe ser 10
//METHOD: GET
function getEmployees($page = 1)
{
    $response = array();
    $response['msg'] = "GET EMPLOYEES " . $page;
    return $response;
};


//Debe registrar un empleado en el sistema, hacer todas las validaciones posibles antes de impactar la BD
//METHOD: POST
//Angel
function saveEmployee($form_data)
{
    $response = array();
    $response['msg'] = "Empleado guardado correctamente";
    return $response;
}

//Se deben actualizar los datos del empleado en base a su ID, validar los datos antes de realizar la actualización
//METHOD: PUT
function updateEmployee()
{
}


//Debe 'eliminar un usuario' pero en realidad debe pasar su estado activo=false
//METHOD: DELETE
function deleteEmployee()
{
};
