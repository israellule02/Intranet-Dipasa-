<?php

session_start();

require_once("../config/conexion.php");

header("Content-Type: application/json; charset=utf-8");


/*=========================================
    CONTRASEÑA FIJA DE AUTORIZACIÓN
=========================================*/

$CLAVE_AUTORIZACION = "INTRANET2026";


/*=========================================
    CLAVE DE CIFRADO
=========================================*/

$CLAVE_CIFRADO =
    "INFRANETV2_CLAVE_SEGURA_2026_123456789";


/*=========================================
    FUNCIÓN PARA DESCIFRAR
=========================================*/

function descifrarContrasena($dato, $clave)
{

    $datos = base64_decode(
        $dato,
        true
    );


    if($datos === false){

        return false;

    }


    /*
     * Los primeros 16 bytes corresponden
     * al IV utilizado durante el cifrado.
     */

    $iv =
        substr(
            $datos,
            0,
            16
        );


    $cifrado =
        substr(
            $datos,
            16
        );


    return openssl_decrypt(

        $cifrado,

        "AES-256-CBC",

        hash(
            "sha256",
            $clave,
            true
        ),

        OPENSSL_RAW_DATA,

        $iv

    );

}


/*=========================================
    VALIDAR MÉTODO
=========================================*/

if(
    $_SERVER["REQUEST_METHOD"]
    !==
    "POST"
){

    echo json_encode([

        "success" => false,

        "mensaje" =>
            "Método no permitido."

    ]);

    exit();

}


/*=========================================
    RECIBIR DATOS
=========================================*/

$id =
    isset($_POST["id"])
    ?
    intval($_POST["id"])
    :
    0;


$password =
    isset($_POST["password"])
    ?
    trim($_POST["password"])
    :
    "";


/*=========================================
    VALIDAR ID
=========================================*/

if($id <= 0){

    echo json_encode([

        "success" => false,

        "mensaje" =>
            "Credencial no válida."

    ]);

    exit();

}


/*=========================================
    VALIDAR CONTRASEÑA DE AUTORIZACIÓN
=========================================*/

if(
    $password
    !==
    $CLAVE_AUTORIZACION
){

    echo json_encode([

        "success" => false,

        "mensaje" =>
            "Contraseña de autorización incorrecta."

    ]);

    exit();

}


/*=========================================
    OBTENER CREDENCIAL
=========================================*/

$sql =
    $conexion->prepare("

        SELECT

            id,

            `contraseña`

        FROM credenciales

        WHERE id = ?

    ");


$sql->execute([

    $id

]);


$credencial =
    $sql->fetch(
        PDO::FETCH_ASSOC
    );


/*=========================================
    VALIDAR CREDENCIAL
=========================================*/

if(!$credencial){

    echo json_encode([

        "success" => false,

        "mensaje" =>
            "No se encontró la credencial."

    ]);

    exit();

}


/*=========================================
    GUARDAR AUTORIZACIÓN EN SESIÓN
=========================================*/

if(
    !isset(
        $_SESSION["credencial_autorizada"]
    )
){

    $_SESSION["credencial_autorizada"] =
        [];

}


$_SESSION[
    "credencial_autorizada"
][$id] = true;


/*=========================================
    DESCIFRAR CONTRASEÑA
=========================================*/

$contrasena =
    descifrarContrasena(

        $credencial["contraseña"],

        $CLAVE_CIFRADO

    );


/*=========================================
    VALIDAR DESCIFRADO
=========================================*/

if(
    $contrasena === false
    ||
    $contrasena === null
){

    echo json_encode([

        "success" => false,

        "mensaje" =>
            "No fue posible descifrar la contraseña."

    ]);

    exit();

}


/*=========================================
    RESPUESTA
=========================================*/

echo json_encode([

    "success" => true,

    "contrasena" =>
        $contrasena

]);


exit();

?>