<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once("../assets/phpmailer/src/Exception.php");
require_once("../assets/phpmailer/src/PHPMailer.php");
require_once("../assets/phpmailer/src/SMTP.php");

require_once("../config/conexion.php");


/*=========================================
VALIDAR ID DE SOLICITUD
=========================================*/

if (!isset($_GET["idRegistro"]) || empty($_GET["idRegistro"])) {
    exit("No se recibió el ID de la solicitud.");
}

$idRegistro = intval($_GET["idRegistro"]);


/*=========================================
OBTENER JEFE Y EMPLEADO
=========================================*/

$sql = $conexion->prepare("

    SELECT

        CONCAT(u.nombre, ' ', u.apellidos) AS empleado,

        CONCAT(j.nombre, ' ', j.apellidos) AS jefe,

        j.correo AS correoJefe

    FROM registrovac r

    INNER JOIN usuarios u
        ON u.id = r.idUsuario

    INNER JOIN usuarios j
        ON j.id = r.idjefe

    WHERE r.idRegistro = :idRegistro

    LIMIT 1

");

$sql->execute([
    ":idRegistro" => $idRegistro
]);

$datos = $sql->fetch(PDO::FETCH_ASSOC);


/*=========================================
VALIDAR INFORMACIÓN
=========================================*/

if (!$datos) {
    exit("No se encontró la solicitud.");
}

if (empty($datos["correoJefe"])) {
    exit("El jefe no tiene un correo registrado.");
}


/*=========================================
CONFIGURAR PHPMailer
=========================================*/

$mail = new PHPMailer(true);

try {

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
        $datos["correoJefe"],
        $datos["jefe"]
    );


    /*=====================================
    CORREO
    =====================================*/

    $mail->isHTML(true);

    $mail->CharSet = "UTF-8";

    $mail->Subject = "Nueva solicitud de vacaciones";

    $mail->Body = "

        <div style='
            font-family: Arial, sans-serif;
            padding: 20px;
        '>

            <h2 style='color:#198754;'>
                Intranet Dipasa
            </h2>

            <p>
                Hola <strong>{$datos["jefe"]}</strong>.
            </p>

            <p>
                El colaborador
                <strong>{$datos["empleado"]}</strong>
                ha realizado una nueva solicitud de vacaciones.
            </p>

            <p>
                Por favor, ingresa ala plataforma para revisar
                y autorizar la solicitud.
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

    $mail->AltBody = "
        Hola {$datos["jefe"]}.

        El colaborador {$datos["empleado"]}
        ha realizado una nueva solicitud de vacaciones.

        Por favor, ingresa ala intranet para revisar
        y autorizar la solicitud.

        Intranet Dipasa
    ";

     
    /*=====================================
    ENVIAR
    =====================================*/

    $mail->send();

   

} catch (Exception $e) {

    echo "Error al enviar el correo: "
        . $mail->ErrorInfo;

}