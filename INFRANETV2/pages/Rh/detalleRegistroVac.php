<?php

require_once("../config/conexion.php");

if(!isset($_GET["id"])){

    echo "<div class='alert alert-danger'>
            Registro de vacaciones no encontrado.
          </div>";

    exit();

}

$id = $_GET["id"];


/*=========================================
OBTENER REGISTRO DE VACACIONES
=========================================*/

$sql = $conexion->prepare("

SELECT

    r.*,

    validacionJefe.nombre AS validacionJefe,

    validacionRh.nombre AS validacionRh,

    p.descripcion AS periodo,

    CONCAT(u.nombre,' ',u.apellidos) AS empleado,

    CONCAT(jefe.nombre,' ',jefe.apellidos) AS jefe,

    CONCAT(rh.nombre,' ',rh.apellidos) AS rh

FROM registrovac r

LEFT JOIN usuarios u
    ON r.idUsuario = u.id

LEFT JOIN usuarios jefe
    ON r.idJefe = jefe.id

LEFT JOIN usuarios rh
    ON r.idRh = rh.id

LEFT JOIN periodosvac p
    ON r.idPeriodo = p.idPeriodo

LEFT JOIN estatusvac validacionRh
    ON r.idValidacionRh = validacionRh.id

LEFT JOIN estatusvac validacionJefe
    ON r.idValidacionJefe = validacionJefe.id

WHERE r.idRegistro = :id

");


$sql->bindParam(
    ":id",
    $id,
    PDO::PARAM_INT
);

$sql->execute();

$vacaciones = $sql->fetch(PDO::FETCH_ASSOC);


if(!$vacaciones){

    echo "<div class='alert alert-danger'>
            El registro de vacaciones no existe.
          </div>";

    exit();

}


/*=========================================
OBTENER DETALLE DE VACACIONES
=========================================*/

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
    $id,
    PDO::PARAM_INT
);

$sqlDetalle->execute();

$detalles = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);


/*=========================================
CALCULAR TOTAL DEL DETALLE
=========================================*/

$totalDetalle = 0;

foreach($detalles as $detalle){

    $totalDetalle += (float)$detalle["cantidad"];

}

?>


<style>

/*=========================================
CONTENEDOR DEL DETALLE
=========================================*/

.detalle-vacaciones-card {

    border-radius: 18px;

    overflow: hidden;

}


/*=========================================
TABLA DE DETALLE
=========================================*/

.tabla-detalle-vacaciones {

    width: 100%;

    border-collapse: collapse;

}


.tabla-detalle-vacaciones th {

    background: #198754;

    color: white;

    padding: 12px;

    font-weight: 600;

}


.tabla-detalle-vacaciones td {

    padding: 12px;

    border-bottom: 1px solid #dee2e6;

    vertical-align: middle;

}


.tabla-detalle-vacaciones tbody tr:hover {

    background: #f8f9fa;

}


/*=========================================
BADGES
=========================================*/

.badge-dia-completo {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    background: #198754;

    color: white;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;

}


.badge-medio-dia {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    background: #ffc107;

    color: #212529;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;

}


/*=========================================
TOTAL
=========================================*/

.total-vacaciones {

    display: flex;

    justify-content: flex-end;

    margin-top: 15px;

}


.total-vacaciones-box {

    background: #f8f9fa;

    border: 1px solid #dee2e6;

    border-radius: 12px;

    padding: 12px 20px;

    font-weight: 600;

}


.total-vacaciones-box span {

    color: #198754;

    font-size: 20px;

}


/*=========================================
CAJAS DE INFORMACION
=========================================*/

.info-vacaciones {

    background: #f8f9fa;

    border-radius: 10px;

    padding: 12px 15px;

    border: 1px solid #e9ecef;

}


.info-vacaciones label {

    color: #495057;

}


/*=========================================
RESPONSIVE
=========================================*/

@media(max-width:768px){

    .tabla-detalle-vacaciones {

        min-width: 600px;

    }

}

</style>


