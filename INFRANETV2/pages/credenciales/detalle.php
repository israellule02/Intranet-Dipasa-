<?php

require_once("../config/conexion.php");


/*=========================================
    VALIDAR ID
=========================================*/

if(!isset($_GET["id"])){

    header(
        "Location: dashboard.php?page=consultarCredenciales"
    );

    exit();

}


$id = intval($_GET["id"]);


/*=========================================
    OBTENER CREDENCIAL
=========================================*/

$sql = $conexion->prepare("

    SELECT

        c.id,

        c.identificador,

        c.idDispositivo,

        c.`contraseña`,

        c.idTipoDeCredencial,

        tc.nombre AS tipoCredencial,

        d.id AS dispositivoId,

        d.marca,

        d.numdeserie,

        d.caracteristicas,

        t.nombre AS tipoDispositivo

    FROM credenciales c

    LEFT JOIN tiposdecredeciales tc
        ON c.idTipoDeCredencial = tc.id

    LEFT JOIN dispositivos d
        ON c.idDispositivo = d.id

    LEFT JOIN Pre_tipoDeDispositivo t
        ON d.idTipoDis = t.id

    WHERE c.id = ?

");


$sql->execute([
    $id
]);


$credencial = $sql->fetch(PDO::FETCH_ASSOC);


/*=========================================
    VALIDAR
=========================================*/

if(!$credencial){

    echo "

    <div class='alert alert-danger'>

        <i class='bi bi-exclamation-triangle-fill'></i>

        La credencial no existe.

    </div>

    ";

    exit();

}

?>

<div class="container-fluid">


<!--=========================================
    REGRESAR
=========================================-->

<a
    href="dashboard.php?page=consultarCredenciales"
    class="btn btn-outline-success mb-4">

    <i class="bi bi-arrow-left"></i>

    Regresar

</a>


<!--=========================================
    CARD
=========================================-->

<div class="card shadow border-0 rounded-4">


    <!-- HEADER -->

    <div class="card-header bg-success text-white">

        <h3 class="mb-0">

            <i class="bi bi-key-fill"></i>

            Detalle de credencial

        </h3>

    </div>


    <!-- BODY -->

    <div class="card-body p-4">


        <div class="row">


            <!--=================================
                IDENTIFICADOR
            =================================-->

            <div class="col-md-6 mb-4">

                <label class="fw-bold">

                    Identificador

                </label>

                <div class="border rounded p-3 bg-light">

                    <?php

                    echo htmlspecialchars(
                        $credencial["identificador"] ?? ""
                    );

                    ?>

                </div>

            </div>


            <!--=================================
                TIPO DE CREDENCIAL
            =================================-->

            <div class="col-md-6 mb-4">

                <label class="fw-bold">

                    Tipo de credencial

                </label>

                <div>

                    <span class="badge bg-primary fs-6">

                        <?php

                        echo htmlspecialchars(
                            $credencial["tipoCredencial"]
                            ?? "Sin tipo"
                        );

                        ?>

                    </span>

                </div>

            </div>


            <!--=================================
                CONTRASEÑA
            =================================-->

            <div class="col-md-6 mb-4">

                <label class="fw-bold">

                    Contraseña

                </label>


                <div class="input-group">

                    <input
                        type="password"
                        id="contrasena"
                        class="form-control"
                        value="••••••••••••"
                        readonly
                    >


                    <button
                        type="button"
                        class="btn btn-outline-success"
                        id="btnVerContrasena">

                        <i class="bi bi-eye-fill"></i>

                        Ver

                    </button>

                </div>


                <small class="text-muted">

                    Debe autorizarse para visualizarla.

                </small>

            </div>


            <!--=================================
                DISPOSITIVO
            =================================-->

            <div class="col-md-6 mb-4">

                <label class="fw-bold">

                    Dispositivo

                </label>


                <?php if(
                    !empty($credencial["idDispositivo"])
                ){ ?>


                    <div class="border rounded p-3 bg-light">

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $credencial["marca"] ?? ""
                            );

                            ?>

                        </strong>


                        <br>


                        <small>

                            <strong>ID:</strong>

                            <?php

                            echo htmlspecialchars(
                                $credencial["dispositivoId"]
                                ?? ""
                            );

                            ?>

                        </small>


                        <br>


                        <small>

                            <strong>Número de serie:</strong>

                            <?php

                            echo htmlspecialchars(
                                $credencial["numdeserie"]
                                ?? ""
                            );

                            ?>

                        </small>


                        <br>


                        <small>

                            <strong>Tipo:</strong>

                            <?php

                            echo htmlspecialchars(
                                $credencial["tipoDispositivo"]
                                ?? ""
                            );

                            ?>

                        </small>

                    </div>


                <?php }else{ ?>


                    <div class="border rounded p-3 bg-light text-muted">

                        <i class="bi bi-pc-display"></i>

                        Sin dispositivo ligado.

                    </div>


                <?php } ?>

            </div>


            <!--=================================
                CARACTERÍSTICAS
            =================================-->

            <?php

            if(
                !empty(
                    $credencial["caracteristicas"]
                )
            ){

            ?>

                <div class="col-12 mb-4">

                    <label class="fw-bold">

                        Características del dispositivo

                    </label>


                    <div class="border rounded p-3 bg-light">

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $credencial["caracteristicas"]
                            )
                        );

                        ?>

                    </div>

                </div>

            <?php

            }

            ?>


        </div>


        <hr>


        <!--=================================
            BOTONES
        =================================-->

        <div class="text-end">

            <a
                href="dashboard.php?page=editarCredencial&id=<?php
                    echo $credencial["id"];
                ?>"
                class="btn btn-warning">

                <i class="bi bi-pencil-square"></i>

                Editar

            </a>

        </div>


    </div>

