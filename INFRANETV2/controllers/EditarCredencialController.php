<?php

session_start();

require_once("../config/conexion.php");


/*=========================================
    CLAVE DE CIFRADO
=========================================*/

$CLAVE_CIFRADO =
    "INFRANETV2_CLAVE_SEGURA_2026_123456789";


/*=========================================
    FUNCIÓN PARA CIFRAR
=========================================*/

function cifrarContrasena($texto, $clave)
{

    $iv =
        openssl_random_pseudo_bytes(
            16
        );


    $cifrado =
        openssl_encrypt(

            $texto,

            "AES-256-CBC",

            hash(
                "sha256",
                $clave,
                true
            ),

            OPENSSL_RAW_DATA,

            $iv

        );


    return base64_encode(

        $iv . $cifrado

    );

}


/*=========================================
    VALIDAR MÉTODO
=========================================*/

if(
    $_SERVER["REQUEST_METHOD"]
    !==
    "POST"
){

    header(
        "Location: ../views/dashboard.php?page=consultarCredenciales"
    );

    exit();

}


/*=========================================
    RECIBIR ID
=========================================*/

$id =
    isset($_POST["id"])
    ?
    intval($_POST["id"])
    :
    0;


/*=========================================
    VALIDAR ID
=========================================*/

if($id <= 0){

    echo "

    <script>

        alert(
            'Credencial no válida.'
        );

        window.location.href =
            '../views/dashboard.php?page=consultarCredenciales';

    </script>

    ";

    exit();

}


/*=========================================
    RECIBIR DATOS
=========================================*/

$identificador =
    isset($_POST["identificador"])
    ?
    trim($_POST["identificador"])
    :
    "";


$idTipoDeCredencial =
    isset($_POST["idTipoDeCredencial"])
    ?
    intval($_POST["idTipoDeCredencial"])
    :
    0;


$idDispositivo =
    isset($_POST["idDispositivo"])
    ?
    intval($_POST["idDispositivo"])
    :
    0;


$nuevaContrasena =
    isset($_POST["contrasena"])
    ?
    trim($_POST["contrasena"])
    :
    "";


/*=========================================
    VALIDAR IDENTIFICADOR
=========================================*/




/*=========================================
    VALIDAR TIPO
=========================================*/

if($idTipoDeCredencial <= 0){

    echo "

    <script>

        alert(
            'Debes seleccionar un tipo de credencial.'
        );

        history.back();

    </script>

    ";

    exit();

}


/*=========================================
    VERIFICAR SI LA CREDENCIAL EXISTE
=========================================*/

