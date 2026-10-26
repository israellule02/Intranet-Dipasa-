<?php



require_once("../config/conexion.php");



if (session_status() === PHP_SESSION_NONE) {

    session_start();

}





/*====================================================

OBTENER FILTROS

====================================================*/



$buscar  = $_GET["buscar"] ?? "";

$estatus = $_GET["estatus"] ?? "";





/*====================================================

OBTENER USUARIO ACTUAL

====================================================*/



if (!isset($_SESSION["idUsuario"])) {

    header("Location: ../index.php");

    exit();

}



$idUsuario = (int)$_SESSION["idUsuario"];





/*====================================================

OBTENER ESTATUS

====================================================*/



$sqlEstatus = $conexion->prepare("

    SELECT *

    FROM estatusvac

    ORDER BY nombre

");



$sqlEstatus->execute();



$estatuses = $sqlEstatus->fetchAll(PDO::FETCH_ASSOC);





/*====================================================

CONSULTAR SOLICITUDES PENDIENTES DEL JEFE

====================================================*/



$sql = "



    SELECT



        r.idRegistro,

        r.idJefe,

        r.fechainicio,

        r.fechafinal,

        r.diastomados,

        r.idUsuario,



        u.idDep,



        d.nombre AS departamento,



        CONCAT(

            u.nombre,

            ' ',

            u.apellidos

        ) AS empleado,



        es.nombre AS estatus



    FROM registrovac r



    INNER JOIN usuarios u

        ON r.idUsuario = u.id



    INNER JOIN departamentos d

        ON u.idDep = d.idDepartamento



    INNER JOIN estatusvac es

        ON r.idValidacionJefe = es.id



    WHERE



        r.idValidacionJefe = 2



        AND r.idJefe = :idJefe



";





/*====================================================

BUSCADOR

====================================================*/



if ($buscar != "") {



    $sql .= "



        AND (



            u.nombre LIKE :buscar



            OR u.apellidos LIKE :buscar



            OR CONCAT(

                u.nombre,

                ' ',

                u.apellidos

            ) LIKE :buscar



        )



    ";

}





/*

    NOTA:

    No agregamos filtro de estatus aquí porque esta pantalla

    muestra exclusivamente solicitudes pendientes del jefe

    (idValidacionJefe = 2).

*/





/*====================================================

ORDEN

====================================================*/



$sql .= "



    ORDER BY r.idRegistro DESC



";





/*====================================================

PREPARAR CONSULTA

====================================================*/



$stmt = $conexion->prepare($sql);





/*====================================================

PARAMETROS

====================================================*/



$stmt->bindValue(

    ":idJefe",

    $idUsuario,

    PDO::PARAM_INT

);





if ($buscar != "") {



    $stmt->bindValue(

        ":buscar",

        "%" . $buscar . "%"

    );

}





/*====================================================

EJECUTAR

====================================================*/



$stmt->execute();



$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);





/*====================================================

OBTENER DETALLE DE VACACIONES

====================================================*/



$detallesVacaciones = [];





foreach ($registros as $registro) {



    $idRegistro = (int)$registro["idRegistro"];





    $sqlDetalle = $conexion->prepare("



        SELECT



            idDetalle,

            fecha,

            cantidad



        FROM registrovac_detalle



        WHERE idRegistro = :idRegistro



        ORDER BY fecha ASC



    ");





    $sqlDetalle->bindValue(

        ":idRegistro",

        $idRegistro,

        PDO::PARAM_INT

    );





    $sqlDetalle->execute();





    $detallesVacaciones[$idRegistro] =

        $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);

}



?>





