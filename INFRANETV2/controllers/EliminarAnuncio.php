<?php

session_start();

require_once("../config/conexion.php");

if(isset($_GET["id"])){

    $id=$_GET["id"];

    /*=========================================
        OBTENER IMAGEN
    =========================================*/

    $sql=$conexion->prepare("
        SELECT imagen
        FROM avisos
        WHERE idAvisos=:id
    ");

    $sql->bindParam(":id",$id);

    $sql->execute();

    $aviso=$sql->fetch(PDO::FETCH_ASSOC);

    if($aviso){

        if($aviso["imagen"]!=""){

            $ruta="../assets/img/anuncios/".$aviso["imagen"];

            if(file_exists($ruta)){
                unlink($ruta);
            }

        }

    }

    /*=========================================
        ELIMINAR REGISTRO
    =========================================*/

    $eliminar=$conexion->prepare("
        DELETE FROM avisos
        WHERE idAvisos=:id
    ");

    $eliminar->bindParam(":id",$id);

    if($eliminar->execute()){

        header("Location: ../views/dashboard.php?page=administrarAnuncios&eliminado=1");

    }else{

        header("Location: ../views/dashboard.php?page=administrarAnuncios&error=1");

    }

}