$sqlExiste =
    $conexion->prepare("

        SELECT

            id,

            `contraseña`

        FROM credenciales

        WHERE id = :id

    ");


$sqlExiste->bindParam(
    ":id",
    $id,
    PDO::PARAM_INT
);


$sqlExiste->execute();


$credencial =
    $sqlExiste->fetch(
        PDO::FETCH_ASSOC
    );


if(!$credencial){

    echo "

    <script>

        alert(
            'La credencial no existe.'
        );

        window.location.href =
            '../views/dashboard.php?page=consultarCredenciales';

    </script>

    ";

    exit();

}


/*=========================================
    VERIFICAR AUTORIZACIÓN
=========================================*/

$autorizada = false;


if(
    isset(
        $_SESSION[
            "credencial_autorizada"
        ][$id]
    )
    &&
    $_SESSION[
        "credencial_autorizada"
    ][$id] === true
){

    $autorizada = true;

}


/*=========================================
    VALIDAR CONTRASEÑA
=========================================*/

/*
 * Si el campo viene vacío:
 *
 * NO se modifica la contraseña actual.
 *
 * Si el usuario escribió una contraseña nueva:
 *
 * DEBE haber autorizado previamente.
 */

if(
    $nuevaContrasena !== ""
){

    if(!$autorizada){

        echo "

        <script>

            alert(
                'Debes autorizar la contraseña antes de modificarla.'
            );

            history.back();

        </script>

        ";

        exit();

    }

}


/*=========================================
    VALIDAR DISPOSITIVO
=========================================*/

/*
 * Si viene vacío se guarda NULL.
 */

if(
    $idDispositivo <= 0
){

    $idDispositivo = null;

}


/*=========================================
    ACTUALIZAR
=========================================*/

try{

    $conexion->beginTransaction();


    /*=====================================
        SI CAMBIÓ CONTRASEÑA
    =====================================*/

    if(
        $nuevaContrasena !== ""
    ){

        /*
         * Cifrar nueva contraseña
         */

        $contrasenaCifrada =
            cifrarContrasena(

                $nuevaContrasena,

                $CLAVE_CIFRADO

            );


        /*
         * UPDATE incluyendo contraseña
         */

        $sqlUpdate =
            $conexion->prepare("

                UPDATE credenciales

                SET

                    identificador =
                        :identificador,

                    idTipoDeCredencial =
                        :idTipoDeCredencial,

                    idDispositivo =
                        :idDispositivo,

                    `contraseña` =
                        :contrasena

                WHERE id = :id

            ");


        $sqlUpdate->bindValue(
            ":identificador",
            $identificador,
            PDO::PARAM_STR
        );


        $sqlUpdate->bindValue(
            ":idTipoDeCredencial",
            $idTipoDeCredencial,
            PDO::PARAM_INT
        );


        if($idDispositivo === null){

            $sqlUpdate->bindValue(
                ":idDispositivo",
                null,
                PDO::PARAM_NULL
            );

        }
        else{

            $sqlUpdate->bindValue(
                ":idDispositivo",
                $idDispositivo,
                PDO::PARAM_INT
            );

        }


        $sqlUpdate->bindValue(
            ":contrasena",
            $contrasenaCifrada,
            PDO::PARAM_STR
        );


        $sqlUpdate->bindValue(
            ":id",
            $id,
            PDO::PARAM_INT
        );


        $sqlUpdate->execute();

    }


    /*=====================================
        SI NO CAMBIÓ CONTRASEÑA
    =====================================*/

    else{

        /*
         * UPDATE sin tocar contraseña
         */

        $sqlUpdate =
            $conexion->prepare("

                UPDATE credenciales

                SET

                    identificador =
                        :identificador,

                    idTipoDeCredencial =
                        :idTipoDeCredencial,

                    idDispositivo =
                        :idDispositivo

                WHERE id = :id

            ");


        $sqlUpdate->bindValue(
            ":identificador",
            $identificador,
            PDO::PARAM_STR
        );


        $sqlUpdate->bindValue(
            ":idTipoDeCredencial",
            $idTipoDeCredencial,
            PDO::PARAM_INT
        );


        if($idDispositivo === null){

            $sqlUpdate->bindValue(
                ":idDispositivo",
                null,
                PDO::PARAM_NULL
            );

        }
        else{

            $sqlUpdate->bindValue(
                ":idDispositivo",
                $idDispositivo,
                PDO::PARAM_INT
            );

        }


        $sqlUpdate->bindValue(
            ":id",
            $id,
            PDO::PARAM_INT
        );


        $sqlUpdate->execute();

    }


    /*=====================================
        CONFIRMAR TRANSACCIÓN
    =====================================*/

    $conexion->commit();


    /*=====================================
        ELIMINAR AUTORIZACIÓN
    =====================================*/

    if(
        isset(
            $_SESSION[
                "credencial_autorizada"
            ][$id]
        )
    ){

        unset(
            $_SESSION[
                "credencial_autorizada"
            ][$id]
        );

    }


    /*=====================================
        REDIRECCIÓN
    =====================================*/

    echo "

    <script>

        alert(
            'Credencial actualizada correctamente.'
        );

        window.location.href =
            '../views/dashboard.php?page=consultarCredenciales';

    </script>

    ";

    exit();


}


/*=========================================
    ERROR
=========================================*/

catch(
    PDOException $e
){

    if(
        $conexion->inTransaction()
    ){

        $conexion->rollBack();

    }


    echo "

    <script>

        alert(
            'Error al actualizar la credencial: "
            .
            addslashes(
                $e->getMessage()
            )
            .
            "'
        );

        history.back();

    </script>

    ";

    exit();

}

?>