<?php



require_once("../config/conexion.php");



$buscar  = $_GET["buscar"] ?? "";

$tipo    = $_GET["tipo"] ?? "";

$estatus = $_GET["estatus"] ?? "";



/*=========================================

ESTATUS

\=========================================*/



$sqlEstatus = $conexion->prepare("

    SELECT *

    FROM estatusvac

    ORDER BY nombre

");



$idUsuario = $_SESSION["idUsuario"];



$sqlEstatus->execute();

$estatuses = $sqlEstatus->fetchAll(PDO::FETCH_ASSOC);





/*=========================================

CONSULTAR SOLICITUDES RH

\=========================================*/



$sql = "



SELECT



    r.idRegistro,

    r.idjefe,

    r.fechainicio,

    r.fechafinal,

    u.idDep,

    r.diastomados,

    d.nombre AS departamento,

    r.idUsuario,



    CONCAT(u.nombre,' ',u.apellidos) AS empleado,



    CONCAT(jefe.nombre,' ',jefe.apellidos) AS jefe,



    es.nombre AS estatus



FROM registrovac r



INNER JOIN usuarios u

    ON r.idUsuario = u.id



INNER JOIN departamentos d

    ON u.idDep = d.idDepartamento



INNER JOIN usuarios jefe

    ON r.idjefe = jefe.id



INNER JOIN estatusvac es

    ON r.idValidacionJefe = es.id



WHERE

    r.idValidacionJefe = 1

    AND r.idValidacionRh = 2



";





/*=========================================

BUSCADOR

\=========================================*/



if($buscar != ""){



    $sql .= "



        AND (



            u.nombre LIKE :buscar



            OR u.apellidos LIKE :buscar



            OR CONCAT(u.nombre,' ',u.apellidos) LIKE :buscar



        )



    ";



}





/*=========================================

FILTRO ESTATUS

\=========================================*/



if($estatus != ""){



    $sql .= "



        AND r.idValidacionJefe = :estatus



    ";



}





$sql .= "



    ORDER BY r.idRegistro DESC



";





$stmt = $conexion->prepare($sql);





/*=========================================

PARAMETROS

\=========================================*/



if($buscar != ""){



    $stmt->bindValue(

        ":buscar",

        "%".$buscar."%"

    );



}





if($estatus != ""){



    $stmt->bindValue(

        ":estatus",

        $estatus,

        PDO::PARAM_INT

    );



}





$stmt->execute();



$resgistros = $stmt->fetchAll(PDO::FETCH_ASSOC);





/*=========================================

OBTENER DETALLE DE CADA SOLICITUD

\=========================================*/



$detallesVacaciones = [];



foreach($resgistros as $resgistro){



    $sqlDetalle = $conexion->prepare("



        SELECT



            idDetalle,

            idRegistro,

            fecha,

            cantidad



        FROM registrovac_detalle



        WHERE idRegistro = :idRegistro



        ORDER BY fecha ASC



    ");



    $sqlDetalle->bindValue(

        ":idRegistro",

        $resgistro["idRegistro"],

        PDO::PARAM_INT

    );



    $sqlDetalle->execute();



    $detallesVacaciones[$resgistro["idRegistro"]] =

        $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);



}



?>





<style>



/*=========================================

MODAL PURO CSS

\=========================================*/



.modal-css {



    position: fixed;



    inset: 0;



    width: 100%;

    height: 100%;



    background: rgba(0,0,0,.60);



    display: flex;



    align-items: center;

    justify-content: center;



    padding: 20px;



    box-sizing: border-box;



    z-index: 99999;



    visibility: hidden;



    opacity: 0;



    pointer-events: none;



    transition:

        opacity .25s ease,

        visibility .25s ease;



}





/*=========================================

ABRIR MODAL

\=========================================*/



.modal-css:target {



    visibility: visible;



    opacity: 1;



    pointer-events: auto;



}





/*=========================================

CONTENIDO

\=========================================*/



.modal-css-contenido {



    position: relative;



    width: 100%;



    max-width: 850px;



    max-height: 90vh;



    overflow-y: auto;



    background: #fff;



    border-radius: 18px;



    box-shadow: 0 15px 50px rgba(0,0,0,.30);



    padding: 0;



}





/*=========================================

ENCABEZADO

\=========================================*/



.modal-css-header {



    display: flex;



    justify-content: space-between;



    align-items: center;



    padding: 20px 25px;



    border-bottom: 1px solid #e9ecef;



    background: #198754;



    color: white;



    border-radius: 18px 18px 0 0;



}





.modal-css-header h5 {



    margin: 0;



    font-weight: 700;



}





.modal-css-cerrar {



    color: white;



    text-decoration: none;



    font-size: 32px;



    line-height: 1;



    font-weight: 300;



    transition: .2s;



}





.modal-css-cerrar:hover {



    color: #d1e7dd;



    transform: scale(1.1);



}





/*=========================================

CUERPO

\=========================================*/



.modal-css-body {



    padding: 25px;



}





/*=========================================

INFORMACION

\=========================================*/



.info-vacaciones {



    display: grid;



    grid-template-columns:

        repeat(2, minmax(0, 1fr));



    gap: 15px;



    margin-bottom: 25px;



}





.info-vacacion-item {



    background: #f8f9fa;



    border-radius: 12px;



    padding: 15px;



    border: 1px solid #e9ecef;



}





.info-vacacion-item small {



    display: block;



    color: #6c757d;



    font-size: 12px;



    margin-bottom: 4px;



}





.info-vacacion-item strong {



    color: #212529;



    font-size: 15px;



}





/*=========================================

TITULO DETALLE

\=========================================*/



.titulo-detalle {



    font-weight: 700;



    margin-bottom: 15px;



    color: #198754;



}





/*=========================================

TABLA DETALLE

\=========================================*/



.tabla-detalle {



    width: 100%;



    border-collapse: collapse;



    margin-bottom: 20px;



}





.tabla-detalle th {



    background: #198754;



    color: white;



    padding: 11px;



    text-align: left;



    font-size: 14px;



}





.tabla-detalle td {



    padding: 11px;



    border-bottom: 1px solid #dee2e6;



    font-size: 14px;



}





.tabla-detalle tbody tr:hover {



    background: #f8f9fa;



}





/*=========================================

BADGES

\=========================================*/



.badge-dia-completo {



    background: #198754;



    color: white;



    padding: 5px 10px;



    border-radius: 20px;



    font-size: 12px;



}





.badge-medio-dia {



    background: #ffc107;



    color: #212529;



    padding: 5px 10px;



    border-radius: 20px;



    font-size: 12px;



}





/*=========================================

TOTAL

\=========================================*/



.total-detalle {



    display: flex;



    justify-content: flex-end;



    margin-top: 15px;



}





.total-detalle-contenido {



    background: #f8f9fa;



    border: 1px solid #dee2e6;



    border-radius: 12px;



    padding: 12px 18px;



    font-weight: 700;



}





.total-detalle-contenido span {



    color: #198754;



    font-size: 18px;



}





/*=========================================

PIE DEL MODAL

\=========================================*/



.modal-css-footer {



    display: flex;



    justify-content: flex-end;



    gap: 10px;



    padding: 18px 25px;



    border-top: 1px solid #e9ecef;



}





/*=========================================

BOTONES

\=========================================*/



.modal-btn {



    display: inline-flex;



    align-items: center;



    justify-content: center;



    gap: 6px;



    padding: 8px 16px;



    border-radius: 8px;



    text-decoration: none;



    border: none;



    cursor: pointer;



    font-size: 14px;



}





.modal-btn-cerrar {



    background: #6c757d;



    color: white;



}





.modal-btn-cerrar:hover {



    background: #5c636a;



    color: white;



}





/*=========================================

BOTON DETALLE

\=========================================*/



.btn-detalle-vacaciones {



    display: inline-flex;



    align-items: center;



    gap: 5px;



    background: #0d6efd;



    color: white;



    text-decoration: none;



    padding: 6px 10px;



    border-radius: 6px;



    font-size: 13px;



    transition: .2s;



}





.btn-detalle-vacaciones:hover {



    background: #0b5ed7;



    color: white;



    transform: translateY(-1px);



}





/*=========================================

RESPONSIVE

\=========================================*/



@media(max-width: 768px){



    .info-vacaciones {



        grid-template-columns: 1fr;



    }



    .modal-css {



        padding: 10px;



    }



    .modal-css-contenido {



        max-height: 95vh;



    }



    .modal-css-body {



        padding: 18px;



    }



}



</style>





<div class="container-fluid">





    <!--=====================================

    TITULO

    =====================================-->



    <div class="d-flex justify-content-between align-items-center mb-4">



        <h2 class="fw-bold">



            <i class="bi bi-luggage-fill text-success"></i>



            Solicitudes de vacaciones



        </h2>



    </div>





    <!--=====================================

    MENSAJE ACEPTADO

    =====================================-->



    <?php if(isset($_GET["aceptado"])){ ?>



        <div class="alert alert-success alert-dismissible fade show">



            <i class="bi bi-check-circle-fill"></i>



            La solicitud de vacaciones fue aceptada correctamente.



            <button

                type="button"

                class="btn-close"

                data-bs-dismiss="alert">

            </button>



        </div>



    <?php } ?>





    <!--=====================================

    MENSAJE RECHAZADO

    =====================================-->



    <?php if(isset($_GET["rechazado"])){ ?>



        <div class="alert alert-danger alert-dismissible fade show">



            <i class="bi bi-ban-fill"></i>



            La solicitud de vacaciones fue rechazada correctamente.



            <button

                type="button"

                class="btn-close"

                data-bs-dismiss="alert">

            </button>



        </div>



    <?php } ?>





    <!--=====================================

    FILTROS

    =====================================-->



    <div class="card shadow border-0 rounded-4 mb-4">



        <div class="card-body">



            <form

                method="GET"

                action="dashboard.php">



                <input

                    type="hidden"

                    name="page"

                    value="SolicitudesVacacionesRH">





                <div class="row g-3">





                    <!-- BUSCAR -->



                    <div class="col-md-4">



                        <input

                            type="text"

                            name="buscar"

                            class="form-control"

                            placeholder="Nombre de empleado..."

                            value="<?php echo htmlspecialchars($buscar); ?>">



                    </div>





                    <!-- ESTATUS -->



                    <div class="col-md-3">



                        <select

                            name="estatus"

                            class="form-select">



                            <option value="">



                                Todos los estatus



                            </option>



                            <?php foreach($estatuses as $es){ ?>



                                <option

                                    value="<?php echo $es["id"]; ?>"

                                    <?php

                                    if($estatus == $es["id"])

                                        echo "selected";

                                    ?>>



                                    <?php

                                    echo htmlspecialchars(

                                        $es["nombre"]

                                    );

                                    ?>



                                </option>



                            <?php } ?>



                        </select>



                    </div>





                    <!-- FILTRAR -->



                    <div class="col-md-2 d-grid">



                        <button

                            type="submit"

                            class="btn btn-success">



                            <i class="bi bi-funnel-fill"></i>



                            Filtrar



                        </button>



                    </div>





                </div>



            </form>



        </div>



    </div>





    <!--=====================================

    MENSAJE EDITADO

    =====================================-->



    <?php if(isset($_GET["editado"])){ ?>



        <div class="alert alert-success alert-dismissible fade show">



            <i class="bi bi-check-circle-fill"></i>



            El registro fue actualizado correctamente.



            <button

                type="button"

                class="btn-close"

                data-bs-dismiss="alert">

            </button>



        </div>



    <?php } ?>





    <!--=====================================

    MENSAJE ELIMINADO

    =====================================-->



    <?php if(isset($_GET["eliminado"])){ ?>



        <div class="alert alert-danger alert-dismissible fade show">



            <i class="bi bi-trash-fill"></i>



            El registro fue eliminado correctamente.



            <button

                type="button"

                class="btn-close"

                data-bs-dismiss="alert">

            </button>



        </div>



    <?php } ?>





    <!--=====================================

    MENSAJE ERROR

    =====================================-->



    <?php if(isset($_GET["error"])){ ?>



        <div class="alert alert-warning alert-dismissible fade show">



            <i class="bi bi-exclamation-triangle-fill"></i>



            Ocurrió un error al realizar la operación.



            <button

                type="button"

                class="btn-close"

                data-bs-dismiss="alert">

            </button>



        </div>



    <?php } ?>





    <!--=====================================

    TABLA

    =====================================-->



    <div class="card shadow border-0 rounded-4">



        <div class="card-body">



            <div class="table-responsive">



                <table class="table table-hover align-middle">



                    <thead class="table-success">



                        <tr>



                            <th>ID</th>



                            <th>Empleado</th>



                            <th>Departamento</th>



                            <th>Jefe de Área</th>



                            <th>Fecha de inicio</th>



                            <th>Fecha de término</th>



                            <th>Días tomados</th>



                            <th>Detalle</th>



                            <th width="180">Acciones</th>



                        </tr>



                    </thead>





                    <tbody>





                    <?php foreach($resgistros as $resgistro){ ?>





                        <?php



                        $idRegistro = $resgistro["idRegistro"];



                        $detalleActual =

                            $detallesVacaciones[$idRegistro]

                            ?? [];



                        ?>





                        <tr>





                            <!-- ID -->



                            <td>



                                <?php

                                echo $idRegistro;

                                ?>



                            </td>





                            <!-- EMPLEADO -->



                            <td>



                                <?php

                                echo htmlspecialchars(

                                    $resgistro["empleado"]

                                );

                                ?>



                            </td>





                            <!-- DEPARTAMENTO -->



                            <td>



                                <?php

                                echo htmlspecialchars(

                                    $resgistro["departamento"]

                                );

                                ?>



                            </td>





                            <!-- JEFE -->



                            <td>



                                <?php

                                echo htmlspecialchars(

                                    $resgistro["jefe"]

                                );

                                ?>



                            </td>





                            <!-- FECHA INICIO -->



                            <td>



                                <?php

                                echo htmlspecialchars(

                                    $resgistro["fechainicio"]

                                );

                                ?>



                            </td>





                            <!-- FECHA TERMINO -->



                            <td>



                                <?php

                                echo htmlspecialchars(

                                    $resgistro["fechafinal"]

                                );

                                ?>



                            </td>





                            <!-- DIAS -->



                            <td>



                                <?php

                                echo htmlspecialchars(

                                    $resgistro["diastomados"]

                                );

                                ?>



                            </td>





                            <!--=================================

                            BOTON DETALLE

                            ==================================-->



                            <td>



                                <a

                                    href="#modalDetalleRH<?php echo $idRegistro; ?>"

                                    class="btn-detalle-vacaciones"

                                    title="Ver detalle de vacaciones">



                                    <i class="bi bi-eye-fill"></i>



                                    Ver detalle



                                </a>



                            </td>





                            <!--=================================

                            ACCIONES

                            ==================================-->



                            <td>





                                <!-- ACEPTAR -->



                                <form

                                    action="../controllers/AceptarVacacionesRHController.php"

                                    method="POST"

                                    class="d-inline">



                                    <input

                                        type="hidden"

                                        name="idRegistro"

                                        value="<?php

                                        echo $idRegistro;

                                        ?>">



                                    <input

                                        type="hidden"

                                        name="accion"

                                        value="aceptar">



                                    <button

                                        type="submit"

                                        class="btn btn-success btn-sm"

                                        title="Aceptar solicitud">



                                        <i class="bi bi-check-circle-fill"></i>



                                    </button>



                                </form>





                                <!-- RECHAZAR -->



                                <form

                                    action="../controllers/AceptarVacacionesRHController.php"

                                    method="POST"

                                    class="d-inline"

                                    onsubmit="return confirm('¿Desea rechazar esta solicitud de vacaciones?');">



                                    <input

                                        type="hidden"

                                        name="idRegistro"

                                        value="<?php

                                        echo $idRegistro;

                                        ?>">



                                    <input

                                        type="hidden"

                                        name="accion"

                                        value="rechazar">



                                    <button

                                        type="submit"

                                        class="btn btn-danger btn-sm"

                                        title="Rechazar solicitud">



                                        <i class="bi bi-ban"></i>



                                    </button>



                                </form>





                            </td>





                        </tr>





                    <?php } ?>





                    <?php if(count($resgistros) == 0){ ?>





                        <tr>



                            <td

                                colspan="9"

                                class="text-center py-5">



                                <i class="bi bi-inbox fs-1"></i>



                                <br><br>



                                No existen solicitudes pendientes

                                de vacaciones por el momento.



                            </td>



                        </tr>





                    <?php } ?>





                    </tbody>



                </table>



            </div>



        </div>



    </div>





</div>





<!--====================================================

MODALES DE DETALLE

\=====================================================-->



<?php foreach($resgistros as $resgistro){ ?>





    <?php



    $idRegistro = $resgistro["idRegistro"];



    $detalleActual =

        $detallesVacaciones[$idRegistro]

        ?? [];



    ?>





    <div

        class="modal-css"

        id="modalDetalleRH<?php echo $idRegistro; ?>">





        <div class="modal-css-contenido">





            <!--=========================================

            ENCABEZADO

            =========================================-->



            <div class="modal-css-header">



                <h5>



                    <i class="bi bi-calendar-check-fill me-2"></i>



                    Detalle de vacaciones



                </h5>





                <a

                    href="#"

                    class="modal-css-cerrar"

                    title="Cerrar">



                    &times;



                </a>



            </div>





            <!--=========================================

            CUERPO

            =========================================-->



            <div class="modal-css-body">





                <!--=====================================

                INFORMACION GENERAL

                ======================================-->



                <div class="info-vacaciones">





                    <div class="info-vacacion-item">



                        <small>Empleado</small>



                        <strong>



                            <?php

                            echo htmlspecialchars(

                                $resgistro["empleado"]

                            );

                            ?>



                        </strong>



                    </div>





                    <div class="info-vacacion-item">



                        <small>Departamento</small>



                        <strong>



                            <?php

                            echo htmlspecialchars(

                                $resgistro["departamento"]

                            );

                            ?>



                        </strong>



                    </div>





                    <div class="info-vacacion-item">



                        <small>Jefe de área</small>



                        <strong>



                            <?php

                            echo htmlspecialchars(

                                $resgistro["jefe"]

                            );

                            ?>



                        </strong>



                    </div>





                    <div class="info-vacacion-item">



                        <small>ID de solicitud</small>



                        <strong>



                            #<?php echo $idRegistro; ?>



                        </strong>



                    </div>





                    <div class="info-vacacion-item">



                        <small>Fecha de inicio</small>



                        <strong>



                            <?php

                            echo htmlspecialchars(

                                $resgistro["fechainicio"]

                            );

                            ?>



                        </strong>



                    </div>





                    <div class="info-vacacion-item">



                        <small>Fecha de regreso</small>



                        <strong>



                            <?php

                            echo htmlspecialchars(

                                $resgistro["fechafinal"]

                            );

                            ?>



                        </strong>



                    </div>





                </div>





                <!--=====================================

                DETALLE

                ======================================-->



                <h6 class="titulo-detalle">



                    <i class="bi bi-list-check me-1"></i>



                    Días solicitados



                </h6>





                <?php if(count($detalleActual) > 0){ ?>





                    <div class="table-responsive">



                        <table class="tabla-detalle">





                            <thead>



                                <tr>



                                    <th>



                                        Fecha



                                    </th>



                                    <th>



                                        Cantidad



                                    </th>



                                    <th>



                                        Tipo



                                    </th>



                                </tr>



                            </thead>





                            <tbody>





                            <?php



                            $totalDetalle = 0;



                            foreach($detalleActual as $detalle){



                                $cantidad =

                                    (float)$detalle["cantidad"];



                                $totalDetalle += $cantidad;



                            ?>





                                <tr>





                                    <td>



                                        <?php

                                        echo htmlspecialchars(

                                            $detalle["fecha"]

                                        );

                                        ?>



                                    </td>





                                    <td>



                                        <?php

                                        echo $cantidad;

                                        ?>



                                    </td>





                                    <td>





                                        <?php if($cantidad == 0.5){ ?>





                                            <span

                                                class="badge-medio-dia">



                                                <i class="bi bi-clock-fill"></i>



                                                Medio día



                                            </span>





                                        <?php }else{ ?>





                                            <span

                                                class="badge-dia-completo">



                                                <i class="bi bi-calendar-check-fill"></i>



                                                Día completo



                                            </span>





                                        <?php } ?>





                                    </td>





                                </tr>





                            <?php } ?>





                            </tbody>





                        </table>



                    </div>





                    <!-- TOTAL -->



                    <div class="total-detalle">



                        <div class="total-detalle-contenido">



                            Total solicitado:



                            <span>



                                <?php

                                echo $totalDetalle;

                                ?>



                                <?php

                                echo

                                ($totalDetalle == 1)

                                ? " día"

                                : " días";

                                ?>



                            </span>



                        </div>



                    </div>





                <?php }else{ ?>





                    <div class="alert alert-warning">



                        <i class="bi bi-exclamation-triangle-fill me-2"></i>



                        No se encontró detalle de días para esta

                        solicitud.



                    </div>





                <?php } ?>





            </div>





            <!--=========================================

            PIE

            =========================================-->



            <div class="modal-css-footer">





                <a

                    href="#"

                    class="modal-btn modal-btn-cerrar">



                    <i class="bi bi-x-circle"></i>



                    Cerrar



                </a>





            </div>





        </div>



    </div>





<?php } ?>


<!-- BÚFER DE CARGA: solo se muestra al aceptar una solicitud -->
<style>
.loader-vacaciones-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 200000;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(0, 0, 0, .60);
}

