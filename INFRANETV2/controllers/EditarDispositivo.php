<?php

session_start();

require_once("../config/conexion.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $id = $_POST["id"];
    $marca = trim($_POST["marca"]);
    $numdeserie = trim($_POST["numdeserie"]);
    $caracteristicas = trim($_POST["caracteristicas"]);
    
    $idTipoDis = $_POST["idTipoDis"];
    $idestatusdis = $_POST["idestatusdis"];
    $fechadecompra = ($_POST["fechadecompra"]);


    /*=========================================
    VALIDAR CAMPOS
    =========================================*/

    if(
        empty($id) ||
        empty($marca) ||
        empty($numdeserie) ||
        empty($caracteristicas) ||
        empty($idTipoDis) ||
        empty($idestatusdis) ||
        empty($fechadecompra)
    ){

        header("Location: ../views/dashboard.php?page=editarDispositivo&id=".$id."&error=1");
        exit();

    }

    /*=========================================
    ACTUALIZAR DISPOSITIVO
    =========================================*/

    $sql = $conexion->prepare("

        UPDATE dispositivos

        SET

            marca = :marca,
            caracteristicas = :caracteristicas,
            numdeserie = :numdeserie,
            idTipoDis = :idTipoDis,
            idestatusdis = :idestatusdis,
            fechadecompra = :fechadecompra

        WHERE id = :id

    ");

    $sql->bindParam(":marca",$marca);
    $sql->bindParam(":caracteristicas",$caracteristicas);
    $sql->bindParam(":numdeserie",$numdeserie);
   
    $sql->bindParam(":idTipoDis",$idTipoDis,PDO::PARAM_INT);
    $sql->bindParam(":idestatusdis",$idestatusdis,PDO::PARAM_INT);
    $sql->bindParam(":id",$id,PDO::PARAM_INT);

      $sql->bindParam(":fechadecompra",$fechadecompra);

    if($sql->execute()){

        header("Location: ../views/dashboard.php?page=consultarDispositivo&editado=1");
        exit();

    }else{

        header("Location: ../views/dashboard.php?page=editarDispositivo&id=".$id."&error=1");
        exit();

    }

}else{

    header("Location: ../views/dashboard.php?page=consultarDispositivo");
    exit();

}