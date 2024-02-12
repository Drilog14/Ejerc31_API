<?php

// Ruta al archivo autoload.php de Composer
require_once 'vendor/autoload.php'; 

// usamos la librería Firebase
// use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// indicamos la clave secreta del usuario
$clave = "1234";

// obtenemos el token de la variable del servidor
//print_r($_SERVER);
$token = $_SERVER['HTTP_TOKEN'];
//$token = $_SERVER['HTTP_AUTHORIZATION'];

// creamos la cabecera para indicar que vamos a devolver un recurso REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {

    // creamos un objeto de la clase stdClass con el algoritmo
    $objeto = new stdClass();

    // intentamos decodificar el token
    $stdInfoUsuario = JWT::decode($token, new Key($clave, 'HS256'), $objeto);

    // si el token no es válido, no se permite el acceso al servicio web
    if (!$stdInfoUsuario) throw new Exception("Acceso denegado");


// creamos la cabecera para indicar que vamos a devolver un recurso REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// parámetros de conexión
$dbname = "fallas_valencia";
$host = "localhost"; // también podríamos haber puesto 127.0.0.1

// creamos la cadena de conexión
$strConexion = "mysql:dbname=$dbname;host=$host";

// creamos las credenciales del usuario
$usuario = "guiesp";
$clave = "12345678";

try {

    // creamos un objeto de la clase PDO
    $bd = new PDO($strConexion, $usuario, $clave);

    // si no se han encontrado fallas se devolverá el error
    if (!$bd) throw new Exception("Error en la conexión a la BD");

    // creamos la insercion
    $sql = "delete from falleros where dni = ?;";

    // preparamos la consulta
    $pdoPreparada = $bd->prepare($sql);

    // recogemos los parámetros del JSON recibido
    $objFalla = json_decode(file_get_contents("php://input"));

    // creamos el array de parámetros para los valores
    $arrParametros = [
        $objFalla->dni
    ];

    // obtenemos el id de la falla
    $id = $objFalla->dni;

    // ejecutamos la consulta preparada
    $resultado = $pdoPreparada->execute($arrParametros);

    // si no se ha podido insertar se lanza el error
    if (!$resultado) throw new Exception('No se ha podido realizar la actualización.<br>');
    
    // definimos la cabecera HTTP con código OK
    http_response_code(200); 

    // respuesta de inserción realizada con éxito
    $arrInsercion = ["mensaje" => "Falla $id eliminada con éxito"];
    
    // definimos y enviamos el json con las fallas
    echo json_encode($arrInsercion);
    
// si la conexión no ha tenido éxito lo indicamos    
} catch (PDOException $e) {

    // definimos la cabecera HTTP con el código de error
    http_response_code(400); 

    // definimos y enviamos el array con el mensaje de error
    $arrError = ["mensaje" => $e->getMessage()];
    echo json_encode($arrError);
}
// si no se han encontrado fallas se devuelve el error
} catch (Exception $e) {

    // definimos la cabecera HTTP con el código de error
    http_response_code(400); 

    // definimos y enviamos el array con el mensaje de error
    $arrError = ["mensaje" => $e->getMessage()];
    echo json_encode($arrError);
}
?>