</div>

</div>


<!--=========================================
    MODAL PERSONALIZADO
    NO DEPENDE DE BOOTSTRAP JS
=========================================-->

<div
    id="modalAutorizacion"
    style="
        display:none;
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background:rgba(0,0,0,0.55);
        z-index:99999;
        align-items:center;
        justify-content:center;
    "
>


    <div
        style="
            width:90%;
            max-width:450px;
            background:#fff;
            border-radius:15px;
            box-shadow:0 10px 40px rgba(0,0,0,0.35);
            overflow:hidden;
        "
    >


        <!-- HEADER -->

        <div
            class="bg-success text-white"
            style="
                padding:16px 20px;
                display:flex;
                align-items:center;
                justify-content:space-between;
            "
        >

            <h5 class="mb-0">

                <i class="bi bi-shield-lock-fill"></i>

                Autorización requerida

            </h5>


            <button
                type="button"
                id="cerrarModal"
                style="
                    border:none;
                    background:transparent;
                    color:white;
                    font-size:25px;
                    line-height:1;
                "
            >

                &times;

            </button>

        </div>


        <!-- BODY -->

        <div style="padding:20px;">

            <p>

                Para visualizar la contraseña
                introduce tu contraseña de usuario.

            </p>


            <label class="form-label fw-bold">

                Contraseña de autorización

            </label>


            <input
                type="password"
                id="passwordAutorizacion"
                class="form-control"
                placeholder="Ingrese su contraseña"
                autocomplete="current-password"
            >


            <div
                id="mensajeAutorizacion"
                class="text-danger mt-3"
            >
            </div>

        </div>


        <!-- FOOTER -->

        <div
            style="
                padding:15px 20px;
                display:flex;
                justify-content:flex-end;
                gap:10px;
                border-top:1px solid #dee2e6;
            "
        >

            <button
                type="button"
                class="btn btn-secondary"
                id="btnCancelar">

                Cancelar

            </button>


            <button
                type="button"
                class="btn btn-success"
                id="btnAutorizar">

                <i class="bi bi-unlock-fill"></i>

                Autorizar

            </button>

        </div>


    </div>

