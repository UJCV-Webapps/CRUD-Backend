<?php

require('./database/connection.php');
require('./helpers/utils.php');
require('./helpers/db_validations.php');

// Aqui iran las funciones que se ejecutaran dependiendo de la petición

//Se debe obtener unicamente un empleado, puede ser por id o email, el empleado debe de estar activo
//Debe de retornar el trabajo 'JOIN jobs'
//METHOD: GET
function getEmployee($param)
{
    $response = array();
    global $connection;
    //Comprobar si es un email
    $pattern = "/^[\w\-\.]+@([\w-]+\.)+[\w-]{2,4}$/";

    if (preg_match_all($pattern, $param) == 1) {
        //Declarando consulta
        $query = "SELECT e.employee_id, e.first_name, e.last_name, e.email, e.phone_number, e.salary, e.hire_date, e.profile, j.job_title FROM employees AS e
        JOIN jobs AS j
        ON e.job_id = j.job_id
        WHERE e.email=? && active=1";

        //Es un correo, hacemos la consulta mediante email
        $stmt = $connection->prepare($query);
        $stmt->bind_param('s', $email);
        $email = $param;

        if ($stmt->execute()) {
            $row = $stmt->get_result();
            $response = fromArrToJSON($row);
        }
        return $response;
    } else {
        if (!is_numeric($param)) {
            http_response_code(400);
            $response['error'] = "El ID debe de ser numerico";
            return $response;
        }

        $query = "SELECT e.employee_id, e.first_name, e.last_name, e.email, e.phone_number, e.salary, e.hire_date, e.profile, j.job_title FROM employees AS e
        JOIN jobs AS j
        ON e.job_id = j.job_id
        WHERE e.employee_id=? && active=1";

        $stmt = $connection->prepare($query);
        $stmt->bind_param('i', $employee_id);
        $employee_id = $param;

        if ($stmt->execute()) {
            $row = $stmt->get_result();
            $response = fromArrToJSON($row);
            return $response;
        }
    }
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
    global $connection;
    $path = 'assets/profiles/';
    $name = "";

    //Pattern para verificar la estructura del email
    $pattern = "/^[\w\-\.]+@([\w-]+\.)+[\w-]{2,4}$/";

    //Validando datos del frontend
    if (!isset($form_data['first_name'])) {
        $response["error"] = "El nombre es obligatorio para poder registrar al empleado.";
        return $response;
    } elseif (!isset($form_data['last_name'])) {
        $response["error"] = "El apellido es obligatorio para poder registrar al empleado.";
        return $response;
    } elseif (!isset($form_data['email'])) {
        $response["error"] = "El correo electrónico es obligatorio para poder registrar al empleado.";
        return $response;
    } elseif (!isset($form_data['phone_number'])) {
        $response["error"] = "Se requiere el numero de telefono para registrar el empleado.";
        return $response;
    } elseif (!isset($form_data['salary']) || !is_numeric($form_data['salary'])) {
        $response["error"] = "El salario es obligatorio y debe de ser numerico.";
        return $response;
    } elseif (!isset($form_data['job_id'])) {
        $response["error"] = "Porfavor especifique el role a desempeñar dentro de la organización.";
        return $response;
    }

    if (preg_match_all($pattern, $form_data['email']) == 0) {
        $response["error"] = "Formato de correo electrónico no valido.";
        return $response;
    }

    //Funcion dedicada a verificar si el email ya se encuentra registrado en la base de datos
    if (existEmail($form_data['email'])) {
        $response['error'] = "La dirección de correo electrónico ya se encuentra registrada en el sistema.";
        return $response;
    }


    //Verificamos si el directorio existe de no ser asi lo creamos
    createDirectoryIfNotExist($path);

    //La imagen no es obligatoria, verificamos si el usuario envio una imagen para guardarla
    if (isset($_FILES['profile'])) {
        // Validacion para solo aceptar un determinado formato de imagenes
        if ($_FILES['profile']['type'] != 'image/png' && $_FILES['profile']['type'] != 'image/jpg' && $_FILES['profile']['type'] != 'image/jpeg') {
            $response['error'] = "El formato de la foto de perfil no es correcto, por favor selecciona una imagen valida.";
            return $response;
        }
        //Nombre del archivo que se subira
        $name = time() . '_' . $_FILES['profile']['name'];
        if (!move_uploaded_file($_FILES['profile']['tmp_name'], $path . $name)) {
            //Se ejecuta si se produce un error al momento de guardar la imagen
            $response['error'] = "Se produjo un error al momento de subir la imagen.";
            return $response;
        }
    } else {
        //Si el usuario no manda una imagen se guarda con la imagen por defecto
        $name = "default.jpg";
    }

    try {
        //Preparamos el Statment para la consulta 
        $stmt = $connection->prepare("INSERT INTO employees(job_id, first_name, last_name, email, phone_number, salary, profile) VALUES(?,?,?,?,?,?,?)");

        //Definicion de los compos con su tipo
        $stmt->bind_param("issssds", $job_id, $first_name, $last_name, $email, $phone_number, $salary, $profile);

        //Asignación de los datos a variables para guardar en la BD
        $profile = $path . $name;
        $job_id = (int)$form_data['job_id'];
        $first_name = $form_data['first_name'];
        $last_name = $form_data['last_name'];
        $email = $form_data['email'];
        $phone_number = $form_data['phone_number'];
        $salary = $form_data['salary'];

        //Ejecutamos la operación
        $stmt->execute();

        //Cerramos conexiones
        $stmt->close();
        $connection->close();

        //Respondemos el servicio con un status 200 que todo salio bien
        $response["msg"] = "Empleado registrado correctamente";
        return $response;
    } catch (Exception $e) {
        var_dump($e);
    }
}

