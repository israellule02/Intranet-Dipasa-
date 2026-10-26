<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("../assets/phpmailer/src/Exception.php");
require_once("../assets/phpmailer/src/PHPMailer.php");
require_once("../assets/phpmailer/src/SMTP.php");

require_once("../config/conexion.php");


/*=========================================
FUNCIÓN PARA ENVIAR CORREO A RH
=========================================*/

function enviarCorreoVacacionesRH($idRegistro)
{

    global $conexion;


    /*=========================================
    OBTENER INFORMACIÓN DE LA SOLICITUD
    =========================================*/

    $sql = $conexion->prepare("

        SELECT

            r.idRegistro,

            CONCAT(u.nombre, ' ', u.apellidos) AS empleado

        FROM registrovac r

        INNER JOIN usuarios u
            ON u.id = r.idUsuario

        WHERE r.idRegistro = :idRegistro

        LIMIT 1

    ");

    $sql->execute([
        ":idRegistro" => $idRegistro
    ]);

    $solicitud = $sql->fetch(PDO::FETCH_ASSOC);


    /*=========================================
    VALIDAR SOLICITUD
    =========================================*/

    if (!$solicitud) {
        return false;
    }


    /*=========================================
    OBTENER ADMINISTRADORES DE RH
    idRol = 1
    =========================================*/

    $sqlRH = $conexion->prepare("

        SELECT

            id,

            CONCAT(nombre, ' ', apellidos) AS nombre,

            correo

        FROM usuarios

        WHERE idRol = 1

        AND correo IS NOT NULL

        AND correo <> ''

    ");

    $sqlRH->execute();

    $usuariosRH = $sqlRH->fetchAll(PDO::FETCH_ASSOC);


    /*=========================================
    SI NO HAY USUARIOS DE RH
    =========================================*/

    if (!$usuariosRH) {
        return false;
    }


    /*=========================================
    ENVIAR CORREO A CADA ADMINISTRADOR RH
    =========================================*/

    foreach ($usuariosRH as $rh) {

        try {

            $mail = new PHPMailer(true);


            /*=====================================
            CONFIGURACIÓN SMTP
            =====================================*/

            $mail->isSMTP();

            $mail->Host = "smtp.gmail.com";

            $mail->SMTPAuth = true;

            $mail->Username = "intranet.dipasa@gmail.com";

            $mail->Password = "xxta xuup nwde ltpd";

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            $mail->Port = 465;


            /*=====================================
            REMITENTE
            =====================================*/

            $mail->setFrom(
                "intranetDipasa@gmail.com",
                "Intranet Dipasa"
            );


            /*=====================================
            DESTINATARIO
            =====================================*/

            $mail->addAddress(
                $rh["correo"],
                $rh["nombre"]
            );


            /*=====================================
            ASUNTO
            =====================================*/

            $mail->Subject =
                "Solicitud de vacaciones pendiente de RH";


            /*=====================================
            CONTENIDO HTML
            =====================================*/

            $mail->isHTML(true);

            $mail->CharSet = "UTF-8";

            $mail->Body = "

                <div style='
                    font-family: Arial, sans-serif;
                    padding: 20px;
                '>

                    <h2 style='
                        color:#198754;
                    '>
                        Intranet Dipasa
                    </h2>

                    <p>
                        Hola
                        <strong>{$rh["nombre"]}</strong>.
                    </p>

                    <p>
                        La solicitud de vacaciones del colaborador
                        <strong>{$solicitud["empleado"]}</strong>
                        ha sido aceptada por su jefe.
                    </p>

                    <p>
                        La solicitud se encuentra pendiente de
                        autorización por Recursos Humanos.
                    </p>

                    <p>
                        Por favor, ingresa al sistema para
                        revisar y procesar la solicitud.
                    </p>

                    <br>

                    <p>
                        Saludos.
                    </p>

                    <p>
                        <strong>Intranet Dipasa</strong>
                    </p>

                </div>

            ";


            /*=====================================
            VERSIÓN TEXTO
            =====================================*/

            $mail->AltBody = "

                Hola {$rh["nombre"]}.

                La solicitud de vacaciones del colaborador
                {$solicitud["empleado"]}
                ha sido aceptada por su jefe.

                La solicitud se encuentra pendiente de
                autorización por Recursos Humanos.

                Por favor, ingresa al sistema para
                revisar y procesar la solicitud.

                Intranet Dipasa

            ";


            /*=====================================
            ENVIAR
            =====================================*/

            $mail->send();

        } catch (Exception $e) {

            /*
             * Si un correo falla, continúa con los demás
             * administradores de RH.
             */

            continue;

        }

    }


    return true;
}