</div>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function(){

        /*=========================================
            ELEMENTOS
        =========================================*/

        const btnVer =
            document.getElementById(
                "btnVerContrasena"
            );

        const btnAutorizar =
            document.getElementById(
                "btnAutorizar"
            );

        const btnCancelar =
            document.getElementById(
                "btnCancelar"
            );

        const btnCerrar =
            document.getElementById(
                "cerrarModal"
            );

        const modal =
            document.getElementById(
                "modalAutorizacion"
            );

        const passwordAutorizacion =
            document.getElementById(
                "passwordAutorizacion"
            );

        const mensaje =
            document.getElementById(
                "mensajeAutorizacion"
            );

        const campoContrasena =
            document.getElementById(
                "contrasena"
            );


        /*=========================================
            CONTADOR
        =========================================*/

        let intervalo = null;


        /*=========================================
            ABRIR MODAL
        =========================================*/

        btnVer.addEventListener(
            "click",
            function(){

                /*
                 * Si la contraseña está visible,
                 * ocultarla.
                 */

                if(
                    campoContrasena.type === "text"
                ){

                    ocultarContrasena();

                    return;

                }


                passwordAutorizacion.value = "";

                mensaje.innerHTML = "";

                btnAutorizar.disabled = false;

                btnAutorizar.innerHTML =
                    '<i class="bi bi-unlock-fill"></i> Autorizar';


                modal.style.display = "flex";


                setTimeout(
                    function(){

                        passwordAutorizacion.focus();

                    },
                    100
                );

            }
        );


        /*=========================================
            CERRAR MODAL
        =========================================*/

        function cerrarModal(){

            modal.style.display = "none";

            passwordAutorizacion.value = "";

            mensaje.innerHTML = "";

            btnAutorizar.disabled = false;

            btnAutorizar.innerHTML =
                '<i class="bi bi-unlock-fill"></i> Autorizar';

        }


        btnCancelar.addEventListener(
            "click",
            cerrarModal
        );


        btnCerrar.addEventListener(
            "click",
            cerrarModal
        );


        /*=========================================
            CLICK FUERA
        =========================================*/

        modal.addEventListener(
            "click",
            function(event){

                if(
                    event.target === modal
                ){

                    cerrarModal();

                }

            }
        );


        /*=========================================
            ENTER
        =========================================*/

        passwordAutorizacion.addEventListener(
            "keydown",
            function(event){

                if(
                    event.key === "Enter"
                ){

                    event.preventDefault();

                    autorizarContrasena();

                }

            }
        );


        /*=========================================
            BOTÓN AUTORIZAR
        =========================================*/

        btnAutorizar.addEventListener(
            "click",
            autorizarContrasena
        );


        /*=========================================
            AUTORIZAR
        =========================================*/

        function autorizarContrasena(){

            const password =
                passwordAutorizacion.value.trim();


            /*=====================================
                VALIDAR
            =====================================*/

            if(
                password === ""
            ){

                mensaje.innerHTML =
                    "Ingrese su contraseña.";

                passwordAutorizacion.focus();

                return;

            }


            /*=====================================
                DESACTIVAR
            =====================================*/

            btnAutorizar.disabled = true;

            btnAutorizar.innerHTML =
                '<span class="spinner-border spinner-border-sm"></span> Validando...';

            mensaje.innerHTML = "";


            /*=====================================
                CREAR DATOS
            =====================================*/

            const datos =
                new URLSearchParams();


            datos.append(
                "id",
                "<?php echo intval($credencial["id"]); ?>"
            );


            datos.append(
                "password",
                password
            );


            /*=====================================
                AJAX
            =====================================*/

            fetch(
                "../controllers/VerContrasenaCredencialController.php",
                {

                    method: "POST",

                    headers: {

                        "Content-Type":
                            "application/x-www-form-urlencoded; charset=UTF-8",

                        "Accept":
                            "application/json"

                    },

                    body:
                        datos.toString()

                }
            )


            /*=====================================
                RESPUESTA
            =====================================*/

            .then(
                async function(response){

                    const texto =
                        await response.text();


                    console.log(
                        "Respuesta del servidor:",
                        texto
                    );


                    if(
                        !response.ok
                    ){

                        throw new Error(
                            "HTTP " +
                            response.status +
                            ": " +
                            texto
                        );

                    }


                    let data;


                    try{

                        data =
                            JSON.parse(
                                texto
                            );

                    }catch(error){

                        throw new Error(
                            "El controlador no devolvió JSON válido. Respuesta: " +
                            texto
                        );

                    }


                    return data;

                }
            )


            /*=====================================
                PROCESAR
            =====================================*/

           .then(
    function(data){

        console.log(
            "=============================="
        );

        console.log(
            "RESPUESTA COMPLETA:",
            data
        );

        console.log(
            "SUCCESS:",
            data.success
        );

        console.log(
            "CONTRASEÑA:",
            data.contrasena
        );

        console.log(
            "TIPO:",
            typeof data.contrasena
        );

        console.log(
            "LONGITUD:",
            data.contrasena
                ? data.contrasena.length
                : 0
        );

        console.log(
            "=============================="
        );


        if(
            data.success === true
        ){

            campoContrasena.type =
                "text";

            campoContrasena.value =
                data.contrasena;


            cerrarModal();


            btnVer.classList.remove(
                "btn-outline-success"
            );

            btnVer.classList.add(
                "btn-success"
            );


            iniciarContador(
                data.segundos || 10
            );

        }
        else{

            mensaje.innerHTML =
                data.mensaje ||
                "No fue posible autorizar.";

        }

    }
)


            /*=====================================
                ERROR
            =====================================*/

            .catch(
                function(error){

                    console.error(
                        "ERROR:",
                        error
                    );


                    mensaje.innerHTML =
                        error.message;

                }
            )


            /*=====================================
                FINALIZAR
            =====================================*/

            .finally(
                function(){

                    btnAutorizar.disabled =
                        false;

                    btnAutorizar.innerHTML =
                        '<i class="bi bi-unlock-fill"></i> Autorizar';

                }
            );

        }


        /*=========================================
            CONTADOR
        =========================================*/

        function iniciarContador(segundos){

            if(
                intervalo !== null
            ){

                clearInterval(
                    intervalo
                );

            }


            let tiempo =
                parseInt(
                    segundos
                );


            btnVer.innerHTML =
                '<i class="bi bi-eye-slash-fill"></i> Ocultar (' +
                tiempo +
                's)';


            intervalo =
                setInterval(
                    function(){

                        tiempo--;


                        if(
                            tiempo > 0
                        ){

                            btnVer.innerHTML =
                                '<i class="bi bi-eye-slash-fill"></i> Ocultar (' +
                                tiempo +
                                's)';

                        }


                        if(
                            tiempo <= 0
                        ){

                            clearInterval(
                                intervalo
                            );

                            intervalo = null;

                            ocultarContrasena();

                        }

                    },
                    1000
                );

        }


        /*=========================================
            OCULTAR CONTRASEÑA
        =========================================*/

        function ocultarContrasena(){

            if(
                intervalo !== null
            ){

                clearInterval(
                    intervalo
                );

                intervalo = null;

            }


            campoContrasena.type =
                "password";


            campoContrasena.value =
                "••••••••••••";


            btnVer.innerHTML =
                '<i class="bi bi-eye-fill"></i> Ver';


            btnVer.classList.remove(
                "btn-success"
            );


            btnVer.classList.add(
                "btn-outline-success"
            );

        }

    }
);

</script>