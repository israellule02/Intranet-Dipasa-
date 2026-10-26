<?php

session_start();

require_once("../config/conexion.php");

if(!isset($_GET["id"])){

    header("Location: ../views/dashboard.php?page=consultarAsignaciones");
    exit();

}

$id = $_GET["id"];

/*=====================================
OBTENER DISPOSITIVO
=====================================*/

$sql = $conexion->prepare("

SELECT idDispositivo

FROM asignaciones

WHERE id=:id

");

$sql->bindParam(":id",$id,PDO::PARAM_INT);

$sql->execute();

$asignacion = $sql->fetch(PDO::FETCH_ASSOC);

if(!$asignacion){

    die("Asignación no encontrada.");

}

$idDispositivo = $asignacion["idDispositivo"];

/*=====================================
ACTUALIZAR ASIGNACIÓN
=====================================*/

$actualizar = $conexion->prepare("

UPDATE asignaciones

SET

idpreestatus = 2,

fechadeentregado = CURDATE()

WHERE id = :id

");

$actualizar->bindParam(":id",$id,PDO::PARAM_INT);

$actualizar->execute();

/*=====================================
LIBERAR DISPOSITIVO
=====================================*/

$liberar = $conexion->prepare("

UPDATE dispositivos

SET idEstatusDis = 1

WHERE id = :id

");

$liberar->bindParam(":id",$idDispositivo,PDO::PARAM_INT);

$liberar->execute();

/*=====================================
REGRESAR
=====================================*/

header("Location: ../views/dashboard.php?page=detalleAsignacion&id=".$id."&entregado=1");

exit();

?>