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
VALIDAR MÉTODO POST
=========================================*/

if($_SERVER["REQUEST_METHOD"] != "POST"){

    header(
        "Location: ../views/dashboard.php?page=solicitudesVacacionesRH&error=metodo"
    );

    exit();

}


/*=========================================
DATOS DE RH
=========================================*/

$idRh = $_SESSION["idUsuario"];

$idRegistro = $_POST["idRegistro"] ?? "";

$accion = $_POST["accion"] ?? "";


/*=========================================
VALIDAR DATOS
=========================================*/

if(empty($idRegistro) || empty($accion)){

    header(
        "Location: ../views/dashboard.php?page=solicitudesVacacionesRH&error=datos"
    );

    exit();

}


/*=========================================
VALIDAR ACCIÓN
=========================================*/

if(
    $accion != "aceptar" &&
    $accion != "rechazar"
){

    header(
        "Location: ../views/dashboard.php?page=solicitudesVacacionesRH&error=accion"
    );

    exit();

}


/*=========================================
SI RH RECHAZA
=========================================*/

/*
IMPORTANTE:

Al rechazar NO se descuentan vacaciones.
Solamente se cambia:

idValidacionRH = 3

y se guarda quién realizó la acción
en idRh.
*/

/*=========================================
SI RH RECHAZA
=========================================*/

