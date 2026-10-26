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
        "Location: ../views/dashboard.php?page=vacaciones"
    );

    exit();

}


/*=========================================
ZONA HORARIA
=========================================*/

date_default_timezone_set("America/Mexico_City");


/*=========================================
OBTENER DATOS
=========================================*/

$idUsuario = $_SESSION["idUsuario"];

$fechainicio = $_POST["fechainicio"] ?? "";

$fechafinal = $_POST["fechafinal"] ?? "";

$idJefe = $_POST["idJefe"] ?? "";

$diasDescanso = $_POST["diasDescanso"] ?? [];

$diasSolicitud = $_POST["diasSolicitud"] ?? [];


/*=========================================
DATOS CORREO DE AUSENCIA
=========================================*/

/*
    Si el usuario marcó el checkbox:

    enviarCorreoAusencia = 1

    Si no lo marcó:

    enviarCorreoAusencia = 0
*/

$enviarCorreoAusencia =
    isset($_POST["enviarCorreoAusencia"])
    ? 1
    : 0;


/*
    Departamentos seleccionados.

    Ejemplo:

    areasAusencia[] = 3
    areasAusencia[] = 5
    areasAusencia[] = 8
*/

$areasAusencia =
    $_POST["areasAusencia"] ?? [];


/*=========================================
VALIDAR ARRAY DE DEPARTAMENTOS
=========================================*/

if(!is_array($areasAusencia)){

    $areasAusencia = [];

}


/*
    Convertir todos los IDs a entero
    y eliminar duplicados.
*/

$areasAusencia = array_map(
    "intval",
    $areasAusencia
);

$areasAusencia = array_unique(
    $areasAusencia
);

$areasAusencia = array_values(
    array_filter(
        $areasAusencia,
        function($id){

            return $id > 0;

        }
    )
);


/*=========================================
VALIDAR CORREO DE AUSENCIA
=========================================*/

/*
    Si se solicitó correo de ausencia,
    obligatoriamente debe existir
    al menos un departamento.
*/

if(

    $enviarCorreoAusencia == 1

    &&

    empty($areasAusencia)

){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=areas_ausencia"
    );

    exit();

}


/*=========================================
VALIDAR CAMPOS OBLIGATORIOS
=========================================*/

if(

    empty($fechainicio)

    ||

    empty($fechafinal)

    ||

    empty($idJefe)

){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=campos"
    );

    exit();

}


/*=========================================
VALIDAR QUE EXISTAN DÍAS SELECCIONADOS
=========================================*/

if(!is_array($diasSolicitud)){

    $diasSolicitud = [];

}


/*=========================================
FECHA DE SOLICITUD
=========================================*/

$fechaSolicitud = date("Y-m-d");

$fechaActual = date("Y-m-d");


/*=========================================
VALIDAR FECHA DE INICIO
=========================================*/

if($fechainicio < $fechaActual){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=pasado"
    );

    exit();

}


/*=========================================
VALIDAR FECHAS
=========================================*/

if($fechafinal <= $fechainicio){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=fechas"
    );

    exit();

}


/*=========================================
VALIDAR JEFE
=========================================*/

$sqlJefe = $conexion->prepare("

    SELECT id

    FROM usuarios

    WHERE id = :idJefe

    AND idRol = 5

");

$sqlJefe->bindValue(
    ":idJefe",
    $idJefe,
    PDO::PARAM_INT
);

$sqlJefe->execute();

$jefe = $sqlJefe->fetch(PDO::FETCH_ASSOC);


if(!$jefe){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=jefe"
    );

    exit();

}


/*=========================================
CONVERTIR DÍAS DE DESCANSO
=========================================*/

if(!is_array($diasDescanso)){

    $diasDescanso = [];

}

$diasDescanso = array_map(
    "intval",
    $diasDescanso
);

$diasDescanso = array_unique(
    $diasDescanso
);


/*=========================================
VALIDAR DÍAS DE DESCANSO
=========================================*/

$diasDescanso = array_values(
    array_filter(
        $diasDescanso,
        function($dia){

            return $dia >= 0 && $dia <= 6;

        }
    )
);


/*=========================================
CREAR OBJETOS DE FECHA
=========================================*/

