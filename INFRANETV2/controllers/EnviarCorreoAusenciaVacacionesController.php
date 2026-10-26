<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/*=========================================
PHPMailer
=========================================*/

require_once("../assets/phpmailer/src/Exception.php");
require_once("../assets/phpmailer/src/PHPMailer.php");
require_once("../assets/phpmailer/src/SMTP.php");


/*=========================================
CONFIGURACIÓN DEL CORREO
=========================================*/

function configurarCorreo()
{
    $mail = new PHPMailer(true);

    /*=====================================
    SMTP
    =====================================*/

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;


    $mail->Username = 'intranet.dipasa@gmail.com';

    $mail->Password = 'xxta xuup nwde ltpd';



    $mail->SMTPSecure =
        PHPMailer::ENCRYPTION_SMTPS;

    $mail->Port = 465;


    /*=====================================
    REMITENTE
    =====================================*/

    $mail->setFrom(
        'intranetDipasa@gmail.com',
        'Intranet Dipasa'
    );


    /*=====================================
    CODIFICACIÓN
    =====================================*/

    $mail->CharSet = 'UTF-8';


    /*
        IMPORTANTE:

        Mantiene abierta la conexión SMTP
        para poder enviar varios correos
        sin reconectarse cada vez.
    */

    $mail->SMTPKeepAlive = true;


    return $mail;
}


/*=========================================================
FUNCIÓN
CORREO DE CONFIRMACIÓN AL EMPLEADO
=========================================================*/

