<?php

require_once("../config/conexion.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $id = $_POST["id"];
    $idEstatus = $_POST["idEstatus"];

    if($idEstatus==2){

        $sql=$conexion->prepare("

            UPDATE prestamos
            SET
                idEstatus=:idEstatus,
                fechaDeEntregado=CURDATE()
            WHERE id=:id

        ");

    }else{

        $sql=$conexion->prepare("

            UPDATE prestamos
            SET
                idEstatus=:idEstatus,
                fechaDeEntregado='0000-00-00'
            WHERE id=:id

        ");

    }

    $sql->bindParam(":idEstatus",$idEstatus);
    $sql->bindParam(":id",$id);

    if($sql->execute()){

        header("Location: ../views/dashboard.php?page=estatusPrestamos&ok=1");

    }else{

        header("Location: ../views/dashboard.php?page=estatusPrestamos&error=1");

    }

}