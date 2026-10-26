<?php

require_once("../config/conexion.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $id               = $_POST["id"];
    $nombre           = trim($_POST["nombre"]);
    $apellidos        = trim($_POST["apellidos"]);
    $nomusuario       = trim($_POST["nomusuario"]);
    $contrasena       = trim($_POST["contrasena"]);
    $fechaNacimiento  = $_POST["fechaNacimiento"];
    $fechaIngreso     = $_POST["fechaIngreso"];
    $numNomina        = $_POST["numNomina"];
    $idRol            = $_POST["idRol"];
    $idDep            = $_POST["idDep"];
    $idPuesto         = $_POST["idPuesto"];
    $diasvac        = $_POST["diasvac"];
    $correo      = $_POST["correo"];

    $sql = $conexion->prepare("
        UPDATE usuarios
        SET

        nombre=:nombre,
        apellidos=:apellidos,
        nomusuario=:nomusuario,
        contrasena=:contrasena,
        fechaNacimiento=:fechaNacimiento,
        fechaIngreso=:fechaIngreso,
        numNomina=:numNomina,
        idRol=:idRol,
        idDep=:idDep,
        idPuesto=:idPuesto,
        correo=:correo,
        diasVacaciones=:diasvac

        WHERE id=:id
    ");

    $sql->bindParam(":nombre",$nombre);
    $sql->bindParam(":apellidos",$apellidos);
    $sql->bindParam(":nomusuario",$nomusuario);
    $sql->bindParam(":contrasena",$contrasena);
    $sql->bindParam(":fechaNacimiento",$fechaNacimiento);
    $sql->bindParam(":fechaIngreso",$fechaIngreso);
    $sql->bindParam(":numNomina",$numNomina);
    $sql->bindParam(":idRol",$idRol);
    $sql->bindParam(":idDep",$idDep);
    $sql->bindParam(":idPuesto",$idPuesto);
     $sql->bindParam(":diasvac",$diasvac);

      $sql->bindParam(":correo",$correo);
    $sql->bindParam(":id",$id);

    if($sql->execute()){

        header("Location: ../views/dashboard.php?page=usuarios&editado=1");
        exit();

    }else{

        header("Location: ../views/dashboard.php?page=usuarios&error=1");
        exit();

    }

}