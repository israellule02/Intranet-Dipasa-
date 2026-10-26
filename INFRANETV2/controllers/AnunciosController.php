<?php

session_start();

require_once("../config/conexion.php");

/*=====================================
    VERIFICAR SESIÓN
=====================================*/

if(!isset($_SESSION["idUsuario"])){

    header("Location: ../index.php");
    exit();

}

$idUsuario = $_SESSION["idUsuario"];

/*=====================================
    GUARDAR ANUNCIO
=====================================*/

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $titulo    = trim($_POST["titulo"]);
    $contenido = trim($_POST["contenido"]);
    $fecha     = $_POST["fecha"];
    $hora      = $_POST["hora"];

    $nombreImagen = "";

    /*=====================================
        SUBIR IMAGEN
    =====================================*/

    if(isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0){

        $carpeta = "../assets/img/anuncios/";

        if(!file_exists($carpeta)){

            mkdir($carpeta,0777,true);

        }

        $extension = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));

        $nombreImagen = time()."_".uniqid().".".$extension;

        move_uploaded_file(

            $_FILES["imagen"]["tmp_name"],
            $carpeta.$nombreImagen

        );

    }


    echo "<pre>";
print_r($_POST);
exit();



  
    /*=====================================
        INSERTAR EN BASE DE DATOS
    =====================================*/

    $sql = $conexion->prepare("

        INSERT INTO avisos(

            titulo,
            contenido,
            idUsuario,
            fecha,
            imagen,
            hora

        )

        VALUES(

            :titulo,
            :contenido,
            :idUsuario,
            :fecha,
            :imagen,
            :hora

        )

    ");

    $sql->bindParam(":titulo",$titulo);
    $sql->bindParam(":contenido",$contenido);
    $sql->bindParam(":idUsuario",$idUsuario,PDO::PARAM_INT);
    $sql->bindParam(":fecha",$fecha);
    $sql->bindParam(":imagen",$nombreImagen);
    $sql->bindParam(":hora",$hora);

    if($sql->execute()){

        header("Location: ../views/dashboard.php?page=verAnuncios&ok=1");
        exit();

    }else{

        header("Location: ../views/dashboard.php?page=crearAnuncio&error=1");
        exit();

    }

}