<div class="container-fluid">





    <!--================================================

    ENCABEZADO

    =================================================-->



    <div class="d-flex justify-content-between align-items-center mb-4">



        <h2 class="fw-bold mb-0">



            <i class="bi bi-luggage-fill text-success"></i>



            Solicitudes de vacaciones



        </h2>



    </div>





    <!--================================================

    MENSAJE ACEPTADO

    =================================================-->



    <?php if (isset($_GET["aceptado"])) { ?>



        <div class="alert alert-success alert-dismissible fade show">



            <i class="bi bi-check-circle-fill me-2"></i>



            La solicitud de vacaciones fue aceptada correctamente.



            <button

                type="button"

                class="btn-close"

                data-bs-dismiss="alert">

            </button>



        </div>



    <?php } ?>





    <!--================================================

    MENSAJE RECHAZADO

    =================================================-->



    <?php if (isset($_GET["rechazado"])) { ?>



        <div class="alert alert-danger alert-dismissible fade show">



            <i class="bi bi-ban-fill me-2"></i>



            La solicitud de vacaciones fue rechazada correctamente.



            <button

                type="button"

                class="btn-close"

                data-bs-dismiss="alert">

            </button>



        </div>



    <?php } ?>





    <!--================================================

    MENSAJE EDITADO

    =================================================-->



    <?php if (isset($_GET["editado"])) { ?>



        <div class="alert alert-success alert-dismissible fade show">



            <i class="bi bi-check-circle-fill me-2"></i>



            El registro fue actualizado correctamente.



            <button

                type="button"

                class="btn-close"

                data-bs-dismiss="alert">

            </button>



        </div>



    <?php } ?>





    <!--================================================

    MENSAJE ELIMINADO

    =================================================-->



    <?php if (isset($_GET["eliminado"])) { ?>



        <div class="alert alert-danger alert-dismissible fade show">



            <i class="bi bi-trash-fill me-2"></i>



            El registro fue eliminado correctamente.



            <button

                type="button"

                class="btn-close"

                data-bs-dismiss="alert">

            </button>



        </div>



    <?php } ?>





    <!--================================================

    MENSAJE ERROR

    =================================================-->



    <?php if (isset($_GET["error"])) { ?>



        <div class="alert alert-warning alert-dismissible fade show">



            <i class="bi bi-exclamation-triangle-fill me-2"></i>



            Ocurrió un error al realizar la operación.



            <button

                type="button"

                class="btn-close"

                data-bs-dismiss="alert">

            </button>



        </div>



    <?php } ?>





    <!--================================================

    FILTROS

    =================================================-->



    <div class="card shadow-sm border-0 rounded-4 mb-4">



        <div class="card-body p-4">



            <form

                method="GET"

                action="dashboard.php">



                <input

                    type="hidden"

                    name="page"

                    value="solicitudes">





                <div class="row g-3 align-items-end">





                    <!-- BUSCAR -->



                    <div class="col-lg-8 col-md-8">



                        <label class="form-label fw-semibold">



                            <i class="bi bi-search me-1"></i>



                            Buscar empleado



                        </label>



                        <input

                            type="text"

                            name="buscar"

                            class="form-control"

                            placeholder="Nombre o apellidos..."

                            value="<?= htmlspecialchars($buscar); ?>">



                    </div>





                    <!-- FILTRAR -->



                    <div class="col-lg-2 col-md-2 d-grid">



                        <button

                            type="submit"

                            class="btn btn-success">



                            <i class="bi bi-funnel-fill me-1"></i>



                            Filtrar



                        </button>



                    </div>





                    <!-- LIMPIAR -->



                    <div class="col-lg-2 col-md-2 d-grid">



                        <a

                            href="dashboard.php?page=solicitudes"

                            class="btn btn-outline-secondary">



                            <i class="bi bi-x-circle me-1"></i>



                            Limpiar



                        </a>



                    </div>





                </div>



            </form>



        </div>



    </div>





    <!--================================================

    TABLA

    =================================================-->



    <div class="card shadow border-0 rounded-4">



        <div class="card-body">



            <div class="table-responsive">



                <table class="table table-hover align-middle">



                    <thead class="table-success">



                        <tr>



                            <th>

                                ID

                            </th>



                            <th>

                                Empleado

                            </th>



                            <th>

                                Departamento

                            </th>



                            <th>

                                Fecha de inicio

                            </th>



                            <th>

                                Fecha de regreso

                            </th>



                            <th class="text-center">

                                Días tomados

                            </th>



                            <th class="text-center">

                                Detalle

                            </th>



                            <th

                                width="180"

                                class="text-center">



                                Acciones



                            </th>



                        </tr>



                    </thead>





                    <tbody>





                        <?php if (count($registros) > 0) { ?>





                            <?php foreach ($registros as $registro) { ?>





                                <?php



                                $idRegistro =

                                    (int)$registro["idRegistro"];



                                $detalle =

                                    $detallesVacaciones[$idRegistro]

                                    ?? [];



                                ?>





                                <tr>





                                    <!-- ID -->



                                    <td>



                                        <span class="fw-semibold">



                                            <?= htmlspecialchars(

                                                $idRegistro

                                            ); ?>



                                        </span>



                                    </td>





                                    <!-- EMPLEADO -->



                                    <td>



                                        <div class="fw-semibold">



                                            <?= htmlspecialchars(

                                                $registro["empleado"]

                                            ); ?>



                                        </div>



                                    </td>





                                    <!-- DEPARTAMENTO -->



                                    <td>



                                        <?= htmlspecialchars(

                                            $registro["departamento"]

                                        ); ?>



                                    </td>





                                    <!-- FECHA INICIO -->



                                    <td>



                                        <?php



                                        echo date(

                                            "d/m/Y",

                                            strtotime(

                                                $registro["fechainicio"]

                                            )

                                        );



                                        ?>



                                    </td>





                                    <!-- FECHA REGRESO -->



                                    <td>



                                        <?php



                                        echo date(

                                            "d/m/Y",

                                            strtotime(

                                                $registro["fechafinal"]

                                            )

                                        );



                                        ?>



                                    </td>





                                    <!-- DIAS TOMADOS -->



                                    <td class="text-center">



                                        <span class="badge bg-success fs-6">



                                            <?= rtrim(

                                                rtrim(

                                                    number_format(

                                                        (float)$registro["diastomados"],

                                                        2,

                                                        ".",

                                                        ""

                                                    ),

                                                    "0"

                                                ),

                                                "."

                                            ); ?>



                                            día<?=

                                                ((float)$registro["diastomados"] == 1)

                                                ? ""

                                                : "s";

                                            ?>



                                        </span>



                                    </td>





                                    <!-- DETALLE -->



                                    <td class="text-center">





                                        <!--

                                            ESTE ES EL ÚNICO BOTÓN

                                            DE VER DETALLE.

                                        -->



                                        <a

                                            href="#modalDetalle<?= $idRegistro; ?>"

                                            class="btn-detalle-vacaciones"

                                            title="Ver detalle de vacaciones">



                                            <i class="bi bi-eye-fill"></i>

                                            Ver detalle



                                        </a>





                                    </td>





                                    <!-- ACCIONES -->



                                    <td class="text-center">





                                        <!-- ACEPTAR -->



                                        <form

                                            action="../controllers/AceptarVacacionesJefeController.php"

                                            method="POST"

                                            class="d-inline">



                                            <input

                                                type="hidden"

                                                name="idRegistro"

                                                value="<?= $idRegistro; ?>">



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

                                            action="../controllers/AceptarVacacionesJefeController.php"

                                            method="POST"

                                            class="d-inline">



                                            <input

                                                type="hidden"

                                                name="idRegistro"

                                                value="<?= $idRegistro; ?>">



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





                        <?php } else { ?>





                            <tr>



                                <td

                                    colspan="8"

                                    class="text-center py-5">



                                    <i class="bi bi-inbox fs-1 text-muted"></i>



                                    <div class="mt-3 text-muted">



                                        No existen solicitudes pendientes

                                        de vacaciones por el momento.



                                    </div>



                                </td>



                            </tr>





                        <?php } ?>





                    </tbody>



                </table>



            </div>



        </div>



    </div>





    <!--================================================

    MODALES DE DETALLE - PURO CSS

    =================================================-->



<?php foreach ($registros as $registro) { ?>



    <?php



    $idRegistro = (int)$registro["idRegistro"];



    $detalle = $detallesVacaciones[$idRegistro] ?? [];



    ?>



    <div

        class="modal-css"

        id="modalDetalle<?= $idRegistro; ?>">



        <div class="modal-css-contenido">



            <!-- HEADER -->

            <div class="modal-css-header">



                <div>



                    <h3>

                        <i class="bi bi-calendar-check-fill"></i>

                        Detalle de vacaciones

                    </h3>



                    <small>

                        Solicitud #<?= $idRegistro; ?>

                    </small>



                </div>



                <a

                    href="#"

                    class="modal-css-cerrar"

                    aria-label="Cerrar">&times;</a>



            </div>



            <!-- CUERPO -->

            <div class="modal-css-body">



                <div class="modal-info-grid">



                    <div class="modal-info-card">

                        <small>

                            <i class="bi bi-person-fill"></i>

                            Empleado

                        </small>

                        <strong>

                            <?= htmlspecialchars($registro["empleado"]); ?>

                        </strong>

                    </div>



                    <div class="modal-info-card">

                        <small>

                            <i class="bi bi-building"></i>

                            Departamento

                        </small>

                        <strong>

                            <?= htmlspecialchars($registro["departamento"]); ?>

                        </strong>

                    </div>



                    <div class="modal-info-card total">

                        <small>

                            <i class="bi bi-sun-fill"></i>

                            Total solicitado

                        </small>

                        <strong>

                            <?= rtrim(rtrim(number_format((float)$registro["diastomados"], 2, ".", ""), "0"), "."); ?>

                            día<?= ((float)$registro["diastomados"] == 1) ? "" : "s"; ?>

                        </strong>

                    </div>



                    <div class="modal-info-card">

                        <small>

                            <i class="bi bi-calendar-event"></i>

                            Fecha de inicio

                        </small>

                        <strong>

                            <?= date("d/m/Y", strtotime($registro["fechainicio"])); ?>

                        </strong>

                    </div>



                    <div class="modal-info-card">

                        <small>

                            <i class="bi bi-calendar-event"></i>

                            Fecha de regreso

                        </small>

                        <strong>

                            <?= date("d/m/Y", strtotime($registro["fechafinal"])); ?>

                        </strong>

                    </div>



                </div>



                <h4 class="modal-css-titulo">

                    <i class="bi bi-calendar3"></i>

                    Días solicitados

                </h4>



                <?php if (count($detalle) > 0) { ?>



                    <div class="modal-tabla-contenedor">



                        <table class="modal-tabla">



                            <thead>

                                <tr>

                                    <th>Fecha</th>

                                    <th>Día</th>

                                    <th>Tipo</th>

                                    <th class="text-center">Cantidad</th>

                                </tr>

                            </thead>



                            <tbody>



                                <?php

                                $totalDetalle = 0;



                                $nombresDias = [

                                    0 => "Domingo",

                                    1 => "Lunes",

                                    2 => "Martes",

                                    3 => "Miércoles",

                                    4 => "Jueves",

                                    5 => "Viernes",

                                    6 => "Sábado"

                                ];

                                ?>



                                <?php foreach ($detalle as $dia) { ?>



                                    <?php

                                    $fecha = new DateTime($dia["fecha"]);

                                    $cantidad = (float)$dia["cantidad"];

                                    $totalDetalle += $cantidad;

                                    $diaSemana = (int)$fecha->format("w");

                                    ?>



                                    <tr>



                                        <td>

                                            <i class="bi bi-calendar3 text-success"></i>

                                            <?= $fecha->format("d/m/Y"); ?>

                                        </td>



                                        <td>

                                            <?= $nombresDias[$diaSemana]; ?>

                                        </td>



                                        <td>

                                            <?php if ($cantidad == 1) { ?>

                                                <span class="modal-badge-completo">

                                                    <i class="bi bi-sun-fill"></i>

                                                    Día completo

                                                </span>

                                            <?php } else { ?>

                                                <span class="modal-badge-medio">

                                                    <i class="bi bi-sun-half"></i>

                                                    Medio día

                                                </span>

                                            <?php } ?>

                                        </td>



                                        <td class="text-center">

                                            <strong>

                                                <?= $cantidad == 1 ? "1" : "0.5"; ?>

                                            </strong>

                                            día

                                        </td>



                                    </tr>



                                <?php } ?>



                            </tbody>



                            <tfoot>

                                <tr>

                                    <th colspan="3" class="text-end">

                                        Total:

                                    </th>

                                    <th class="text-center">

                                        <?= rtrim(rtrim(number_format($totalDetalle, 2, ".", ""), "0"), "."); ?>

                                        días

                                    </th>

                                </tr>

                            </tfoot>



                        </table>



                    </div>



                <?php } else { ?>



                    <div class="modal-sin-detalle">

                        <i class="bi bi-exclamation-triangle-fill"></i>

                        Esta solicitud no tiene detalle de días registrado.

                    </div>



                <?php } ?>



            </div>



            <!-- FOOTER -->

            <div class="modal-css-footer">



                <a

                    href="#"

                    class="modal-btn modal-btn-cerrar">

                    <i class="bi bi-x-circle"></i>

                    Cerrar

                </a>



                <form

                    action="../controllers/AceptarVacacionesJefeController.php"

                    method="POST"

                    style="display:inline;">



                    <input

                        type="hidden"

                        name="idRegistro"

                        value="<?= $idRegistro; ?>">



                    <input

                        type="hidden"

                        name="accion"

                        value="aceptar">



                    <button

                        type="submit"

                        class="modal-btn modal-btn-aceptar">

                        <i class="bi bi-check-circle-fill"></i>

                        Aceptar solicitud

                    </button>



                </form>



                <form

                    action="../controllers/AceptarVacacionesJefeController.php"

                    method="POST"

                    style="display:inline;">



                    <input

                        type="hidden"

                        name="idRegistro"

                        value="<?= $idRegistro; ?>">



                    <input

                        type="hidden"

                        name="accion"

                        value="rechazar">



                    <button

                        type="submit"

                        class="modal-btn modal-btn-rechazar">

                        <i class="bi bi-ban"></i>

                        Rechazar

                    </button>



                </form>



            </div>



        </div>



    </div>



<?php } ?>





<style>

/* =====================================================

   BOTÓN VER DETALLE

   ===================================================== */

.btn-detalle-vacaciones {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 6px;

    padding: 7px 12px;

    border-radius: 7px;

    background: #0d6efd;

    color: #fff;

    text-decoration: none;

    font-size: 14px;

    font-weight: 500;

    border: 1px solid #0d6efd;

    cursor: pointer;

    transition: .2s ease;

}



.btn-detalle-vacaciones:hover {

    background: #0b5ed7;

    color: #fff;

    transform: translateY(-1px);

    box-shadow: 0 4px 10px rgba(0,0,0,.15);

}



/* =====================================================

   MODAL

   ===================================================== */

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

    transition: opacity .25s ease, visibility .25s ease;

}



