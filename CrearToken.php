<?php

// Ruta al archivo autoload.php de Composer
require_once 'vendor/autoload.php'; 

// usamos la librería Firebase
use Firebase\JWT\JWT;

// indicamos la clave secreta del usuario
$clave = "1234";

// indicamos más información acerca del usuario
$arrInfoUsuario = array(
    "id_usuario" => 123,
    "usuario" => "Guille Puerto",
    "email" => "guille.puerto@gmail.com"
);

// creamos el token con el algoritmo HS256
$token = JWT::encode($arrInfoUsuario, $clave, 'HS256');

// creamos la cookie en el navegador del usuario para que dure 1 hora
setcookie("token", $token, time() + 3600);

// avisamos al usuario de que se ha creado la cookie adecuadamente
echo "token creado y enviado al cliente:<br><br>$token";
?>