//Se deben actualizar los datos del empleado en base a su ID, validar los datos antes de realizar la actualización
//METHOD: PUT
function updateEmployee()
{
    $response = array();
    global $connection;
    //Pattern para verificar la estructura del email
    $pattern = "/^[\w\-\.]+@([\w-]+\.)+[\w-]{2,4}$/";


    //Validando los datos del frontend, recicle el que se usa para el saveEmployee
    if (!isset($form_data['first_name']) || isset($form_data['first name']) == '') {
        $response["error"] = "Por favor, escriba un nombre para poder actualizar la informacion del empleado.";
        return $response;
    } elseif (!isset($form_data['last_name'])) {
        $response["error"] = "Por favor, escriba un apellido para poder actualizar la informacion del empleado.";
        return $response;
    } elseif (!isset($form_data['email'])) {
        $response["error"] = "Por favor, escriba un correo electronico para poder actualizar la informacion del empleado.";
        return $response;
    } elseif (!isset($form_data['phone_number'])) {
        $response["error"] = "Por favor, escriba un numero de telefono para poder actualizar la informacion del empleado.";
        return $response;
    } elseif (!isset($form_data['salary']) || !is_numeric($form_data['salary'])) {
        $response["error"] = "Por favor, escriba el salario para poder actualizar la informacion del empleado.";
        return $response;
    } elseif (!isset($form_data['job_id'])) {
        $response["error"] = "Por favor, escriba un rol para poder actualizar la informacion del empleado.";
        return $response;
    }

    if (preg_match_all($pattern, $form_data['email']) == 0) {
        $response["error"] = "Formato de correo electrónico no valido.";
        return $response;
    }

    //Funcion dedicada a verificar si el email ya se encuentra registrado en la base de datos
    //copie muchas cosas de arriba perdon por no saber ;-;
    if (existEmail($form_data['email'])) {
        $response['error'] = "La dirección de correo electrónico ya se encuentra registrada en el sistema.";
        return $response;
    }

    try{
    //Prepar el Statment para la consulta de update
    $stmt = $connection->prepare("UPDATE employees SET job_id = ?, first_name = ?, last_name = ?, email = ?, phone_number = ?, hire_date = ?, salary = ?, active = ?, profile = ?  
    WHERE employee_id = ?");

    //Definicion de los compos con su tipo
    $stmt->bind_param("issssds", $job_id, $first_name, $last_name, $email, $phone_number, $salary, $profile);

    //Asignación de los datos a variables para guardar en la BD
    $profile = $path . $name;
    $job_id = (int)$form_data['job_id'];
    $first_name = $form_data['first_name'];
    $last_name = $form_data['last_name'];
    $email = $form_data['email'];
    $phone_number = $form_data['phone_number'];
    $salary = $form_data['salary'];

    //Ejecutamos la operación
    $stmt->execute();

    //Cerramos conexiones
    $stmt->close();
    $connection->close();

    //Respondemos el servicio con un status 200 que todo salio bien
    $response["msg"] = "Empleado actualizado correctamente";
    return $response;
    }catch(Exception $e){
        var_dump($e);
    }
}


//Debe 'eliminar un usuario' pero en realidad debe pasar su estado activo=false
//METHOD: DELETE
function deleteEmployee()
{
};
