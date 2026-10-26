<?php

session_start();

require_once("../config/conexion.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $marca = trim($_POST["marca"]);
    $numdeserie = trim($_POST["numdeserie"]);
    $caracteristicas = trim($_POST["caracteristicas"]);
    $idTipoDis = $_POST["idTipoDis"];
    $idestatusdis = $_POST["idestatusdis"];
    $fechadecompra = $_POST["fechadecompra"];

    /*=========================================
    VALIDAR CAMPOS
    =========================================*/

    if(
        empty($marca) ||
        empty($numdeserie) ||
        empty($caracteristicas) ||
        empty($idTipoDis) ||
        empty($idestatusdis) ||
        empty($fechadecompra)
    ){

        header("Location: ../views/dashboard.php?page=registrarDispositivo&error=1");
        exit();

    }

    /*=========================================
    INSERTAR DISPOSITIVO
    =========================================*/

    $sql = $conexion->prepare("

        INSERT INTO dispositivos(

            marca,
            caracteristicas,
            numdeserie,
            idTipoDis,
            idestatusdis,
            fechadecompra

        )

        VALUES(

            :marca,
            :caracteristicas,
            :numdeserie,
            :idTipoDis,
            :idestatusdis,
            :fechadecompra

        )

    ");

    $sql->bindParam(":marca",$marca);
    $sql->bindParam(":caracteristicas",$caracteristicas);
    $sql->bindParam(":numdeserie",$numdeserie);
    $sql->bindParam(":idTipoDis",$idTipoDis,PDO::PARAM_INT);
    $sql->bindParam(":idestatusdis",$idestatusdis,PDO::PARAM_INT);
    $sql->bindParam(":fechadecompra",$fechadecompra);

    if($sql->execute()){

        header("Location: ../views/dashboard.php?page=consultarDispositivo&guardado=1");
        exit();

    }else{

        header("Location: ../views/dashboard.php?page=registrarDispositivo&error=1");
        exit();

    }

}else{

    header("Location: ../views/dashboard.php?page=consultarDispositivo");
    exit();

}