<div class="container-fluid">


    <!--=========================================
    REGRESAR
    =========================================-->

    <a
        href="dashboard.php?page=historialVacaciones"
        class="btn btn-outline-success mb-4">

        <i class="bi bi-arrow-left"></i>

        Regresar

    </a>


    <!--=========================================
    CARD PRINCIPAL
    =========================================-->

    <div class="card shadow border-0 rounded-4 detalle-vacaciones-card">


        <!--=====================================
        ENCABEZADO
        ======================================-->

        <div class="card-header bg-success text-white">

            <h3 class="mb-0">

                <i class="bi bi-brightness-high-fill me-2"></i>

                Detalle de las vacaciones

            </h3>

        </div>


        <!--=====================================
        CUERPO
        ======================================-->

        <div class="card-body">


            <div class="row">


                <!--=================================
                EMPLEADO
                ==================================-->

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">

                        Empleado

                    </label>

                    <div class="info-vacaciones">

                        <?php
                        echo htmlspecialchars(
                            $vacaciones["empleado"]
                        );
                        ?>

                    </div>

                </div>


                <!--=================================
                PERIODO
                ==================================-->

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">

                        Periodo

                    </label>

                    <div class="info-vacaciones">

                        <?php
                        echo htmlspecialchars(
                            $vacaciones["periodo"]
                        );
                        ?>

                    </div>

                </div>


                <!--=================================
                FECHA INICIO
                ==================================-->

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">

                        Fecha de inicio

                    </label>

                    <div class="border rounded p-3 alert alert-success mb-0">

                        <?php
                        echo htmlspecialchars(
                            $vacaciones["fechainicio"]
                        );
                        ?>

                    </div>

                </div>


                <!--=================================
                FECHA REGRESO
                ==================================-->

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">

                        Fecha de regreso

                    </label>

                    <div class="border rounded p-3 alert alert-success mb-0">

                        <?php
                        echo htmlspecialchars(
                            $vacaciones["fechafinal"]
                        );
                        ?>

                    </div>

                </div>


                <!--=================================
                DIAS TOMADOS
                ==================================-->

                <div class="col-md-6 mb-3 mt-2">

                    <label class="fw-bold">

                        Días tomados

                    </label>

                    <div class="info-vacaciones">

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $vacaciones["diastomados"]
                            );
                            ?>

                        </strong>

                        <?php

                        $diasTomados =
                            (float)$vacaciones["diastomados"];

                        echo $diasTomados == 1
                            ? " día"
                            : " días";

                        ?>

                    </div>

                    <small class="text-muted">

                        No se consideran días de descanso
                        ni los días festivos.

                    </small>

                </div>


                <!--=================================
                FECHA SOLICITUD
                ==================================-->

                <div class="col-md-6 mb-3 mt-2">

                    <label class="fw-bold">

                        Fecha de solicitud

                    </label>

                    <div class="info-vacaciones">

                        <?php
                        echo htmlspecialchars(
                            $vacaciones["fechasolicitud"]
                        );
                        ?>

                    </div>

                </div>


                <!--=================================
                JEFE
                ==================================-->

                <div class="col-md-6 mb-3 mt-3">

                    <label class="fw-bold">

                        Jefe que autorizó

                    </label>

                    <div class="info-vacaciones">

                        <?php

                        echo !empty($vacaciones["jefe"])
                            ? htmlspecialchars(
                                $vacaciones["jefe"]
                              )
                            : "Pendiente";

                        ?>

                    </div>

                </div>


                <!--=================================
                RH
                ==================================-->

                <div class="col-md-6 mb-3 mt-3">

                    <label class="fw-bold">

                        Encargada de Recursos Humanos que autorizó

                    </label>

                    <div class="info-vacaciones">

                        <?php

                        echo !empty($vacaciones["rh"])
                            ? htmlspecialchars(
                                $vacaciones["rh"]
                              )
                            : "Pendiente";

                        ?>

                    </div>

                </div>


            </div>


            <!--=========================================
            SEPARADOR
            =========================================-->

            <hr class="my-4">


            <!--=========================================
            DETALLE DE FECHAS
            =========================================-->

            <div class="mb-4">

                <h4 class="fw-bold text-success mb-3">

                    <i class="bi bi-calendar3 me-2"></i>

                    Dias tomados 

                </h4>


                <?php if(count($detalles) > 0){ ?>


                    <div class="table-responsive">

                        <table
                            class="tabla-detalle-vacaciones">


                            <thead>

                                <tr>

                                    <th>

                                        #

                                    </th>

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

                            $contador = 1;

                            foreach($detalles as $detalle){

                                $cantidad =
                                    (float)$detalle["cantidad"];

                            ?>


                                <tr>


                                    <!-- NUMERO -->

                                    <td>

                                        <?php
                                        echo $contador++;
                                        ?>

                                    </td>


                                    <!-- FECHA -->

                                    <td>

                                        <strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $detalle["fecha"]
                                            );
                                            ?>

                                        </strong>

                                    </td>


                                    <!-- CANTIDAD -->

                                    <td>

                                        <?php
                                        echo $cantidad;
                                        ?>

                                    </td>


                                    <!-- TIPO -->

                                    <td>


                                        <?php
                                        if($cantidad == 0.5){
                                        ?>


                                            <span
                                                class="badge-medio-dia">

                                                <i
                                                    class="bi bi-clock-fill">
                                                </i>

                                                Medio día

                                            </span>


                                        <?php
                                        }else{
                                        ?>


                                            <span
                                                class="badge-dia-completo">

                                                <i
                                                    class="bi bi-calendar-check-fill">
                                                </i>

                                                Día completo

                                            </span>


                                        <?php
                                        } ?>


                                    </td>


                                </tr>


                            <?php } ?>


                            </tbody>


                        </table>

                    </div>


                    <!--=================================
                    TOTAL DETALLE
                    ==================================-->

                    <div class="total-vacaciones">

                        <div class="total-vacaciones-box">

                            Total del detalle:

                            <span>

                                <?php
                                echo $totalDetalle;
                                ?>

                            </span>

                            <?php

                            echo
                            $totalDetalle == 1
                            ? " día"
                            : " días";

                            ?>

                        </div>

                    </div>


                <?php }else{ ?>


                    <div class="alert alert-warning">

                        <i class="bi bi-exclamation-triangle-fill me-2"></i>

                        Este registro no tiene detalle de fechas
                        almacenado.

                    </div>


                <?php } ?>


            </div>


            <!--=========================================
            VALIDACIONES
            =========================================-->

            <hr class="my-4">


            <h4 class="fw-bold text-success mb-3">

                <i class="bi bi-shield-check me-2"></i>

                Validaciones

            </h4>


            <div class="row">


                <!--=================================
                VALIDACION JEFE
                ==================================-->

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">

                        Jefe de área

                    </label>

                    <div class="info-vacaciones">

                        <?php
                        echo htmlspecialchars(
                            $vacaciones["validacionJefe"]
                        );
                        ?>

                    </div>

                </div>


                <!--=================================
                VALIDACION RH
                ==================================-->

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">

                        Recursos Humanos

                    </label>

                    <div class="info-vacaciones">

                        <?php
                        echo htmlspecialchars(
                            $vacaciones["validacionRh"]
                        );
                        ?>

                    </div>

                </div>


            </div>


        </div>

    </div>

</div>