<?php

session_start();

require_once("../config/conexion.php");

/*=========================================
VALIDAR ID
=========================================*/

if(!isset($_GET["id"])){

    header("Location: ../views/dashboard.php?page=consultarDispositivo&error=1");
    exit();

}

$id = intval($_GET["id"]);

/*=========================================
VERIFICAR QUE EXISTA
=========================================*/

$sql = $conexion->prepare("

SELECT id

FROM dispositivos

WHERE id=:id

");

$sql->bindParam(":id",$id,PDO::PARAM_INT);

$sql->execute();

if($sql->rowCount()==0){

    header("Location: ../views/dashboard.php?page=consultarDispositivos&error=1");
    exit();

}

/*=========================================
ELIMINAR
=========================================*/

$eliminar = $conexion->prepare("

DELETE FROM dispositivos

WHERE id=:id

");

$eliminar->bindParam(":id",$id,PDO::PARAM_INT);

if($eliminar->execute()){

    header("Location: ../views/dashboard.php?page=consultarDispositivo&eliminado=1");
    exit();

}else{

    header("Location: ../views/dashboard.php?page=consultarDispositivo&error=1");
    exit();

}