if($accion == "rechazar"){

    try{

        /*
        =========================================
        BUSCAR LA SOLICITUD
        =========================================
        */

        $sqlBuscar = $conexion->prepare("

            SELECT
                idRegistro,
                idValidacionJefe,
                idValidacionRH

            FROM registrovac

            WHERE idRegistro = :idRegistro

        ");

        $sqlBuscar->bindValue(
            ":idRegistro",
            $idRegistro,
            PDO::PARAM_INT
        );

        $sqlBuscar->execute();

        $solicitud = $sqlBuscar->fetch(PDO::FETCH_ASSOC);


        /*
        =========================================
        VALIDAR QUE EXISTA
        =========================================
        */

        if(!$solicitud){

            header(
                "Location: ../views/dashboard.php?page=SolicitudesVacacionesRH&error=no_existe"
            );

            exit();

        }


        /*
        =========================================
        VALIDAR QUE EL JEFE YA HAYA ACEPTADO
        =========================================
        */

        if((int)$solicitud["idValidacionJefe"] !== 1){

            header(
                "Location: ../views/dashboard.php?page=SolicitudesVacacionesRH&error=jefe_pendiente"
            );

            exit();

        }


        /*
        =========================================
        VALIDAR QUE RH TODAVÍA NO LA HAYA PROCESADO
        =========================================
        */

        if((int)$solicitud["idValidacionRH"] !== 2){

            header(
                "Location: ../views/dashboard.php?page=SolicitudesVacacionesRH&error=ya_procesada"
            );

            exit();

        }


        /*
        =========================================
        RECHAZAR SOLICITUD
        =========================================

        IMPORTANTE:

        NO SE DESCUENTAN DÍAS.

        NO SE MODIFICA diasperiodos.

        NO SE MODIFICA usuarios.diasVacaciones.

        SOLAMENTE:

        idValidacionRH = 3
        idRh = usuario que rechazó
        */

        $sqlRechazar = $conexion->prepare("

            UPDATE registrovac

            SET

                idValidacionRH = 3,

                idRh = :idRh

            WHERE idRegistro = :idRegistro

            AND idValidacionRH = 2

        ");


        $sqlRechazar->bindValue(
            ":idRh",
            $idRh,
            PDO::PARAM_INT
        );


        $sqlRechazar->bindValue(
            ":idRegistro",
            $idRegistro,
            PDO::PARAM_INT
        );


        $sqlRechazar->execute();


        /*
        =========================================
        VERIFICAR ACTUALIZACIÓN
        =========================================
        */

        if($sqlRechazar->rowCount() <= 0){

            header(
                "Location: ../views/dashboard.php?page=SolicitudesVacacionesRH&error=ya_procesada"
            );

            exit();

        }


        /*
        =========================================
        REDIRECCIÓN
        =========================================
        */

        header(
            "Location: ../views/dashboard.php?page=SolicitudesVacacionesRH&rechazado=1"
        );

        exit();


    }catch(PDOException $e){

        header(
            "Location: ../views/dashboard.php?page=SolicitudesVacacionesRH&error=bd"
        );

        exit();

    }

}


/*=========================================
RH ACEPTA
=========================================*/

try{


    /*=====================================
    INICIAR TRANSACCIÓN
    =====================================*/

    $conexion->beginTransaction();


    /*=====================================
    OBTENER SOLICITUD
    =====================================*/

    $sqlSolicitud = $conexion->prepare("

        SELECT

            r.idRegistro,

            r.idUsuario,

            r.diastomados,

            r.idValidacionJefe,

            r.idValidacionRH

        FROM registrovac r

        WHERE r.idRegistro = :idRegistro

        AND r.idValidacionJefe = 1

        AND r.idValidacionRH = 2

        FOR UPDATE

    ");


    $sqlSolicitud->bindValue(
        ":idRegistro",
        $idRegistro,
        PDO::PARAM_INT
    );


    $sqlSolicitud->execute();


    $solicitud =
        $sqlSolicitud->fetch(PDO::FETCH_ASSOC);


    /*=====================================
    VALIDAR SOLICITUD
    =====================================*/

    if(!$solicitud){

        throw new Exception(
            "La solicitud no existe o ya fue procesada."
        );

    }


    /*=====================================
    DATOS DE LA SOLICITUD
    =====================================*/

    $idUsuario =
        $solicitud["idUsuario"];


    $diasSolicitados =
        (float)$solicitud["diastomados"];


    /*=====================================
    VALIDAR DÍAS
    =====================================*/

    if($diasSolicitados <= 0){

        throw new Exception(
            "La solicitud no tiene días válidos."
        );

    }


    /*=====================================
    OBTENER REGISTROS DE DÍAS
    =====================================*/

    /*
    Se ordenan por fecha de inicio
    del período más antiguo al más nuevo.

    De esta manera se consumen primero
    las vacaciones más antiguas.
    */

    $sqlDias = $conexion->prepare("

        SELECT

            dp.id,

            dp.idUsuario,

            dp.idPeriodo,

            dp.diasDisponibles,

            p.fechaInicio,

            p.fechaFinal

        FROM diasperiodos dp

        INNER JOIN periodosvac p

            ON dp.idPeriodo = p.idPeriodo

        WHERE dp.idUsuario = :idUsuario

        AND dp.diasDisponibles > 0

        ORDER BY

            p.fechaInicio ASC,

            dp.id ASC

        FOR UPDATE

    ");


    $sqlDias->bindValue(
        ":idUsuario",
        $idUsuario,
        PDO::PARAM_INT
    );


    $sqlDias->execute();


    $registrosDias =
        $sqlDias->fetchAll(PDO::FETCH_ASSOC);


    /*=====================================
    CALCULAR TOTAL DISPONIBLE
    =====================================*/

    $totalDisponible = 0;


    foreach($registrosDias as $registro){

        $totalDisponible +=
            (float)$registro["diasDisponibles"];

    }


    /*=====================================
    VALIDAR QUE ALCANCE
    =====================================*/

    if($totalDisponible < $diasSolicitados){

        throw new Exception(
            "El empleado no tiene suficientes días disponibles."
        );

    }


    /*=====================================
    DESCONTAR DÍAS
    =====================================*/

    $faltanPorDescontar =
        $diasSolicitados;


    foreach($registrosDias as $registro){


        /*=================================
        SI YA TERMINAMOS
        =================================*/

        if($faltanPorDescontar <= 0){

            break;

        }


        /*=================================
        DÍAS DEL PERÍODO
        =================================*/

        $diasDisponibles =
            (float)$registro["diasDisponibles"];


        /*=================================
        DETERMINAR DESCUENTO
        =================================*/

        if(
            $diasDisponibles
            <=
            $faltanPorDescontar
        ){

            /*
            Consumimos todo el período.
            */

            $nuevoDisponible = 0;

            $faltanPorDescontar -=
                $diasDisponibles;

        }else{

            /*
            Consumimos solamente
            lo que hace falta.
            */

            $nuevoDisponible =
                $diasDisponibles
                -
                $faltanPorDescontar;

            $faltanPorDescontar = 0;

        }


        /*=================================
        ACTUALIZAR PERÍODO
        =================================*/

        $sqlActualizarDias = $conexion->prepare("

            UPDATE diasperiodos

            SET

                diasDisponibles = :diasDisponibles

            WHERE id = :id

        ");


        $sqlActualizarDias->bindValue(
            ":diasDisponibles",
            $nuevoDisponible
        );


        $sqlActualizarDias->bindValue(
            ":id",
            $registro["id"],
            PDO::PARAM_INT
        );


        $sqlActualizarDias->execute();

    }


    /*=====================================
    VERIFICAR QUE SE DESCONTÓ TODO
    =====================================*/

    if($faltanPorDescontar > 0){

        throw new Exception(
            "No fue posible descontar todos los días."
        );

    }


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


    $sqlUsuario->bindValue(
        ":diasVacaciones",
        $nuevoTotal
    );


    $sqlUsuario->bindValue(
        ":idUsuario",
        $idUsuario,
        PDO::PARAM_INT
    );


    $sqlUsuario->execute();


    /*=====================================
    ACTUALIZAR SOLICITUD
    =====================================*/

    $sqlAceptar = $conexion->prepare("

        UPDATE registrovac

        SET

            idValidacionRH = 1,

            idRh = :idRh

        WHERE idRegistro = :idRegistro

        AND idValidacionJefe = 1

        AND idValidacionRH = 2

    ");


    $sqlAceptar->bindValue(
        ":idRh",
        $idRh,
        PDO::PARAM_INT
    );


    $sqlAceptar->bindValue(
        ":idRegistro",
        $idRegistro,
        PDO::PARAM_INT
    );


    $sqlAceptar->execute();


    /*=====================================
    VERIFICAR ACTUALIZACIÓN
    =====================================*/

    if($sqlAceptar->rowCount() <= 0){

        throw new Exception(
            "La solicitud ya fue procesada."
        );

    }


    /*=====================================
    CONFIRMAR TRANSACCIÓN
    =====================================*/

    $conexion->commit();



    require_once(__DIR__ . "/EnviarCorreoAusenciaVacacionesController.php");

    enviarCorreoConfirmacionVacaciones($idRegistro);

    enviarCorreoAusencia($idRegistro);


    /*=====================================
    REDIRECCIÓN
    =====================================*/

    header(
        "Location: ../views/dashboard.php?page=SolicitudesVacacionesRH&aceptado=1"
    );

    exit();


}catch (Exception $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    die("
        <div style='font-family:Arial;padding:30px'>
            <h2 style='color:red'>ERROR EN AceptarVacacionesRHController.php</h2>
            <pre style='background:#f5f5f5;padding:20px;border:1px solid #ccc;white-space:pre-wrap;'>"
            . htmlspecialchars($e->getMessage()) .
            "</pre>
        </div>
    ");
}

?>