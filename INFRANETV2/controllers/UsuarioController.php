<?php

require_once("../config/conexion.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

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
    $correo         = $_POST["correo"];

    $sql = $conexion->prepare("
        INSERT INTO usuarios
        (
            nombre,
            apellidos,
            nomusuario,
            contrasena,
            fechaNacimiento,
            fechaIngreso,
            numNomina,
            idRol,
            idDep,
            correo,
            idPuesto
        )
        VALUES
        (
            :nombre,
            :apellidos,
            :nomusuario,
            :contrasena,
            :fechaNacimiento,
            :fechaIngreso,
            :numNomina,
            :idRol,
            :idDep,
            :correo,
            :idPuesto
        )
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
    $sql->bindParam(":correo",$correo);
    $sql->bindParam(":idPuesto",$idPuesto);

    if($sql->execute()){

        header("Location: ../views/dashboard.php?page=usuarios&ok=1");
        exit();

    }else{

        header("Location: ../views/dashboard.php?page=crearUsuario&error=1");
        exit();

    }

}