.modal-css:target {

    visibility: visible;

    opacity: 1;

    pointer-events: auto;

}



.modal-css-contenido {

    width: 100%;

    max-width: 900px;

    max-height: 90vh;

    background: #fff;

    border-radius: 16px;

    box-shadow: 0 20px 60px rgba(0,0,0,.30);

    overflow: hidden;

    display: flex;

    flex-direction: column;

    transform: translateY(-20px) scale(.97);

    transition: transform .25s ease;

}



.modal-css:target .modal-css-contenido {

    transform: translateY(0) scale(1);

}



/* HEADER */

.modal-css-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 18px 22px;

    background: #198754;

    color: #fff;

    flex-shrink: 0;

}



.modal-css-header h3 {

    margin: 0;

    font-size: 20px;

    font-weight: 700;

}



.modal-css-header small {

    display: block;

    margin-top: 3px;

    font-size: 13px;

    opacity: .9;

}



.modal-css-cerrar {

    width: 34px;

    height: 34px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    color: #fff;

    text-decoration: none;

    font-size: 27px;

    line-height: 1;

    transition: background .2s ease, transform .2s ease;

}



.modal-css-cerrar:hover {

    color: #fff;

    background: rgba(255,255,255,.20);

    transform: rotate(90deg);

}



/* BODY */

