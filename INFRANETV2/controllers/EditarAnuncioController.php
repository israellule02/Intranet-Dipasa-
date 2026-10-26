<?php

session_start();

require_once("../config/conexion.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $id=$_POST["idAvisos"];
    $titulo=$_POST["titulo"];
    $contenido=$_POST["contenido"];
    $fecha=$_POST["fecha"];
    $hora=$_POST["hora"];

    $imagen=$_POST["imagenActual"];

    /*=========================================
        CAMBIAR IMAGEN
    =========================================*/

    if(isset($_FILES["imagen"]) && $_FILES["imagen"]["error"]==0){

        $carpeta="../assets/img/anuncios/";

        //Eliminar imagen anterior
        if($imagen!="" && file_exists($carpeta.$imagen)){
            unlink($carpeta.$imagen);
        }

        $extension=pathinfo($_FILES["imagen"]["name"],PATHINFO_EXTENSION);

        $imagen=time()."_".uniqid().".".$extension;

        move_uploaded_file(
            $_FILES["imagen"]["tmp_name"],
            $carpeta.$imagen
        );

    }

    /*=========================================
        ACTUALIZAR
    =========================================*/

    $sql=$conexion->prepare("
        UPDATE avisos SET

            titulo=:titulo,
            contenido=:contenido,
            fecha=:fecha,
            hora=:hora,
            imagen=:imagen

        WHERE idAvisos=:id
    ");

    $sql->bindParam(":titulo",$titulo);
    $sql->bindParam(":contenido",$contenido);
    $sql->bindParam(":fecha",$fecha);
    $sql->bindParam(":hora",$hora);
    $sql->bindParam(":imagen",$imagen);
    $sql->bindParam(":id",$id);

    if($sql->execute()){

        header("Location: ../views/dashboard.php?page=administrarAnuncios&editado=1");

    }else{

        header("Location: ../views/dashboard.php?page=editarAnuncio&id=".$id."&error=1");

    }

}