<?php
require('./controllers/employees.php');

//Obetemos el la solicitud HTTP desde el frontend
$request_method = $_SERVER['REQUEST_METHOD'];
$response = array();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

//Rutas dependiendo del verbo http recibido
switch ($request_method) {
    case 'GET':
        //Obtenemos los parametros dentro de la URL
        $request_uri = $_SERVER['REQUEST_URI'];

        //Separamos el string por cada '/' que exista
        $args_arr = explode("/", $request_uri);

        //Obtenemos el ultimo parametro ya que de venir seria en la ultima posición
        $arg = $args_arr[count($args_arr)  - 1];

        //Si el ultimo argumento esta vacio o inicia con 'employee.php' es porque no hay parametros en la URL
        if (str_starts_with($arg, 'employees.php') || $arg == '') {

            //Si no hay parametros en la URL debemos recibir el Query Param de page, el cual debe ser numerico
            if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
                http_response_code(400);
                $response['error'] = "Se debe especificar la pagina y debe ser en valor numerico.";
            } else {
                //En este punto ya esta claro que podemos obtener la pagina la cual sabemos que existe y es un numero
                $page = $_GET['page'];

                //Ejecutamos el controlador el cual retornara la información solicitada.
                $response = getEmployees($page);
            }
        } else {
            $response = getEmployee($arg);
        }
        echo json_encode($response);
        break;
    case 'POST':
        $response = saveEmployee($_POST);
        if (isset($response['error'])) {
            http_response_code(400);
        }
        echo json_encode($response);
        break;
    case 'PUT':
        //TODO: Validar, obtener datos y llamar al controlador para actualizar la información
        break;
    case 'DELETE':
        //TODO: Validar, obtener datos y llamar al controlador para eliminar
        break;
    default:
        //Si el verbo HTTP no es ninguno de los declarados se envia el siguiente error
        http_response_code(500);
        $response["error"] = "Error interno, contacte al administrador";
        echo json_encode($response, true);
}
