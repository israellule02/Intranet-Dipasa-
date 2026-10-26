<?php

session_start();

require_once("../config/conexion.php");

if(isset($_GET["id"])){

    $id = $_GET["id"];

    $sql = $conexion->prepare("

        DELETE FROM prestamos

        WHERE id = :id

    ");

    $sql->bindParam(":id",$id,PDO::PARAM_INT);

    if($sql->execute()){

        header("Location: ../views/dashboard.php?page=consultarPrestamo&eliminado=1");
        exit();

    }else{

        header("Location: ../views/dashboard.php?page=consultarPrestamo&error=1");
        exit();

    }

}else{

    header("Location: ../views/dashboard.php?page=consultarPrestamo");
    exit();

}

?>