<?php

session_start();

require_once("../config/conexion.php");

$usuario=$_POST['usuario'];
$password=$_POST['password'];


$sql="SELECT * FROM usuarios
WHERE nomusuario=:usuario
AND contrasena=:password";

$stmt=$conexion->prepare($sql);

$stmt->bindParam(":usuario",$usuario);
$stmt->bindParam(":password",$password);

$stmt->execute();

if($stmt->rowCount()>0){

    $datos=$stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['usuario'] = $datos['nomusuario'];
    $_SESSION['nombre'] = $datos['nombre'];
    $_SESSION['apellidos'] = $datos['apellidos'];
    $_SESSION['idUsuario'] = $datos['id'];
     $_SESSION['idRol'] = $datos['idRol'];
      $_SESSION['idDep'] = $datos['idDep'];

    header("Location: ../views/dashboard.php");

}else{

    header("Location: ../index.php?error");

}