try{

    $inicio = new DateTime($fechainicio);

    $final = new DateTime($fechafinal);

}catch(Exception $e){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=fechas"
    );

    exit();

}


/*=========================================
OBTENER DÍAS DISPONIBLES
=========================================*/

$sqlDiasDisponibles = $conexion->prepare("

    SELECT

        COALESCE(
            SUM(diasDisponibles),
            0
        ) AS diasDisponibles

    FROM diasperiodos

    WHERE idUsuario = :idUsuario

");

$sqlDiasDisponibles->bindValue(
    ":idUsuario",
    $idUsuario,
    PDO::PARAM_INT
);

$sqlDiasDisponibles->execute();

$resultadoDias =
    $sqlDiasDisponibles->fetch(PDO::FETCH_ASSOC);

$diasDisponibles =
    (float)$resultadoDias["diasDisponibles"];


/*=========================================
VALIDAR QUE TENGA DÍAS DISPONIBLES
=========================================*/

if($diasDisponibles <= 0){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=dias_insuficientes"
    );

    exit();

}


/*=========================================
VALIDAR DÍAS SOLICITADOS
=========================================*/

/*
    Aquí vamos a reconstruir en PHP
    exactamente qué fechas son válidas.

    IMPORTANTE:

    $fechafinal NO se incluye porque
    es el día de regreso.
*/

$diasValidos = [];


/*=========================================
RECORRER FECHAS
=========================================*/

$fechaTemporal = clone $inicio;


while($fechaTemporal < $final){

    $anio =
        $fechaTemporal->format("Y");

    $fecha =
        $fechaTemporal->format("Y-m-d");

    $diaSemana =
        intval(
            $fechaTemporal->format("w")
        );


    /*=====================================
    DÍAS FESTIVOS
    =====================================*/

    $diasFestivos = [

        $anio . "-01-01",

        $anio . "-02-02",

        $anio . "-03-16",

        $anio . "-05-01",

        $anio . "-09-16",

        $anio . "-11-16",

        $anio . "-12-25"

    ];


    /*=====================================
    VERIFICAR DESCANSO
    =====================================*/

    $esDiaDescanso =
        in_array(
            $diaSemana,
            $diasDescanso
        );


    /*=====================================
    VERIFICAR FESTIVO
    =====================================*/

    $esFestivo =
        in_array(
            $fecha,
            $diasFestivos
        );


    /*=====================================
    FECHA VÁLIDA
    =====================================*/

    if(

        !$esDiaDescanso

        &&

        !$esFestivo

    ){

        $diasValidos[$fecha] = true;

    }


    $fechaTemporal->modify("+1 day");

}


/*=========================================
VALIDAR QUE EXISTAN DÍAS VÁLIDOS
=========================================*/

if(empty($diasValidos)){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=sin_dias"
    );

    exit();

}


/*=========================================
VALIDAR Y PREPARAR DETALLE
=========================================*/

$detalleVacaciones = [];

$diasTomados = 0;


/*
    Recorremos únicamente lo que
    mandó el formulario.

    Esto evita que el usuario pueda
    mandar fechas inválidas manualmente.
*/

foreach($diasSolicitud as $fecha => $cantidad){

    /*=====================================
    VALIDAR FORMATO DE FECHA
    =====================================*/

    $fechaObjeto = DateTime::createFromFormat(
        "Y-m-d",
        $fecha
    );


    if(

        !$fechaObjeto

        ||

        $fechaObjeto->format("Y-m-d") !== $fecha

    ){

        continue;

    }


    /*=====================================
    VALIDAR QUE LA FECHA SEA VÁLIDA
    =====================================*/

    if(!isset($diasValidos[$fecha])){

        continue;

    }


    /*=====================================
    CONVERTIR CANTIDAD
    =====================================*/

    $cantidad = (float)$cantidad;


    /*=====================================
    SOLO SE PERMITEN:

    0
    0.5
    1
    =====================================*/

    if(

        $cantidad != 0

        &&

        $cantidad != 0.5

        &&

        $cantidad != 1

    ){

        continue;

    }


    /*=====================================
    SI ES 0 NO SE GUARDA
    =====================================*/

    if($cantidad == 0){

        continue;

    }


    /*=====================================
    GUARDAR DETALLE
    =====================================*/

    $detalleVacaciones[$fecha] =
        $cantidad;


    /*=====================================
    SUMAR TOTAL
    =====================================*/

    $diasTomados += $cantidad;

}


