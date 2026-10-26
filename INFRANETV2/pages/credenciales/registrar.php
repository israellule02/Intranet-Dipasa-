<?php

require_once("../config/conexion.php");


/*=========================================
    TIPOS DE CREDENCIAL
=========================================*/

$sqlTipos = $conexion->prepare("

    SELECT
        id,
        nombre

    FROM tiposdecredeciales

    ORDER BY nombre ASC

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
        d.numDeSerie,

        t.nombre AS tipo

    FROM dispositivos d

    INNER JOIN Pre_tipoDeDispositivo t
        ON d.idTipoDis = t.id

    ORDER BY
    d.id ,
        t.nombre,
        d.marca,
        d.numDeSerie

");

$sqlDispositivos->execute();

$dispositivos =
    $sqlDispositivos->fetchAll(PDO::FETCH_ASSOC);

?>


<div class="container-fluid">


    <div class="row justify-content-center">


        <div class="col-lg-10">


            <div class="card shadow border-0 rounded-4">


                <!--=========================================
                    HEADER
                =========================================-->

                <div class="card-header bg-success text-white">

                    <h3 class="mb-0">

                        <i class="bi bi-key-fill"></i>

                        Registrar credencial

                    </h3>

                </div>


                <!--=========================================
                    BODY
                =========================================-->

                <div class="card-body p-4">


                    <form
                        action="../controllers/CredencialesController.php"
                        method="POST"
                        autocomplete="off"
                    >


                        <div class="row">


                            <!--=================================
                                IDENTIFICADOR
                            =================================-->

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">

                                    Identificador

                                </label>


                                <input
                                    type="text"
                                    name="identificador"
                                    class="form-control"
                                    placeholder="Ej. usuario@empresa.com"
                                    maxlength="100"
                                >

                            </div>


                            <!--=================================
                                TIPO DE CREDENCIAL
                            =================================-->

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">

                                    Tipo de credencial

                                </label>


                                <select
                                    name="idTipoDeCredencial"
                                    class="form-select"
                                    required
                                >

                                    <option value="">

                                        Seleccione...

                                    </option>


                                    <?php foreach(
                                        $tipos
                                        as $tipo
                                    ){ ?>

                                        <option
                                            value="<?php
                                                echo intval(
                                                    $tipo["id"]
                                                );
                                            ?>"
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $tipo["nombre"],
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

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">

                                    Contraseña

                                </label>


                                <div class="input-group">


                                    <input
                                        type="password"
                                        name="contraseña"
                                        id="contraseña"
                                        class="form-control"
                                        placeholder="Ingrese la contraseña"
                                        maxlength="255"
                                        required
                                    >


                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        id="mostrarContrasena"
                                    >

                                        <i class="bi bi-eye"></i>

                                    </button>


                                </div>


                                <small class="text-muted">

                                    La contraseña se almacenará
                                    de forma segura.

                                </small>

                            </div>


                            <!--=================================
                                DISPOSITIVO
                            =================================-->

                            <div class="col-md-6 mb-3">


                                <label class="form-label fw-bold">

                                    Dispositivo

                                </label>


                                <!--=================================
                                    INPUT DE BÚSQUEDA
                                =================================-->

                                <input
                                    type="text"
                                    id="buscarDispositivo"
                                    class="form-control"
                                    list="listaDispositivos"
                                    placeholder="Buscar por tipo, marca o número de serie..."
                                    autocomplete="off"
                                >


                                <!--=================================
                                    LISTA DE DISPOSITIVOS
                                =================================-->

                                <datalist
                                    id="listaDispositivos"
                                >


                                    <?php foreach(
                                        $dispositivos
                                        as $dispositivo
                                    ){ ?>


                                        <option

                                            value="<?php

                                                echo htmlspecialchars(

                                                $dispositivo["id"]
                                                    ." | ".
                                                    $dispositivo["tipo"]
                                                    ." | "
                                                    .$dispositivo["marca"]
                                                    ." | Serie: "
                                                    .$dispositivo["numDeSerie"],

                                                    ENT_QUOTES,
                                                    "UTF-8"

                                                );

                                            ?>"

                                            data-id="<?php

                                                echo intval(
                                                    $dispositivo["id"]
                                                );

                                            ?>"
                                        >

                                        </option>


                                    <?php } ?>


                                </datalist>


                                <!--=================================
                                    ID REAL DEL DISPOSITIVO
                                =================================-->

                                <input
                                    type="hidden"
                                    name="idDispositivo"
                                    id="idDispositivo"
                                >


                                <small class="text-muted">

                                    Puede buscar por tipo,
                                    marca o número de serie.

                                    Puede dejarse vacío si la
                                    credencial no pertenece a
                                    un dispositivo.

                                </small>


                            </div>


                            <!--=================================
                                BOTONES
                            =================================-->

                            <div class="col-12 text-end mt-4">


                                <a
                                    href="dashboard.php?page=consultarCredenciales"
                                    class="btn btn-secondary"
                                >

                                    <i class="bi bi-x-circle"></i>

                                    Cancelar

                                </a>


                                <button
                                    type="submit"
                                    class="btn btn-success"
                                >

                                    <i class="bi bi-save-fill"></i>

                                    Guardar credencial

                                </button>


                            </div>


                        </div>

                    </form>


                </div>

            </div>

        </div>

    </div>

</div>


<!--=========================================
    JAVASCRIPT
=========================================-->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function(){


        /*=========================================
            BUSCAR DISPOSITIVO
        =========================================*/

        const inputDispositivo =
            document.getElementById(
                "buscarDispositivo"
            );


        const idDispositivo =
            document.getElementById(
                "idDispositivo"
            );


        const opcionesDispositivos =
            document.querySelectorAll(
                "#listaDispositivos option"
            );


        inputDispositivo.addEventListener(
            "input",
            function(){


                /*
                 * Cada vez que el usuario escribe,
                 * primero limpiamos el ID.
                 */

                idDispositivo.value = "";


                /*
                 * Buscar coincidencia exacta
                 * dentro del datalist.
                 */

                opcionesDispositivos.forEach(
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
            CONTRASEÑA
        =========================================*/

        const botonContrasena =
            document.getElementById(
                "mostrarContrasena"
            );


        const campoContrasena =
            document.getElementById(
                "contraseña"
            );


        botonContrasena.addEventListener(
            "click",
            function(){


                const icono =
                    this.querySelector("i");


                if(
                    campoContrasena.type ===
                    "password"
                ){

                    campoContrasena.type =
                        "text";


                    icono.classList.remove(
                        "bi-eye"
                    );


                    icono.classList.add(
                        "bi-eye-slash"
                    );

                }
                else{

                    campoContrasena.type =
                        "password";


                    icono.classList.remove(
                        "bi-eye-slash"
                    );


                    icono.classList.add(
                        "bi-eye"
                    );

                }

            }
        );


        /*=========================================
            VALIDAR FORMULARIO
        =========================================*/

        document
        .querySelector("form")
        .addEventListener(
            "submit",
            function(e){


                /*
                 * Si escribió algo en el buscador
                 * pero no seleccionó una opción válida.
                 */

                if(
                    inputDispositivo.value.trim() !==
                    "" &&
                    idDispositivo.value === ""
                ){

                    alert(
                        "Seleccione un dispositivo válido de la lista."
                    );


                    inputDispositivo.focus();


                    e.preventDefault();


                    return;

                }


            }
        );


    }
);

</script>