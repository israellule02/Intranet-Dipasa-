<?php

session_start();

require_once("../config/conexion.php");


/*=========================================
VALIDAR SESIÓN
=========================================*/

if(!isset($_SESSION["idUsuario"])){

    header("Location: ../index.php");

    exit();

}


/*=========================================
VALIDAR MÉTODO POST
=========================================*/

if($_SERVER["REQUEST_METHOD"] != "POST"){

    header(
        "Location: ../views/dashboard.php?page=solicitudesVacaciones&error=metodo"
    );

    exit();

}


/*=========================================
OBTENER DATOS
=========================================*/

$idUsuarioSesion = $_SESSION["idUsuario"];

$idRegistro = $_POST["idRegistro"] ?? "";

$accion = $_POST["accion"] ?? "";


/*=========================================
VALIDAR DATOS
=========================================*/

if(

    empty($idRegistro)

    ||

    empty($accion)

){

    header(
        "Location: ../views/dashboard.php?page=solicitudesVacaciones&error=datos"
    );

    exit();

}


/*=========================================
VALIDAR ACCIÓN
=========================================*/

if(

    $accion != "aceptar"

    &&

    $accion != "rechazar"

){

    header(
        "Location: ../views/dashboard.php?page=solicitudesVacaciones&error=accion"
    );

    exit();

}


/*=========================================
ASIGNAR NUEVO ESTATUS
=========================================*/

if($accion == "aceptar"){

    $idNuevoEstatus = 1;

}else{

    $idNuevoEstatus = 3;

}


/*=========================================
VALIDAR QUE LA SOLICITUD EXISTA

TAMBIÉN VALIDAMOS QUE:

1. LA SOLICITUD PERTENEZCA AL JEFE
   QUE ESTÁ EN SESIÓN

2. LA SOLICITUD SIGA PENDIENTE
=========================================*/

$sqlSolicitud = $conexion->prepare("

    SELECT

        idRegistro,

        idJefe,

        idValidacionJefe

    FROM registrovac

    WHERE idRegistro = :idRegistro

    AND idJefe = :idJefe

    AND idValidacionJefe = 2

");


$sqlSolicitud->bindValue(

    ":idRegistro",

    $idRegistro,

    PDO::PARAM_INT

);


$sqlSolicitud->bindValue(

    ":idJefe",

    $idUsuarioSesion,

    PDO::PARAM_INT

);


$sqlSolicitud->execute();


$solicitud = $sqlSolicitud->fetch(PDO::FETCH_ASSOC);


/*=========================================
VALIDAR SOLICITUD
=========================================*/

if(!$solicitud){

    header(
        "Location: ../views/dashboard.php?page=solicitudesVacaciones&error=solicitud"
    );

    exit();

}


/*=========================================
ACTUALIZAR SOLICITUD
=========================================*/

$sqlActualizar = $conexion->prepare("

    UPDATE registrovac

    SET

        idValidacionJefe = :idEstatus

    WHERE idRegistro = :idRegistro

    AND idJefe = :idJefe

    AND idValidacionJefe = 2

");


$sqlActualizar->bindValue(

    ":idEstatus",

    $idNuevoEstatus,

    PDO::PARAM_INT

);


$sqlActualizar->bindValue(

    ":idRegistro",

    $idRegistro,

    PDO::PARAM_INT

);


$sqlActualizar->bindValue(

    ":idJefe",

    $idUsuarioSesion,

    PDO::PARAM_INT

);


/*=========================================
EJECUTAR
=========================================*/

if($sqlActualizar->execute()){


    /*=========================================
    VALIDAR QUE REALMENTE SE MODIFICÓ
    =========================================*/

    if($sqlActualizar->rowCount() > 0){


        /*=========================================
        MENSAJE SEGÚN LA ACCIÓN
        =========================================*/

        if($accion == "aceptar"){


        require_once("EnviarCorreoVacacionesRHController.php");

        enviarCorreoVacacionesRH($idRegistro);

            header(
                "Location: ../views/dashboard.php?page=solicitudesVacaciones&aceptado=1"
            );

        }else{

            header(
                "Location: ../views/dashboard.php?page=solicitudesVacaciones&rechazado=1"
            );

        }

        exit();

    }


}


/*=========================================
ERROR
=========================================*/

header(
    "Location: ../views/dashboard.php?page=solicitudesVacaciones&error=1"
);

exit();

?>