.modal-css-body {

    padding: 24px;

    overflow-y: auto;

    flex: 1;

}



/* INFO */

.modal-info-grid {

    display: grid;

    grid-template-columns: repeat(3, minmax(0,1fr));

    gap: 14px;

    margin-bottom: 25px;

}



.modal-info-card {

    background: #f8f9fa;

    border: 1px solid #e9ecef;

    border-radius: 10px;

    padding: 15px;

}



.modal-info-card small {

    display: block;

    color: #6c757d;

    font-size: 12px;

    margin-bottom: 5px;

}



.modal-info-card strong {

    display: block;

    color: #212529;

    font-size: 15px;

}



.modal-info-card.total {

    background: #198754;

    border-color: #198754;

}



.modal-info-card.total small,

.modal-info-card.total strong {

    color: #fff;

}



.modal-info-card.total strong {

    font-size: 18px;

}



/* TITULO */

.modal-css-titulo {

    margin: 0 0 12px;

    font-size: 16px;

    font-weight: 700;

    color: #212529;

}



/* TABLA */

.modal-tabla-contenedor {

    width: 100%;

    overflow-x: auto;

    border: 1px solid #dee2e6;

    border-radius: 10px;

}



.modal-tabla {

    width: 100%;

    border-collapse: collapse;

}



