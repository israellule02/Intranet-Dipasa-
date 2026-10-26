<?php

require_once("../config/conexion.php");

if(isset($_GET["id"])){

    $id = $_GET["id"];

    $sql = $conexion->prepare("
        DELETE FROM usuarios
        WHERE id=:id
    ");

    $sql->bindParam(":id",$id);

    if($sql->execute()){

        header("Location: ../views/dashboard.php?page=usuarios&eliminado=1");
        exit();

    }else{

        header("Location: ../views/dashboard.php?page=usuarios&error=1");
        exit();

    }

}