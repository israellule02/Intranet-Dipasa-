<?php

session_start();

require_once("../config/conexion.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $id               = $_POST["id"];
    $idDispositivo    = $_POST["idDispositivo"];
    $idUsuario        = $_POST["idUsuario"];
    $idEstado         = $_POST["idEstado"];
    $fechainicio      = $_POST["fechainicio"];

    $fechadeentregado = !empty($_POST["fechadeentregado"])
        ? $_POST["fechadeentregado"]
        : NULL;

    $idpreestatus = $_POST["idpreestatus"];

    /*=========================================
    OBTENER EL DISPOSITIVO ANTERIOR
    =========================================*/

    $consulta = $conexion->prepare("
        SELECT idDispositivo
        FROM asignaciones
        WHERE id = :id
    ");

    $consulta->bindParam(":id",$id,PDO::PARAM_INT);
    $consulta->execute();

    $anterior = $consulta->fetch(PDO::FETCH_ASSOC);

    $idDispositivoAnterior = $anterior["idDispositivo"];

    /*=========================================
    ACTUALIZAR ASIGNACIÓN
    =========================================*/

    $sql = $conexion->prepare("

        UPDATE Prestamos

        SET

            idDispositivo    = :idDispositivo,
            idUsuario        = :idUsuario,
            idEstado         = :idEstado,
            fechainicio      = :fechainicio,
            fechadeentregado = :fechadeentregado,
            idpreestatus     = :idpreestatus

        WHERE id = :id

    ");

    $sql->bindParam(":id",$id,PDO::PARAM_INT);
    $sql->bindParam(":idDispositivo",$idDispositivo,PDO::PARAM_INT);
    $sql->bindParam(":idUsuario",$idUsuario,PDO::PARAM_INT);
    $sql->bindParam(":idEstado",$idEstado,PDO::PARAM_INT);
    $sql->bindParam(":fechainicio",$fechainicio);
    $sql->bindParam(":fechadeentregado",$fechadeentregado);
    $sql->bindParam(":idpreestatus",$idpreestatus,PDO::PARAM_INT);

    if($sql->execute()){

        /*=========================================
        SI CAMBIÓ EL DISPOSITIVO
        =========================================*/

        if($idDispositivoAnterior != $idDispositivo){

            $liberar = $conexion->prepare("
                UPDATE dispositivos
                SET idEstatusDis = 1
                WHERE id = :id
            ");

            $liberar->bindParam(":id",$idDispositivoAnterior,PDO::PARAM_INT);
            $liberar->execute();

        }

        /*=========================================
        ACTUALIZAR ESTATUS DEL DISPOSITIVO
        1 = EN PRÉSTAMO
        2 = ENTREGADO
        =========================================*/

        if($idpreestatus == 1){

            $estadoDispositivo = 2;

        }else{

            $estadoDispositivo = 1;

        }

        $actualizar = $conexion->prepare("
            UPDATE dispositivos
            SET idEstatusDis = :estado
            WHERE id = :id
        ");

        $actualizar->bindParam(":estado",$estadoDispositivo,PDO::PARAM_INT);
        $actualizar->bindParam(":id",$idDispositivo,PDO::PARAM_INT);
        $actualizar->execute();

        header("Location: ../views/dashboard.php?page=consultarPrestamo&editado=1");
        exit();

    }else{

        echo "<pre>";
        print_r($sql->errorInfo());
        echo "</pre>";

    }

}else{

    header("Location: ../views/dashboard.php?page=consultarPrestamo");
    exit();

}