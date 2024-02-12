<?php

// creamos la cabecera para indicar que vamos a devolver un recurso REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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
    $sql = "insert into falleros (dni, nombre, apellidos, cuota, id_falla) values (?, ?, ?, ?, ?);";

    // preparamos la consulta
    $pdoPreparada = $bd->prepare($sql);

    // creamos el array de parámetros para los valores
    $arrParametros = [
        $_POST['dni'],
        $_POST['nombre'],
        $_POST['apellidos'],
        $_POST['cuota'],
        $_POST['id_falla']
    ];

    // ejecutamos la consulta preparada
    $resultado = $pdoPreparada->execute($arrParametros);

    // recuperamos el id del registro que acabamos de insertar
    $id = $bd->lastInsertId();

    // si no se ha podido insertar se lanza el error
    if (!$resultado) throw new Exception('No se ha podido realizar la inserción.<br>');
    
    // definimos la cabecera HTTP con código OK
    http_response_code(200); 

    // respuesta de inserción realizada con éxito
    $arrInsercion = ["mensaje" => "Falla $id creada con éxito"];
    
    // definimos y enviamos el json con las fallas
    echo json_encode($arrInsercion);
    
// si la conexión no ha tenido éxito lo indicamos    
} catch (PDOException $e) {

    // definimos la cabecera HTTP con el código de error
    http_response_code(400); 

    // definimos y enviamos el array con el mensaje de error
    $arrError = ["mensaje" => $e->getMessage()];
    echo json_encode($arrError);

// si no se han encontrado fallas se devuelve el error
} catch (Exception $e) {

    // definimos la cabecera HTTP con el código de error
    http_response_code(400); 

    // definimos y enviamos el array con el mensaje de error
    $arrError = ["mensaje" => $e->getMessage()];
    echo json_encode($arrError);
}
?>