.modal-tabla th {

    background: #f1f3f5;

    color: #212529;

    font-size: 13px;

    font-weight: 700;

    text-align: left;

    padding: 12px;

    border-bottom: 1px solid #dee2e6;

}



.modal-tabla td {

    padding: 11px 12px;

    border-bottom: 1px solid #eee;

    font-size: 14px;

    color: #343a40;

}



.modal-tabla tbody tr:hover {

    background: #f8f9fa;

}



.modal-tabla tbody tr:last-child td {

    border-bottom: none;

}



.modal-tabla tfoot th {

    background: #d1e7dd;

    color: #0f5132;

    padding: 12px;

}



/* BADGES */

.modal-badge-completo,

.modal-badge-medio {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    padding: 5px 9px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: 600;

}



.modal-badge-completo {

    background: #d1e7dd;

    color: #0f5132;

}



.modal-badge-medio {

    background: #fff3cd;

    color: #664d03;

}



.modal-sin-detalle {

    padding: 15px;

    border-radius: 8px;

    background: #fff3cd;

    color: #664d03;

}



/* FOOTER */

.modal-css-footer {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 10px;

    padding: 16px 22px;

    border-top: 1px solid #dee2e6;

    background: #fff;

    flex-shrink: 0;

}



.modal-btn {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 6px;

    padding: 9px 15px;

    border-radius: 7px;

    border: none;

    font-size: 14px;

    font-weight: 600;

    text-decoration: none;

    cursor: pointer;

    transition: transform .2s ease, box-shadow .2s ease;

}



