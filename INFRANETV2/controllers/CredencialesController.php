<?php

session_start();

require_once("../config/conexion.php");

/*=========================================
    CLAVE DE CIFRADO
=========================================*/

/*
 * IMPORTANTE:
 * NO cambies esta clave después de guardar credenciales,
 * porque las credenciales anteriores ya no podrían
 * descifrarse.
 *
 * Lo recomendable posteriormente es moverla a un archivo
 * de configuración fuera de las carpetas públicas.
 */

$CLAVE_CIFRADO = "INFRANETV2_CLAVE_SEGURA_2026_123456789";


/*=========================================
    FUNCIONES DE CIFRADO
=========================================*/

function cifrarContrasena($contrasena, $clave)
{

    $iv = random_bytes(16);

    $cifrado = openssl_encrypt(
        $contrasena,
        "AES-256-CBC",
        hash("sha256", $clave, true),
        OPENSSL_RAW_DATA,
        $iv
    );

    return base64_encode($iv . $cifrado);
}


/*=========================================
    VALIDAR MÉTODO
=========================================*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../views/dashboard.php?page=crearCredencial");
    exit();

}


/*=========================================
    RECIBIR DATOS
=========================================*/

$identificador = isset($_POST["identificador"])
    ? trim($_POST["identificador"])
    : "";

$idDispositivo = isset($_POST["idDispositivo"]) &&
                 $_POST["idDispositivo"] !== ""
    ? intval($_POST["idDispositivo"])
    : null;

$contrasena = isset($_POST["contraseña"])
    ? $_POST["contraseña"]
    : "";

$idTipoDeCredencial = isset($_POST["idTipoDeCredencial"])
    ? intval($_POST["idTipoDeCredencial"])
    : 0;


/*=========================================
    VALIDACIONES
=========================================*/



if ($contrasena === "") {

    die("La contraseña es obligatoria.");

}

if ($idTipoDeCredencial <= 0) {

    die("Debe seleccionar un tipo de credencial.");

}


/*=========================================
    VALIDAR DISPOSITIVO
=========================================*/

if ($idDispositivo !== null) {

    $buscarDispositivo = $conexion->prepare("

        SELECT id

        FROM dispositivos

        WHERE id = ?

    ");

    $buscarDispositivo->execute([
        $idDispositivo
    ]);

    if (!$buscarDispositivo->fetch()) {

        die("El dispositivo seleccionado no existe.");

    }

}


/*=========================================
    CIFRAR CONTRASEÑA
=========================================*/

$contrasenaCifrada = cifrarContrasena(
    $contrasena,
    $CLAVE_CIFRADO
);


/*=========================================
    INSERTAR
=========================================*/

try {

    /*
     * Se utilizan ? para evitar el error HY093.
     */

    $sql = $conexion->prepare("

        INSERT INTO credenciales
        (
            identificador,
            idDispositivo,
            `contraseña`,
            idTipoDeCredencial
        )

        VALUES
        (
            ?,
            ?,
            ?,
            ?
        )

    ");

    $sql->execute([

        $identificador,
        $idDispositivo,
        $contrasenaCifrada,
        $idTipoDeCredencial

    ]);


    /*=========================================
        OBTENER ID
    =========================================*/

    $idCredencial = $conexion->lastInsertId();


    /*=========================================
        REDIRECCIONAR
    =========================================*/

    header(
        "Location: ../views/dashboard.php?page=detalleCredencial&id="
        . $idCredencial
    );

    exit();


} catch (PDOException $e) {

    echo "<div style='
        margin:40px;
        padding:25px;
        border:1px solid #dc3545;
        border-radius:10px;
        font-family:Arial;
        background:#f8d7da;
        color:#842029;
    '>";

    echo "<h3>Error al registrar la credencial</h3>";

    echo "<pre>";

    echo htmlspecialchars(
        $e->getMessage()
    );

    echo "</pre>";

    echo "</div>";

    exit();

}