.loader-vacaciones-overlay.activo {
    display: flex;
}

.loader-vacaciones-card {
    width: 100%;
    max-width: 430px;
    padding: 32px 28px;
    text-align: center;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 15px 45px rgba(0, 0, 0, .25);
}

.loader-vacaciones-card h5 {
    margin-top: 18px;
    color: #198754;
    font-weight: 700;
}
</style>

<div id="loaderVacacionesRH" class="loader-vacaciones-overlay" aria-live="polite" aria-busy="true">
    <div class="loader-vacaciones-card">
        <div class="spinner-border text-success" role="status" style="width: 3.5rem; height: 3.5rem;">
            <span class="visually-hidden">Procesando...</span>
        </div>
        <h5>Procesando solicitud</h5>
        <p class="text-muted mb-2">Estamos registrando la aprobación y enviando los correos.</p>
        <small class="text-muted">Por favor, espera y no cierres esta ventana.</small>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById("loaderVacacionesRH");
    if (!loader) return;

    // La misma URL se utiliza para aceptar y rechazar; mostrar el búfer
    // únicamente cuando el campo oculto accion tiene el valor "aceptar".
    document.querySelectorAll('form[action*="AceptarVacacionesRHController.php"]').forEach(function (formulario) {
        formulario.addEventListener("submit", function (event) {
            const accion = formulario.querySelector('input[name="accion"]');
            if (!accion || accion.value !== "aceptar") return;
            if (!formulario.checkValidity()) return;

            loader.classList.add("activo");
            // No deshabilitamos el botón para no alterar el POST ni el controller.
        });
    });
});
</script>
