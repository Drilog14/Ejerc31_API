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

    // creamos la consulta con interrogantes
    $sql = "select 
    f2.nombre as nombr,
    f1.nombre,
    f1.apellidos
 from fallas f2 left join falleros f1 on f1.id_falla = f2.id_falla
 where f2.id_falla like ?";

    // preparamos la consulta
    $queryPreparada = $bd->prepare($sql);

    $presupuesto = (isset($_GET['id_falla']) && $_GET['id_falla']) ? $_GET['id_falla'] : '1';

    // creamos el array de parámetros para los valores
    $arrParametros = [$presupuesto];

    // ejecutamos la consulta preparada
    $queryPreparada->execute($arrParametros);

    // si no se han encontrado fallas se devolverá el error
    if (!$queryPreparada->rowCount()) throw new Exception("fallas no encontradas");

    // definimos el array de fallas
    $arrFallas = [];

    // mostarmos todos los registros devueltos
    while($registro = $queryPreparada->fetch()) {
    
        // creamos el registro de la falla n-ésima
        $regFalla = [
            'falla' => $registro["nombr"],
            'nombre' => $registro["nombre"],
            'apellidos' => $registro["apellidos"]
        ];

        // guardamos la falla n-ésima en el array de fallas
        $arrFallas[] = $regFalla;
    }

    // definimos la cabecera HTTP con código OK
    http_response_code(200); 

    // definimos y enviamos el json con las fallas
    echo json_encode($arrFallas);
    
// si la conexión no ha tenido éxito lo indicamos    
} catch (PDOException $e) {

    // definimos la cabecera HTTP con el código de error
    http_response_code(502); 

    // definimos y enviamos el array con el mensaje de error
    $arrError = ["mensaje" => $e->getMessage()];
    echo json_encode($arrError);
}
// si no se han encontrado fallas se devuelve el error
} catch (Exception $e) {

    // definimos la cabecera HTTP con el código de error
    http_response_code(403); 

    // definimos y enviamos el array con el mensaje de error
    $arrError = ["mensaje" => $e->getMessage()];
    echo json_encode($arrError);

}
?>