.modal-btn:hover {

    transform: translateY(-1px);

    box-shadow: 0 4px 10px rgba(0,0,0,.12);

}



.modal-btn-cerrar {

    background: #6c757d;

    color: #fff;

}



.modal-btn-cerrar:hover {

    background: #5c636a;

    color: #fff;

}



.modal-btn-aceptar {

    background: #198754;

    color: #fff;

}



.modal-btn-aceptar:hover {

    background: #157347;

    color: #fff;

}



.modal-btn-rechazar {

    background: #dc3545;

    color: #fff;

}



.modal-btn-rechazar:hover {

    background: #bb2d3b;

    color: #fff;

}



/* RESPONSIVE */

@media (max-width: 768px) {

    .modal-css {

        padding: 10px;

    }



    .modal-css-contenido {

        max-height: 95vh;

        border-radius: 12px;

    }



    .modal-info-grid {

        grid-template-columns: 1fr;

    }



    .modal-css-body {

        padding: 16px;

    }



    .modal-css-footer {

        flex-wrap: wrap;

    }



    .modal-btn {

        flex: 1;

    }

}

</style>



<!-- BÚFER DE CARGA: aparece al confirmar una aceptación -->
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
.loader-vacaciones-overlay.activo { display: flex; }
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

<div id="loaderVacacionesJefe" class="loader-vacaciones-overlay" aria-live="polite" aria-busy="true">
    <div class="loader-vacaciones-card">
        <div class="spinner-border text-success" role="status" style="width: 3.5rem; height: 3.5rem;">
            <span class="visually-hidden">Procesando...</span>
        </div>
        <h5>Procesando solicitud</h5>
        <p class="text-muted mb-2">Estamos registrando la decisión sobre la solicitud.</p>
        <small class="text-muted">Por favor, espera y no cierres esta ventana.</small>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById("loaderVacacionesJefe");
    if (!loader) return;

    document.querySelectorAll('form[action*="AceptarVacacionesJefeController.php"]').forEach(function (formulario) {
        formulario.addEventListener("submit", function (event) {
            const campoAccion = formulario.querySelector('input[name="accion"]');
            if (!campoAccion) return;

            const accion = campoAccion.value;
            let mensaje = "";

            if (accion === "aceptar") {
                mensaje = "¿Está seguro de que desea aceptar esta solicitud de vacaciones?";
            } else if (accion === "rechazar") {
                mensaje = "¿Está seguro de que desea rechazar esta solicitud de vacaciones?";
            } else {
                return;
            }

            if (!window.confirm(mensaje)) {
                event.preventDefault();
                return;
            }

            if (accion === "aceptar") {
                loader.classList.add("activo");
            }
        });
    });
});
</script>
