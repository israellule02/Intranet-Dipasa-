<?php

session_start();

require_once("../config/conexion.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $idDispositivo    = $_POST["idDispositivo"];
    $idUsuario        = $_POST["idUsuario"];
    $idEstado         = $_POST["idEstado"];
    $fechainicio      = $_POST["fechainicio"];
    $fechadeentregado =  $_POST["fechadeentregado"] ;
  

    $sql = $conexion->prepare("

        INSERT INTO Prestamos(

            idDispositivo,
            idUsuario,
            idEstado,
            fechainicio,
            fechadeentregado,
            idpreestatus

        )

        VALUES(

            :idDispositivo,
            :idUsuario,
            :idEstado,
            :fechainicio,
            :fechadeentregado,
            1

        )

    ");

    $sql->bindValue(":idDispositivo",$idDispositivo,PDO::PARAM_INT);
    $sql->bindValue(":idUsuario",$idUsuario,PDO::PARAM_INT);
    $sql->bindValue(":idEstado",$idEstado,PDO::PARAM_INT);
    $sql->bindValue(":fechainicio",$fechainicio);
    $sql->bindValue(":fechadeentregado",$fechadeentregado);
   

    if($sql->execute()){

      

          $consulta = $conexion->query("SELECT MAX(id) AS id FROM Prestamos");
        $fila = $consulta->fetch(PDO::FETCH_ASSOC);

        $idAsignacion = $fila["id"];

        header("Location: ../views/dashboard.php?page=detallePrestamo&id=".$idAsignacion);
        exit();

    }else{

        echo "<pre>";
        print_r($sql->errorInfo());

    }

}