<?php

require_once("../config/conexion.php");


/*=========================================
    VALIDAR ID
=========================================*/

if(!isset($_GET["id"])){

    header(
        "Location: dashboard.php?page=consultarPrestamo"
    );

    exit();

}


$id = intval($_GET["id"]);


/*=========================================
    OBTENER PRÉSTAMO
=========================================*/

$sql = $conexion->prepare("

    SELECT

        a.*,

        d.marca,
        d.numDeSerie,
        d.caracteristicas,

        CONCAT(
            u.nombre,
            ' ',
            u.apellidos
        ) AS nombreCompleto,

        CONCAT(
            t.nombre,
            ' | ',
            d.marca,
            ' | Serie: ',
            d.numDeSerie
        ) AS dispositivo

    FROM Asignaciones a

    INNER JOIN dispositivos d
        ON a.idDispositivo = d.id

    INNER JOIN usuarios u
        ON a.idUsuario = u.id

    INNER JOIN pre_tipodedispositivo t
        ON d.idTipoDis = t.id

    WHERE a.id = :id

");


$sql->bindParam(
    ":id",
    $id,
    PDO::PARAM_INT
);

$sql->execute();


$asignacion =
    $sql->fetch(PDO::FETCH_ASSOC);


/*=========================================
    VALIDAR
=========================================*/

if(!$asignacion){

    echo "

    <div class='alert alert-danger'>

        <i class='bi bi-exclamation-triangle-fill'></i>

        El préstamo no existe.

    </div>

    ";

    exit();

}


/*=========================================
    USUARIOS
=========================================*/

$sqlUsuarios = $conexion->prepare("

    SELECT

        id,

        CONCAT(
            nombre,
            ' ',
            apellidos
        ) AS nombreCompleto

    FROM usuarios

    ORDER BY nombre ASC

");

$sqlUsuarios->execute();

$usuarios =
    $sqlUsuarios->fetchAll(PDO::FETCH_ASSOC);


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

    INNER JOIN pre_tipodedispositivo t

        ON d.idTipoDis = t.id

    ORDER BY

        t.nombre ASC,
        d.marca ASC,
        d.numDeSerie ASC

");

$sqlDispositivos->execute();

$dispositivos =
    $sqlDispositivos->fetchAll(PDO::FETCH_ASSOC);


/*=========================================
    ESTADOS
=========================================*/

$sqlEstado = $conexion->prepare("

    SELECT *

    FROM pre_estado

    ORDER BY nombre ASC

");

$sqlEstado->execute();

$estados =
    $sqlEstado->fetchAll(PDO::FETCH_ASSOC);


/*=========================================
    ESTATUS
=========================================*/

$sqlEstatus = $conexion->prepare("

    SELECT *

    FROM pre_estatus

    ORDER BY nombre ASC

");

$sqlEstatus->execute();

$estatuses =
    $sqlEstatus->fetchAll(PDO::FETCH_ASSOC);

?>


<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-10">

            <div class="card shadow border-0 rounded-4">


                <!--=====================================
                    HEADER
                =====================================-->

                <div class="card-header bg-warning">

                    <h3 class="mb-0">

                        <i class="bi bi-pencil-square"></i>

                        Editar préstamo

                    </h3>

                </div>


                <!--=====================================
                    BODY
                =====================================-->

                <div class="card-body">

                    <form
                        action="../controllers/EditarAsignacionController.php"
                        method="POST"
                        autocomplete="off"
                    >


                        <!--=================================
                            ID
                        =================================-->

                        <input
                            type="hidden"
                            name="id"
                            value="<?php
                                echo intval(
                                    $asignacion["id"]
                                );
                            ?>"
                        >


                        <div class="row">


                            <!--=================================
                                EMPLEADO
                            =================================-->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Empleado

                                </label>


                                <input
                                    type="text"
                                    id="buscarEmpleado"
                                    class="form-control"
                                    list="listaEmpleados"
                                    placeholder="Buscar empleado..."
                                    autocomplete="off"
                                    required
                                    value="<?php

                                        echo htmlspecialchars(
                                            $asignacion[
                                                "nombreCompleto"
                                            ] ?? "",
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );

                                    ?>"
                                >


                                <datalist id="listaEmpleados">

                                    <?php
                                    foreach(
                                        $usuarios
                                        as $usuario
                                    ){
                                    ?>

                                        <option
                                            value="<?php

                                                echo htmlspecialchars(
                                                    $usuario[
                                                        "nombreCompleto"
                                                    ],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                );

                                            ?>"
                                            data-id="<?php

                                                echo intval(
                                                    $usuario["id"]
                                                );

                                            ?>"
                                        >

                                        </option>

                                    <?php } ?>

                                </datalist>


                                <!-- ID REAL DEL USUARIO -->

                                <input
                                    type="hidden"
                                    name="idUsuario"
                                    id="idUsuario"
                                    value="<?php

                                        echo intval(
                                            $asignacion[
                                                "idUsuario"
                                            ]
                                        );

                                    ?>"
                                    required
                                >


                                <small class="text-muted">

                                    Escriba para buscar y seleccione
                                    un empleado de la lista.

                                </small>

                            </div>


                            <!--=================================
                                DISPOSITIVO
                            =================================-->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Dispositivo

                                </label>


                                <input
                                    type="text"
                                    id="buscarDispositivo"
                                    class="form-control"
                                    list="listaDispositivos"
                                    placeholder="Buscar por marca o número de serie..."
                                    autocomplete="off"
                                    required
                                    value="<?php

                                        echo htmlspecialchars(
                                            $asignacion[
                                                "dispositivo"
                                            ] ?? "",
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );

                                    ?>"
                                >


                                <datalist id="listaDispositivos">

                                    <?php
                                    foreach(
                                        $dispositivos
                                        as $dispositivo
                                    ){
                                    ?>

                                        <option
                                            value="<?php

                                                echo htmlspecialchars(
                                                    $dispositivo["tipo"]
                                                    . " | "
                                                    . $dispositivo["marca"]
                                                    . " | Serie: "
                                                    . $dispositivo[
                                                        "numDeSerie"
                                                    ],
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


                                <!-- ID REAL DEL DISPOSITIVO -->

                                <input
                                    type="hidden"
                                    name="idDispositivo"
                                    id="idDispositivo"
                                    value="<?php

                                        echo intval(
                                            $asignacion[
                                                "idDispositivo"
                                            ]
                                        );

                                    ?>"
                                    required
                                >


                                <small class="text-muted">

                                    Escriba para buscar por marca
                                    o número de serie.

                                </small>

                            </div>


                            <!--=================================
                                ESTADO
                            =================================-->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Estado del dispositivo

                                </label>


                                <select
                                    name="idEstado"
                                    class="form-select"
                                    required
                                >

                                    <?php
                                    foreach(
                                        $estados
                                        as $estado
                                    ){
                                    ?>

                                        <option
                                            value="<?php

                                                echo intval(
                                                    $estado["id"]
                                                );

                                            ?>"

                                            <?php

                                            if(
                                                $asignacion[
                                                    "idEstado"
                                                ]
                                                ==
                                                $estado["id"]
                                            ){

                                                echo "selected";

                                            }

                                            ?>
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $estado["nombre"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            );

                                            ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>


                            <!--=================================
                                FECHA DE INICIO
                            =================================-->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Fecha de inicio

                                </label>


                                <input
                                    type="date"
                                    name="fechainicio"
                                    class="form-control"
                                    value="<?php

                                        echo htmlspecialchars(
                                            $asignacion[
                                                "fechainicio"
                                            ] ?? "",
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );

                                    ?>"
                                    required
                                >

                            </div>


                            <!--=================================
                                FECHA DE ENTREGA
                            =================================-->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Fecha de entrega

                                </label>


                                <input
                                    type="date"
                                    name="fechadeentregado"
                                    class="form-control"
                                    value="<?php

                                        echo htmlspecialchars(
                                            $asignacion[
                                                "fechadeentregado"
                                            ] ?? "",
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );

                                    ?>"
                                >

                            </div>


                            <!--=================================
                                ESTATUS
                            =================================-->

                            <div class="col-md-6 mb-4">

                                <label class="form-label">

                                    Estatus

                                </label>


                                <select
                                    name="idpreestatus"
                                    class="form-select"
                                    required
                                >

                                    <?php
                                    foreach(
                                        $estatuses
                                        as $estatus
                                    ){
                                    ?>

                                        <option
                                            value="<?php

                                                echo intval(
                                                    $estatus["id"]
                                                );

                                            ?>"

                                            <?php

                                            if(
                                                $asignacion[
                                                    "idpreestatus"
                                                ]
                                                ==
                                                $estatus["id"]
                                            ){

                                                echo "selected";

                                            }

                                            ?>
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $estatus["nombre"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            );

                                            ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>


                            <!--=================================
                                BOTONES
                            =================================-->

                            <div class="col-12 text-end">

                                <a
                                    href="dashboard.php?page=consultarAsignaciones"
                                    class="btn btn-secondary"
                                >

                                    <i class="bi bi-arrow-left"></i>

                                    Cancelar

                                </a>


                                <button
                                    type="submit"
                                    class="btn btn-warning"
                                >

                                    <i class="bi bi-save-fill"></i>

                                    Actualizar asignación

                                </button>

                            </div>


                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

/*=========================================
    BUSCAR EMPLEADO
=========================================*/

const inputEmpleado =
    document.getElementById(
        "buscarEmpleado"
    );


const idUsuario =
    document.getElementById(
        "idUsuario"
    );


inputEmpleado.addEventListener(
    "input",
    function(){

        /*-------------------------------------
            BORRAR ID ANTERIOR
        -------------------------------------*/

        idUsuario.value = "";


        /*-------------------------------------
            BUSCAR COINCIDENCIA
        -------------------------------------*/

        document
        .querySelectorAll(
            "#listaEmpleados option"
        )
        .forEach(
            function(opcion){

                if(
                    opcion.value ===
                    inputEmpleado.value
                ){

                    idUsuario.value =
                        opcion.dataset.id;

                }

            }
        );

    }
);


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


inputDispositivo.addEventListener(
    "input",
    function(){

        /*-------------------------------------
            BORRAR ID ANTERIOR
        -------------------------------------*/

        idDispositivo.value = "";


        /*-------------------------------------
            BUSCAR COINCIDENCIA
        -------------------------------------*/

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
    VALIDAR FORMULARIO
=========================================*/

document
.querySelector("form")
.addEventListener(
    "submit",
    function(e){

        /*-------------------------------------
            VALIDAR EMPLEADO
        -------------------------------------*/

        if(
            idUsuario.value === ""
        ){

            alert(
                "Seleccione un empleado válido de la lista."
            );

            inputEmpleado.focus();

            e.preventDefault();

            return;

        }


        /*-------------------------------------
            VALIDAR DISPOSITIVO
        -------------------------------------*/

        if(
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

</script>