function enviarCorreoConfirmacionVacaciones($idRegistro)
{

    global $conexion;


    /*=========================================
    OBTENER DATOS
    =========================================*/

    $sql = $conexion->prepare("

        SELECT

            r.idRegistro,

            r.idUsuario,

            r.fechainicio,

            r.fechafinal,

            r.diastomados,

            CONCAT(
                u.nombre,
                ' ',
                u.apellidos
            ) AS nombreCompleto,

            u.correo

        FROM registrovac r

        INNER JOIN usuarios u
            ON u.id = r.idUsuario

        WHERE r.idRegistro = :idRegistro

        LIMIT 1

    ");


    $sql->bindValue(
        ":idRegistro",
        $idRegistro,
        PDO::PARAM_INT
    );


    $sql->execute();


    $vacacion =
        $sql->fetch(PDO::FETCH_ASSOC);


    /*=========================================
    VALIDAR
    =========================================*/

    if(!$vacacion){

        return false;

    }


    if(
        empty($vacacion["correo"])
    ){

        return false;

    }


    try{

        /*=====================================
        CREAR SMTP
        =====================================*/

        $mail =
            configurarCorreo();


        /*=====================================
        DESTINATARIO
        =====================================*/

        $mail->addAddress(

            $vacacion["correo"],

            $vacacion["nombreCompleto"]

        );


        /*=====================================
        ASUNTO
        =====================================*/

        $mail->Subject =
            "Vacaciones aceptadas - Intranet Dipasa";


        /*=====================================
        HTML
        =====================================*/

        $mail->isHTML(true);


        $mail->Body = "

            <div style='
                font-family:Arial,sans-serif;
                max-width:650px;
                margin:auto;
                border:1px solid #ddd;
                border-radius:10px;
                overflow:hidden;
            '>

                <div style='
                    background:#198754;
                    color:#fff;
                    padding:20px;
                    text-align:center;
                '>

                    <h2 style='margin:0;'>
                        Vacaciones aceptadas
                    </h2>

                </div>


                <div style='padding:25px;'>

                    <p>
                        Hola
                        <strong>
                            {$vacacion["nombreCompleto"]}
                        </strong>.
                    </p>


                    <p>

                        Te informamos que tu solicitud
                        de vacaciones ha sido

                        <strong style='color:#198754;'>
                            aceptada por Recursos Humanos.
                        </strong>

                    </p>


                    <hr>
    

                    <p>
                        <strong>
                            Fecha de inicio:
                        </strong>

                        {$vacacion["fechainicio"]}

                    </p>


                    <p>
                        <strong>
                            Fecha de regreso:
                        </strong>

                        {$vacacion["fechafinal"]}

                    </p>


                    <p>
                        <strong>
                            Días tomados:
                        </strong>

                        {$vacacion["diastomados"]}

                    </p>


                    <hr>


                    <p style='color:#666;'>

                        Este correo fue generado
                        automáticamente por

                        <strong>
                            Intranet Dipasa
                        </strong>.

                    </p>

                </div>

            </div>

        ";


        /*=====================================
        TEXTO PLANO
        =====================================*/

        $mail->AltBody =

            "Hola "
            .
            $vacacion["nombreCompleto"]
            .
            ". Tu solicitud de vacaciones "
            .
            "ha sido aceptada por Recursos Humanos. "
            .
            "Fecha de inicio: "
            .
            $vacacion["fechainicio"]
            .
            ". Fecha de regreso: "
            .
            $vacacion["fechafinal"]
            .
            ". Días tomados: "
            .
            $vacacion["diastomados"]
            .
            ".";


        /*=====================================
        ENVIAR
        =====================================*/

        $mail->send();


        /*=====================================
        CERRAR CONEXIÓN
        =====================================*/

        $mail->smtpClose();


        return true;


    }catch(Exception $e){

        error_log(

            "Error correo confirmación vacaciones: "
            .
            $mail->ErrorInfo

        );


        return false;

    }

}


/*=========================================================
FUNCIÓN
CORREOS DE AUSENCIA
=========================================================*/

function enviarCorreoAusencia($idRegistro)
{

    global $conexion;


    /*=========================================
    OBTENER INFORMACIÓN DE VACACIONES
    =========================================*/

    $sql = $conexion->prepare("

        SELECT

            r.idRegistro,

            r.fechainicio,

            r.fechafinal,

            r.diastomados,

            CONCAT(
                u.nombre,
                ' ',
                u.apellidos
            ) AS nombreCompleto

        FROM registrovac r

        INNER JOIN usuarios u
            ON u.id = r.idUsuario

        WHERE r.idRegistro = :idRegistro

        LIMIT 1

    ");


    $sql->bindValue(
        ":idRegistro",
        $idRegistro,
        PDO::PARAM_INT
    );


    $sql->execute();


    $vacacion =
        $sql->fetch(PDO::FETCH_ASSOC);


    /*=========================================
    VALIDAR REGISTRO
    =========================================*/

    if(!$vacacion){

        return false;

    }


    /*=========================================
    OBTENER DEPARTAMENTOS
    =========================================*/

    $sqlDepartamentos = $conexion->prepare("

        SELECT DISTINCT

            d.idDepartamento,

            d.nombre

        FROM registrovac_ausencia ra

        INNER JOIN departamentos d

            ON d.idDepartamento =
               ra.idDepartamento

        WHERE ra.idRegistro = :idRegistro

        ORDER BY d.nombre ASC

    ");


    $sqlDepartamentos->bindValue(
        ":idRegistro",
        $idRegistro,
        PDO::PARAM_INT
    );


    $sqlDepartamentos->execute();


    $departamentos =
        $sqlDepartamentos->fetchAll(
            PDO::FETCH_ASSOC
        );


    /*=========================================
    NO HAY DEPARTAMENTOS
    =========================================*/

    if(empty($departamentos)){

        return true;

    }


    /*=========================================
    OBTENER IDS
    =========================================*/

    $idsDepartamentos = [];


    foreach($departamentos as $departamento){

        $idsDepartamentos[] =
            (int)$departamento["idDepartamento"];

    }


    /*=========================================
    CREAR PLACEHOLDERS
    =========================================*/

    $placeholders = implode(
        ",",
        array_fill(
            0,
            count($idsDepartamentos),
            "?"
        )
    );


    /*=========================================
    OBTENER USUARIOS

    UNA SOLA CONSULTA
    =========================================*/

    $sqlUsuarios = $conexion->prepare("

        SELECT DISTINCT

            u.id,

            u.nombre,

            u.apellidos,

            u.correo,

            d.nombre AS departamento

        FROM usuarios u

        INNER JOIN departamentos d

            ON d.idDepartamento = u.idDep

        WHERE u.idDep IN ($placeholders)

        AND u.correo IS NOT NULL

        AND TRIM(u.correo) <> ''

        ORDER BY

            u.nombre ASC,

            u.apellidos ASC

    ");


    $sqlUsuarios->execute(
        $idsDepartamentos
    );


    $usuarios =
        $sqlUsuarios->fetchAll(
            PDO::FETCH_ASSOC
        );


    /*=========================================
    NO HAY DESTINATARIOS
    =========================================*/

    if(empty($usuarios)){

        return true;

    }


    /*=========================================
    CREAR UNA SOLA CONEXIÓN SMTP
    =========================================*/

    try{

        $mail =
            configurarCorreo();


        /*
            Abrimos la conexión antes
            del ciclo.

            Así PHPMailer no tiene que
            autenticarse nuevamente para
            cada empleado.
        */

        $mail->smtpConnect();


    }catch(Exception $e){

        error_log(

            "No se pudo conectar al SMTP: "
            .
            $mail->ErrorInfo

        );

        return false;

    }


    /*=========================================
    ENVIAR A TODOS LOS USUARIOS
    =========================================*/

    foreach($usuarios as $usuario){

        try{

            /*=============================
            LIMPIAR DESTINATARIOS
            =============================*/

            $mail->clearAddresses();


            /*=============================
            LIMPIAR ADJUNTOS
            =============================*/

            $mail->clearAttachments();


            /*=============================
            DESTINATARIO
            =============================*/

            $mail->addAddress(

                $usuario["correo"],

                $usuario["nombre"]
                .
                " "
                .
                $usuario["apellidos"]

            );


            /*=============================
            ASUNTO
            =============================*/

            $mail->Subject =
                "Aviso de ausencia - Intranet Dipasa";


            /*=============================
            HTML
            =============================*/

            $mail->isHTML(true);


            $mail->Body = "

                <div style='
                    font-family:Arial,sans-serif;
                    max-width:650px;
                    margin:auto;
                    border:1px solid #ddd;
                    border-radius:10px;
                    overflow:hidden;
                '>

                    <div style='
                        background:#198754;
                        color:#fff;
                        padding:20px;
                        text-align:center;
                    '>

                        <h2 style='margin:0;'>
                            Aviso de ausencia
                        </h2>

                    </div>


                    <div style='padding:25px;'>

                        <p>

                            Hola
                            <strong>
                                {$usuario["nombre"]}
                                {$usuario["apellidos"]}
                            </strong>.

                        </p>


                        <p>

                            Te informamos que el empleado:

                        </p>


                        <p style='
                            font-size:18px;
                            font-weight:bold;
                        '>

                            {$vacacion["nombreCompleto"]}

                        </p>


                        <p>

                            se encontrará ausente
                            por motivo de vacaciones.
                            cualquier tema pendiente, se quedan atentos sus compañeros de area 

                        </p>


                        <hr>


                        <p>

                            <strong>
                                Inicio de vacaciones:
                            </strong>

                            {$vacacion["fechainicio"]}

                        </p>


                        <p>

                            <strong>
                                Fecha de regreso:
                            </strong>

                            {$vacacion["fechafinal"]}

                        </p>


                        <p>

                            <strong>
                                Días tomados:
                            </strong>

                            {$vacacion["diastomados"]}

                        </p>


                        <hr>


                        <p style='color:#666;'>

                            Este aviso fue generado
                            automáticamente por

                            <strong>
                                Intranet Dipasa
                            </strong>.

                        </p>

                    </div>

                </div>

            ";


            /*=============================
            TEXTO PLANO
            =============================*/

            $mail->AltBody =

                "Aviso de ausencia. "
                .
                "El empleado "
                .
                $vacacion["nombreCompleto"]
                .
                " estará ausente por vacaciones. "
                .
                "Inicio: "
                .
                $vacacion["fechainicio"]
                .
                ". Regreso: "
                .
                $vacacion["fechafinal"]
                .
                ". Días tomados: "
                .
                $vacacion["diastomados"]
                .
                ".";


            /*=============================
            ENVIAR
            =============================*/

            $mail->send();


        }catch(Exception $e){

            /*
                Si un correo falla,
                NO detenemos los demás.
            */

            error_log(

                "Error enviando correo de ausencia a "
                .
                $usuario["correo"]
                .
                ": "
                .
                $mail->ErrorInfo

            );

        }

    }


    /*=========================================
    CERRAR UNA SOLA CONEXIÓN SMTP
    =========================================*/

    $mail->smtpClose();


    return true;

}

?>