/*=========================================
REDONDEAR TOTAL
=========================================*/

$diasTomados =
    round(
        $diasTomados,
        1
    );


/*=========================================
VALIDAR QUE HAYA SELECCIÓN
=========================================*/

if(

    empty($detalleVacaciones)

    ||

    $diasTomados <= 0

){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=sin_seleccion"
    );

    exit();

}


/*=========================================
VALIDAR DÍAS DISPONIBLES
=========================================*/

if($diasTomados > $diasDisponibles){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=dias_insuficientes"
    );

    exit();

}


/*=========================================
OBTENER PERÍODO
=========================================*/

/*
    Se conserva tu lógica actual.

    El período solamente identifica
    el período asociado a la solicitud.

    El descuento real se realizará
    posteriormente cuando RH acepte.
*/

$sqlPeriodo = $conexion->prepare("

    SELECT

        diasperiodos.idPeriodo

    FROM diasperiodos

    LEFT JOIN periodosvac

        ON diasperiodos.idPeriodo =
           periodosvac.idPeriodo

    WHERE diasperiodos.diasDisponibles > 0

    AND diasperiodos.idUsuario = :idUsuario

    ORDER BY periodosvac.fechaInicio ASC

    LIMIT 1

");

$sqlPeriodo->bindValue(
    ":idUsuario",
    $idUsuario,
    PDO::PARAM_INT
);

$sqlPeriodo->execute();

$periodo =
    $sqlPeriodo->fetch(PDO::FETCH_ASSOC);


/*=========================================
VALIDAR PERÍODO
=========================================*/

if(!$periodo){

    header(
        "Location: ../views/dashboard.php?page=vacaciones&error=dias_insuficientes"
    );

    exit();

}


$idPeriodo =
    $periodo["idPeriodo"];


/*=========================================
VALIDACIONES PENDIENTES
=========================================*/

$idValidacionJefe = 2;

$idValidacionRh = 2;


/*=========================================
RH
=========================================*/

$idRh = null;


/*=========================================
INICIAR TRANSACCIÓN
=========================================*/

try{

    $conexion->beginTransaction();


    /*=====================================
    INSERTAR SOLICITUD
    =====================================*/

    $sql = $conexion->prepare("

        INSERT INTO registrovac(

            idUsuario,

            fechasolicitud,

            fechainicio,

            fechafinal,

            diastomados,

            idPeriodo,

            idJefe,

            idRh,

            idValidacionJefe,

            idValidacionRh

        )

        VALUES(

            :idUsuario,

            :fechasolicitud,

            :fechainicio,

            :fechafinal,

            :diastomados,

            :idPeriodo,

            :idJefe,

            :idRh,

            :idValidacionJefe,

            :idValidacionRh

        )

    ");


    /*=====================================
    DATOS SOLICITUD
    =====================================*/

    $sql->bindValue(
        ":idUsuario",
        $idUsuario,
        PDO::PARAM_INT
    );


    $sql->bindValue(
        ":fechasolicitud",
        $fechaSolicitud
    );


    $sql->bindValue(
        ":fechainicio",
        $fechainicio
    );


    $sql->bindValue(
        ":fechafinal",
        $fechafinal
    );


    /*
        IMPORTANTE:

        diastomados ahora puede ser:

        1
        1.5
        2
        2.5
        3
        etc.

        Por eso NO usamos PDO::PARAM_INT.
    */

    $sql->bindValue(
        ":diastomados",
        $diasTomados
    );


    $sql->bindValue(
        ":idPeriodo",
        $idPeriodo,
        PDO::PARAM_INT
    );


    $sql->bindValue(
        ":idJefe",
        $idJefe,
        PDO::PARAM_INT
    );


    $sql->bindValue(
        ":idRh",
        $idRh,
        PDO::PARAM_NULL
    );


    $sql->bindValue(
        ":idValidacionJefe",
        $idValidacionJefe,
        PDO::PARAM_INT
    );


    $sql->bindValue(
        ":idValidacionRh",
        $idValidacionRh,
        PDO::PARAM_INT
    );


    /*=====================================
    EJECUTAR SOLICITUD
    =====================================*/

    $sql->execute();


    /*=====================================
    OBTENER ID DEL REGISTRO
    =====================================*/

    $idRegistro =
        $conexion->lastInsertId();


    /*=====================================
    INSERTAR DETALLE
    =====================================*/

    $sqlDetalle = $conexion->prepare("

        INSERT INTO registrovac_detalle(

            idRegistro,

            fecha,

            cantidad

        )

        VALUES(

            :idRegistro,

            :fecha,

            :cantidad

        )

    ");


    /*=====================================
    RECORRER DETALLE
    =====================================*/

    foreach(
        $detalleVacaciones
        as $fecha => $cantidad
    ){

        $sqlDetalle->bindValue(
            ":idRegistro",
            $idRegistro,
            PDO::PARAM_INT
        );


        $sqlDetalle->bindValue(
            ":fecha",
            $fecha
        );


        $sqlDetalle->bindValue(
            ":cantidad",
            $cantidad
        );


        $sqlDetalle->execute();

    }


    /*=====================================
    GUARDAR DEPARTAMENTOS
    PARA CORREO DE AUSENCIA
    =====================================*/

    /*
        IMPORTANTE:

        Aquí NO enviamos ningún correo.

        Solamente guardamos qué departamentos
        deberán recibir el aviso cuando RH
        acepte posteriormente la solicitud.
    */

    if(

        $enviarCorreoAusencia == 1

        &&

        !empty($areasAusencia)

    ){

        /*=================================
        VALIDAR DEPARTAMENTOS EXISTENTES
        =================================*/

        $sqlDepartamento =
            $conexion->prepare("

                SELECT idDepartamento

                FROM departamentos

                WHERE idDepartamento = :idDepartamento

            ");


        /*=================================
        INSERTAR RELACIONES
        =================================*/

        $sqlAusencia =
            $conexion->prepare("

                INSERT INTO registrovac_ausencia(

                    idRegistro,

                    idDepartamento

                )

                VALUES(

                    :idRegistro,

                    :idDepartamento

                )

            ");


        foreach(

            $areasAusencia
            as $idDepartamento

        ){

            /*=============================
            VALIDAR DEPARTAMENTO
            =============================*/

            $sqlDepartamento->bindValue(
                ":idDepartamento",
                $idDepartamento,
                PDO::PARAM_INT
            );

            $sqlDepartamento->execute();

            $departamento =
                $sqlDepartamento->fetch(
                    PDO::FETCH_ASSOC
                );


            /*
                Si el departamento no existe,
                no se inserta.
            */

            if(!$departamento){

                continue;

            }


            /*=============================
            INSERTAR RELACIÓN
            =============================*/

            $sqlAusencia->bindValue(
                ":idRegistro",
                $idRegistro,
                PDO::PARAM_INT
            );


            $sqlAusencia->bindValue(
                ":idDepartamento",
                $idDepartamento,
                PDO::PARAM_INT
            );


            $sqlAusencia->execute();

        }

    }


    /*=====================================
    CONFIRMAR
    =====================================*/

    $conexion->commit();


    /*=========================================
    ENVIAR CORREO AL JEFE
    =========================================*/

    $ch = curl_init();

    curl_setopt(
        $ch,
        CURLOPT_URL,
        "http://10.81.254.223:8085/controllers/EnvioCorreoJefeController.php?idRegistro=" . $idRegistro
    );

    curl_setopt(
        $ch,
        CURLOPT_RETURNTRANSFER,
        true
    );

    curl_exec($ch);

    curl_close($ch);


    /*=====================================
    REDIRECCIONAR
    =====================================*/

    header(

        "Location: ../views/dashboard.php?page=vacaciones&solicitado=1"

    );

    exit();


}catch(Exception $e){

    /*=====================================
    DESHACER TODO
    =====================================*/

    if($conexion->inTransaction()){

        $conexion->rollBack();

    }


    /*
        Para desarrollo puedes descomentar
        temporalmente estas líneas:

        echo $e->getMessage();
        exit();
    */


    header(

        "Location: ../views/dashboard.php?page=vacaciones&error=1"

    );

    exit();

}

?>