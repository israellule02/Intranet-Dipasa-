<?php

session_start();

require_once("../config/conexion.php");


/*=========================================
VALIDAR SESIÓN
=========================================*/

if(!isset($_SESSION["idUsuario"])){

    header("Location: ../index.php");

    exit();

}


/*=========================================
VALIDAR MÉTODO
=========================================*/

if($_SERVER["REQUEST_METHOD"] != "POST"){

    header(
        "Location: ../views/dashboard.php?page=Registrardias&error=metodo"
    );

    exit();

}


/*=========================================
RECIBIR DATOS
=========================================*/

$idUsuario = $_POST["idUsuario"] ?? "";

$idPeriodo = $_POST["idPeriodo"] ?? "";

$diasDisponibles = $_POST["diasDisponibles"] ?? "";


/*=========================================
VALIDAR DATOS
=========================================*/

if(

    empty($idUsuario) ||

    empty($idPeriodo) ||

    $diasDisponibles === ""

){

    header(
        "Location: ../views/dashboard.php?page=Registrardias&error=datos"
    );

    exit();

}


/*=========================================
VALIDAR QUE LOS DÍAS NO SEAN NEGATIVOS
=========================================*/

if($diasDisponibles < 0){

    header(
        "Location: ../views/dashboard.php?page=Registrardias&error=dias"
    );

    exit();

}


/*=========================================
CONVERTIR A FLOAT
=========================================*/

$diasDisponibles = (float)$diasDisponibles;


/*=========================================
VERIFICAR QUE EL USUARIO EXISTA
=========================================*/

$sqlUsuario = $conexion->prepare("

    SELECT id

    FROM usuarios

    WHERE id = :idUsuario

    LIMIT 1

");

$sqlUsuario->bindValue(
    ":idUsuario",
    $idUsuario,
    PDO::PARAM_INT
);

$sqlUsuario->execute();


if(!$sqlUsuario->fetch()){

    header(
        "Location: ../views/dashboard.php?page=Registrardias&error=usuario"
    );

    exit();

}


/*=========================================
VERIFICAR QUE EL PERÍODO EXISTA
=========================================*/

$sqlPeriodo = $conexion->prepare("

    SELECT idPeriodo

    FROM periodosvac

    WHERE idPeriodo = :idPeriodo

    LIMIT 1

");

$sqlPeriodo->bindValue(
    ":idPeriodo",
    $idPeriodo,
    PDO::PARAM_INT
);

$sqlPeriodo->execute();


if(!$sqlPeriodo->fetch()){

    header(
        "Location: ../views/dashboard.php?page=Registrardias&error=periodo"
    );

    exit();

}


/*=========================================
VERIFICAR SI YA EXISTE REGISTRO
PARA ESE EMPLEADO Y PERÍODO
=========================================*/

$sqlExiste = $conexion->prepare("

    SELECT id

    FROM diasperiodos

    WHERE idUsuario = :idUsuario

    AND idPeriodo = :idPeriodo

    LIMIT 1

");

$sqlExiste->bindValue(
    ":idUsuario",
    $idUsuario,
    PDO::PARAM_INT
);

$sqlExiste->bindValue(
    ":idPeriodo",
    $idPeriodo,
    PDO::PARAM_INT
);

$sqlExiste->execute();


if($sqlExiste->fetch()){

    header(
        "Location: ../views/dashboard.php?page=Registrardias&error=duplicado"
    );

    exit();

}





/*=========================================
INSERTAR
=========================================*/

$sqlInsertar = $conexion->prepare("

    INSERT INTO diasperiodos(

        idUsuario,

        idPeriodo,

        diasDisponibles

    )

    VALUES(

        :idUsuario,

        :idPeriodo,

        :diasDisponibles

    )

");


$sqlInsertar->bindValue(
    ":idUsuario",
    $idUsuario,
    PDO::PARAM_INT
);

$sqlInsertar->bindValue(
    ":idPeriodo",
    $idPeriodo,
    PDO::PARAM_INT
);

$sqlInsertar->bindValue(
    ":diasDisponibles",
    $diasDisponibles
);

/*=====================================
    RECALCULAR VACACIONES DEL USUARIO
    =====================================*/

    $sqlTotal = $conexion->prepare("

        SELECT

            COALESCE(
                SUM(diasDisponibles),
                0
            ) AS total

        FROM diasperiodos

        WHERE idUsuario = :idUsuario

    ");


    $sqlTotal->bindValue(
        ":idUsuario",
        $idUsuario,
        PDO::PARAM_INT
    );


    $sqlTotal->execute();


    $resultadoTotal =
        $sqlTotal->fetch(PDO::FETCH_ASSOC);


    $nuevoTotal =
        (float)$resultadoTotal["total"];


    /*=====================================
    ACTUALIZAR USUARIOS
    =====================================*/

    $sqlUsuario = $conexion->prepare("

        UPDATE usuarios

        SET

            diasVacaciones = :diasVacaciones

        WHERE id = :idUsuario

    "); 




/*=========================================
EJECUTAR
=========================================*/

try{

    if($sqlInsertar->execute()){

        header(
            "Location: ../views/dashboard.php?page=Registrardias&guardado=1"
        );

        exit();

    }


}catch(PDOException $e){

    header(
        "Location: ../views/dashboard.php?page=Registrardias&error=1"
    );

    exit();

}


?>