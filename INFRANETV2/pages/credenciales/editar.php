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

        c.*,

        tc.nombre AS tipoCredencial,

        d.marca,

        d.numdeserie,

        d.caracteristicas,

        d.id AS dispositivoId,

        t.nombre AS tipoDispositivo

    FROM credenciales c

    LEFT JOIN tiposdecredeciales tc
        ON c.idTipoDeCredencial = tc.id

    LEFT JOIN dispositivos d
        ON c.idDispositivo = d.id

    LEFT JOIN Pre_tipoDeDispositivo t
        ON d.idTipoDis = t.id

    WHERE c.id = :id

");


$sql->bindParam(
    ":id",
    $id,
    PDO::PARAM_INT
);

$sql->execute();


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


/*=========================================
    TIPOS DE CREDENCIALES
=========================================*/

$sqlTipos = $conexion->prepare("

    SELECT *

    FROM tiposdecredeciales

    ORDER BY nombre

");

$sqlTipos->execute();

$tipos = $sqlTipos->fetchAll(PDO::FETCH_ASSOC);


/*=========================================
    DISPOSITIVOS
=========================================*/

$sqlDispositivos = $conexion->prepare("

    SELECT

        d.id,

        d.marca,

        d.numdeserie,

        t.nombre AS tipo,

        CONCAT(
            t.nombre,
            ' | ',
            d.marca,
            ' | Serie: ',
            d.numdeserie
        ) AS dispositivo

    FROM dispositivos d

    LEFT JOIN Pre_tipoDeDispositivo t
        ON d.idTipoDis = t.id

    ORDER BY
        t.nombre ASC,
        d.marca ASC,
        d.numdeserie ASC

");

$sqlDispositivos->execute();

$dispositivos =
    $sqlDispositivos->fetchAll(PDO::FETCH_ASSOC);

?>


<div class="container-fluid">


    <!--=========================================
        CARD
    =========================================-->

    <div class="card shadow border-0 rounded-4">


        <!--=====================================
            HEADER
        =====================================-->

        <div class="card-header bg-warning text-white">

            <h3 class="mb-0">

                <i class="bi bi-pencil-square"></i>

                Editar credencial

            </h3>

        </div>


        <!--=====================================
            BODY
        =====================================-->

        <div class="card-body p-4">


            <form
                action="../controllers/EditarCredencialController.php"
                method="POST"
                id="formEditarCredencial"
            >


                <!--=================================
                    ID
                =================================-->

                <input
                    type="hidden"
                    name="id"
                    value="<?php
                        echo intval($credencial["id"]);
                    ?>"
                >


                <div class="row">


                    <!--=================================
                        IDENTIFICADOR
                    =================================-->

                    <div class="col-md-6 mb-4">

                        <label class="fw-bold">

                            Identificador

                        </label>


                        <input
                            type="text"
                            name="identificador"
                            class="form-control"
                           
                            value="<?php

                                echo htmlspecialchars(
                                    $credencial["identificador"] ?? "",
                                    ENT_QUOTES,
                                    "UTF-8"
                                );

                            ?>"
                        >

                    </div>


                    <!--=================================
                        TIPO DE CREDENCIAL
                    =================================-->

                    <div class="col-md-6 mb-4">

                        <label class="fw-bold">

                            Tipo de credencial

                        </label>


                        <select
                            name="idTipoDeCredencial"
                            class="form-select"
                            required
                        >

                            <?php foreach($tipos as $tipo){ ?>

                                <option
                                    value="<?php
                                        echo intval(
                                            $tipo["id"]
                                        );
                                    ?>"

                                    <?php

                                    if(
                                        $credencial[
                                            "idTipoDeCredencial"
                                        ]
                                        ==
                                        $tipo["id"]
                                    ){

                                        echo "selected";

                                    }

                                    ?>
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $tipo["nombre"] ?? "",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </option>

                            <?php } ?>

                        </select>

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
                                name="contrasena"
                                id="contrasena"
                                class="form-control"
                                value=""
                                placeholder="••••••••••••"
                                readonly
                                autocomplete="off"
                            >


                            <button
                                type="button"
                                class="btn btn-outline-success"
                                id="btnVer"
                            >

                                <i class="bi bi-eye-fill"></i>

                                Ver / Editar

                            </button>

                        </div>


                        <small
                            class="text-muted"
                            id="mensajeContrasena"
                        >

                            Debe autorizarse para visualizar
                            o modificar la contraseña.

                        </small>

                    </div>


                    <!--=================================
                        DISPOSITIVO
                    =================================-->
<!--=================================
    DISPOSITIVO
=================================-->

<div class="col-md-6 mb-4">

    <label class="fw-bold">

        Dispositivo

    </label>


    <input
        type="text"
        id="buscarDispositivo"
        class="form-control"
        list="listaDispositivos"
        placeholder="Buscar por tipo, marca o número de serie..."
        autocomplete="off"
    >


    <datalist id="listaDispositivos">

        <?php foreach($dispositivos as $dispositivo){ ?>

            <option

                value="<?php

                    echo htmlspecialchars(
                        $dispositivo["dispositivo"],
                        ENT_QUOTES,
                        "UTF-8"
                    );

                ?>"

                data-id="<?php

                    echo intval(
                        $dispositivo["id"]
                    );

                ?>">

            </option>

        <?php } ?>

    </datalist>


    <!-- ID REAL QUE SE ENVÍA AL CONTROLLER -->

    <input
        type="hidden"
        name="idDispositivo"
        id="idDispositivo"
        value="<?php

            echo !empty($credencial["idDispositivo"])
                ? intval($credencial["idDispositivo"])
                : "";

        ?>"
    >


    <small class="text-muted">

        Escriba para buscar por tipo, marca o número de serie.

    </small>

</div>


                    <!--=================================
                        INFORMACIÓN DEL DISPOSITIVO
                    =================================-->

                    <?php

                    if(
                        !empty(
                            $credencial["idDispositivo"]
                        )
                    ){

                    ?>

                        <div class="col-12 mb-4">

                            <label class="fw-bold">

                                Dispositivo seleccionado

                            </label>


                            <div class="border rounded p-3 bg-light">

                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $credencial["marca"] ?? "",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </strong>


                                <br>


                                <small>

                                    <strong>ID:</strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $credencial[
                                            "dispositivoId"
                                        ] ?? "",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </small>


                                <br>


                                <small>

                                    <strong>
                                        Número de serie:
                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $credencial[
                                            "numdeserie"
                                        ] ?? "",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </small>


                                <?php

                                if(
                                    !empty(
                                        $credencial[
                                            "tipoDispositivo"
                                        ]
                                    )
                                ){

                                ?>

                                    <br>

                                    <small>

                                        <strong>
                                            Tipo:
                                        </strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $credencial[
                                                "tipoDispositivo"
                                            ],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );

                                        ?>

                                    </small>

                                <?php } ?>


                                <?php

                                if(
                                    !empty(
                                        $credencial[
                                            "caracteristicas"
                                        ]
                                    )
                                ){

                                ?>

                                    <br><br>

                                    <small>

                                        <strong>
                                            Características:
                                        </strong>

                                        <br>

                                        <?php

                                        echo nl2br(
                                            htmlspecialchars(
                                                $credencial[
                                                    "caracteristicas"
                                                ],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            )
                                        );

                                        ?>

                                    </small>

                                <?php } ?>

                            </div>

                        </div>

                    <?php } ?>


                    <!--=================================
                        BOTONES
                    =================================-->

                    <div class="col-12 text-end">

                        <a
                            href="dashboard.php?page=consultarCredenciales"
                            class="btn btn-secondary"
                        >

                            <i class="bi bi-x-circle"></i>

                            Cancelar

                        </a>


                        <button
                            type="submit"
                            class="btn btn-warning"
                        >

                            <i class="bi bi-save"></i>

                            Guardar cambios

                        </button>

                    </div>


                </div>

            </form>


        </div>

    </div>

</div>


<!--=========================================
    MODAL DE AUTORIZACIÓN
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


        <!--=====================================
            HEADER
        =====================================-->

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


        <!--=====================================
            BODY
        =====================================-->

        <div style="padding:20px;">

            <p>

                Para visualizar o modificar la contraseña
                introduce tu contraseña de autorización.

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


        <!--=====================================
            FOOTER
        =====================================-->

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
                id="btnCancelar"
            >

                Cancelar

            </button>


            <button
                type="button"
                class="btn btn-success"
                id="btnAutorizar"
            >

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
            document.getElementById("btnVer");


        const btnAutorizar =
            document.getElementById("btnAutorizar");


        const btnCancelar =
            document.getElementById("btnCancelar");


        const btnCerrar =
            document.getElementById("cerrarModal");


        const modal =
            document.getElementById("modalAutorizacion");


        const passwordAutorizacion =
            document.getElementById(
                "passwordAutorizacion"
            );


        const mensaje =
            document.getElementById(
                "mensajeAutorizacion"
            );


        const mensajeContrasena =
            document.getElementById(
                "mensajeContrasena"
            );


        const campoContrasena =
            document.getElementById(
                "contrasena"
            );


        /*=========================================
            ESTADO DE AUTORIZACIÓN
        =========================================*/

        let autorizado = false;


        /*=========================================
            ABRIR MODAL
        =========================================*/

        btnVer.addEventListener(
            "click",
            function(){

                /*
                 * Si ya está autorizado,
                 * simplemente permitir editar.
                 */

                if(autorizado){

                    campoContrasena.readOnly = false;

                    campoContrasena.type = "text";

                    campoContrasena.focus();

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
    BUSCAR DISPOSITIVO
=========================================*/

const inputDispositivo =
    document.getElementById("buscarDispositivo");

const idDispositivo =
    document.getElementById("idDispositivo");


/*=========================================
    CARGAR DISPOSITIVO ACTUAL
=========================================*/

const opcionesDispositivos =
    document.querySelectorAll(
        "#listaDispositivos option"
    );


opcionesDispositivos.forEach(
    function(opcion){

        if(
            opcion.dataset.id ==
            idDispositivo.value
        ){

            inputDispositivo.value =
                opcion.value;

        }

    }
);


/*=========================================
    BUSCAR DISPOSITIVO
=========================================*/

inputDispositivo.addEventListener(
    "input",
    function(){

        idDispositivo.value = "";


        document
        .querySelectorAll(
            "#listaDispositivos option"
        )
        .forEach(
            function(opcion){

                if(
                    opcion.value ===
                    inputDispositivo.value
                ){

                    idDispositivo.value =
                        opcion.dataset.id;

                }

            }
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
            CLICK FUERA DEL MODAL
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
            AUTORIZAR
        =========================================*/

        btnAutorizar.addEventListener(
            "click",
            autorizarContrasena
        );


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
                AJAX
            =====================================*/

            fetch(
                "../controllers/VerContrasenaCredencialController.php",
                {

                    method: "POST",

                    headers: {

                        "Content-Type":
                            "application/x-www-form-urlencoded",

                        "Accept":
                            "application/json"

                    },

                    body:
                        "id=<?php echo intval($credencial["id"]); ?>"
                        +
                        "&password="
                        +
                        encodeURIComponent(password)

                }
            )


            /*=====================================
                RESPUESTA
            =====================================*/

            .then(
                function(response){

                    if(!response.ok){

                        throw new Error(
                            "HTTP " +
                            response.status
                        );

                    }

                    return response.json();

                }
            )


            /*=====================================
                PROCESAR
            =====================================*/

            .then(
                function(data){

                    console.log(
                        "Respuesta autorización:",
                        data
                    );


                    if(
                        data.success === true
                    ){

                        /*
                         * AUTORIZACIÓN CORRECTA
                         */

                        autorizado = true;


                        /*
                         * MOSTRAR CONTRASEÑA
                         */

                        campoContrasena.type =
                            "text";


                        campoContrasena.readOnly =
                            false;


                        campoContrasena.value =
                            data.contrasena;


                        /*
                         * CAMBIAR BOTÓN
                         */

                        btnVer.innerHTML =
                            '<i class="bi bi-unlock-fill"></i> Autorizado';


                        btnVer.classList.remove(
                            "btn-outline-success"
                        );


                        btnVer.classList.add(
                            "btn-success"
                        );


                        /*
                         * MENSAJE
                         */

                        mensajeContrasena.innerHTML =
                            '<i class="bi bi-unlock-fill text-success"></i> ' +
                            'Contraseña autorizada. Puedes verla y modificarla.';


                        mensajeContrasena.classList.remove(
                            "text-muted"
                        );


                        mensajeContrasena.classList.add(
                            "text-success"
                        );


                        /*
                         * CERRAR MODAL
                         */

                        cerrarModal();


                        /*
                         * PONER CURSOR
                         */

                        campoContrasena.focus();

                        campoContrasena.select();

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
                        "Error:",
                        error
                    );


                    mensaje.innerHTML =
                        "No se pudo comunicar con el servidor.";

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
    VALIDAR DISPOSITIVO
=========================================*/

const formulario =
    document.getElementById(
        "formEditarCredencial"
    );


formulario.addEventListener(
    "submit",
    function(event){

        /*
         * Si el usuario escribió algo
         * pero no seleccionó un dispositivo válido
         */

        if(
            inputDispositivo.value.trim() !== ""
            &&
            idDispositivo.value === ""
        ){

            alert(
                "Seleccione un dispositivo válido de la lista."
            );

            inputDispositivo.focus();

            event.preventDefault();

            return;

        }

    }
);

    }


    
);

</script>