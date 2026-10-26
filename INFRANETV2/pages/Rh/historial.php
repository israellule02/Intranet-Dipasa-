<?php

require_once("../config/conexion.php");

$buscar = $_GET["buscar"] ?? "";
$estatus = $_GET["estatus"] ?? "";
$departamento = $_GET["departamento"] ?? "";


/*=========================================================
ESTATUS
=========================================================*/

$sqlEstatus = $conexion->prepare("
    SELECT *
    FROM estatusvac
    ORDER BY nombre
");

$sqlEstatus->execute();

$estatuses = $sqlEstatus->fetchAll(PDO::FETCH_ASSOC);


/*=========================================================
DEPARTAMENTOS
=========================================================*/

$sqlDepartamentos = $conexion->prepare("
    SELECT
        idDepartamento,
        nombre
    FROM departamentos
    ORDER BY nombre
");

$sqlDepartamentos->execute();

$departamentos = $sqlDepartamentos->fetchAll(PDO::FETCH_ASSOC);


/*=========================================================
CONSULTAR REGISTROS DE VACACIONES
=========================================================*/

$sql = "

SELECT

    r.idRegistro,
    
    CONCAT(u.nombre,' ',u.apellidos) AS empleado,
     d.nombre AS departamento,

    p.descripcion AS periodo,

    r.fechainicio,

    r.fechafinal,

    r.diastomados,

    

   

    es.nombre AS estatusJefe

FROM registrovac r

LEFT JOIN usuarios u
    ON r.idUsuario = u.id

LEFT JOIN departamentos d
    ON u.idDep = d.idDepartamento

LEFT JOIN periodosvac p
    ON r.idPeriodo = p.idPeriodo

LEFT JOIN estatusvac es
    ON r.idValidacionJefe = es.id

WHERE 1=1

";


/*=========================================================
FILTRO USUARIO / EMPLEADO
=========================================================*/

if($buscar != ""){

    $sql .= "

        AND (
            u.nombre LIKE :buscar
            OR u.apellidos LIKE :buscar
            OR CONCAT(u.nombre,' ',u.apellidos) LIKE :buscar
        )

    ";

}


/*=========================================================
FILTRO DEPARTAMENTO
=========================================================*/

if($departamento != ""){

    $sql .= "

        AND u.idDep = :departamento

    ";

}


/*=========================================================
FILTRO ESTATUS
=========================================================*/

if($estatus != ""){

    $sql .= "

        AND r.idValidacionJefe = :estatus

    ";

}


/*=========================================================
ORDEN
=========================================================*/

$sql .= "

ORDER BY r.idRegistro DESC

";


/*=========================================================
PREPARAR CONSULTA
=========================================================*/

$stmt = $conexion->prepare($sql);


/*=========================================================
PARAMETRO BUSCAR
=========================================================*/

if($buscar != ""){

    $stmt->bindValue(
        ":buscar",
        "%".$buscar."%",
        PDO::PARAM_STR
    );

}


/*=========================================================
PARAMETRO DEPARTAMENTO
=========================================================*/

if($departamento != ""){

    $stmt->bindValue(
        ":departamento",
        $departamento,
        PDO::PARAM_INT
    );

}


/*=========================================================
PARAMETRO ESTATUS
=========================================================*/

if($estatus != ""){

    $stmt->bindValue(
        ":estatus",
        $estatus,
        PDO::PARAM_INT
    );

}


/*=========================================================
EJECUTAR
=========================================================*/

$stmt->execute();

$resgistros = $stmt->fetchAll(PDO::FETCH_ASSOC);


$_SESSION["datosPDF"] = $resgistros;

?>


<div class="container-fluid">

   <!--=====================================================
ENCABEZADO
=====================================================-->

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2 class="fw-bold mb-0">

        <i class="bi bi-luggage-fill text-success"></i>

        Consultar registro de vacaciones

    </h2>


    <div class="d-flex gap-2">

        <!-- PDF -->


        <a
    href="../controllers/PdfController.php"
    target="_blank"
    class="btn btn-primary mr-3"
>
    <i class="bi bi-file-earmark-pdf-fill"></i>
   
</a>


        <!-- CONSULTAR DÍAS -->

        <a
            href="dashboard.php?page=ConsultarDias"
            class="btn btn-success">

            <i class="bi bi-folder2-open"></i>

            Consultar días disponibles

        </a>

    </div>

</div>


    <!--=====================================================
FILTROS
=====================================================-->

<div class="card shadow-sm border-0 rounded-4 mb-4">

    <div class="card-body p-4">

        <form
            method="GET"
            action="dashboard.php">

            <input
                type="hidden"
                name="page"
                value="historialVacaciones"
            >


            <div class="row g-3 align-items-end">


                <!--=================================================
                EMPLEADO
                =================================================-->

                <div class="col-lg-4 col-md-6">

                    <label
                        class="form-label fw-semibold mb-2">

                        <i class="bi bi-person-fill text-success"></i>

                        Empleado

                    </label>

                    <div class="input-group">

                        <span class="input-group-text bg-light">

                            <i class="bi bi-search"></i>

                        </span>

                        <input
                            type="text"
                            name="buscar"
                            class="form-control"
                            placeholder="Buscar empleado..."
                            value="<?php echo htmlspecialchars($buscar); ?>"
                        >

                    </div>

                </div>


                <!--=================================================
                DEPARTAMENTO
                =================================================-->

                <div class="col-lg-3 col-md-6">

                    <label
                        class="form-label fw-semibold mb-2">

                        <i class="bi bi-building text-success"></i>

                        Departamento

                    </label>

                    <select
                        name="departamento"
                        class="form-select">

                        <option value="">

                            Todos los departamentos

                        </option>


                        <?php foreach($departamentos as $dep){ ?>

                            <option
                                value="<?php echo $dep["idDepartamento"]; ?>"
                                <?php

                                if(
                                    $departamento ==
                                    $dep["idDepartamento"]
                                ){

                                    echo "selected";

                                }

                                ?>
                            >

                                <?php

                                echo htmlspecialchars(
                                    $dep["nombre"]
                                );

                                ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!--=================================================
                ESTATUS
                =================================================-->

                <div class="col-lg-2 col-md-6">

                    <label
                        class="form-label fw-semibold mb-2">

                        <i class="bi bi-check-circle text-success"></i>

                        Estatus

                    </label>

                    <select
                        name="estatus"
                        class="form-select">

                        <option value="">

                            Todos

                        </option>


                        <?php foreach($estatuses as $es){ ?>

                            <option
                                value="<?php echo $es["id"]; ?>"
                                <?php

                                if(
                                    $estatus ==
                                    $es["id"]
                                ){

                                    echo "selected";

                                }

                                ?>
                            >

                                <?php

                                echo htmlspecialchars(
                                    $es["nombre"]
                                );

                                ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!--=================================================
                FILTRAR
                =================================================-->

                <div class="col-lg-1 col-md-3 d-grid">

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-funnel-fill"></i>

                        Filtrar

                    </button>

                </div>


                <!--=================================================
                LIMPIAR
                =================================================-->

                <div class="col-lg-2 col-md-3 d-grid">

                    <a
                        href="dashboard.php?page=historialVacaciones"
                        class="btn btn-outline-secondary">

                        <i class="bi bi-x-circle"></i>

                        Limpiar

                    </a>

                </div>


            </div>

        </form>

    </div>

</div>


    <!--=====================================================
    MENSAJES
    =====================================================-->

    <?php if(isset($_GET["editado"])){ ?>

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill"></i>

            El registro fue actualizado correctamente.

            <button
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    <?php } ?>


    <?php if(isset($_GET["eliminado"])){ ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-trash-fill"></i>

            El registro fue eliminado correctamente.

            <button
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    <?php } ?>


    <?php if(isset($_GET["error"])){ ?>

        <div class="alert alert-warning alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill"></i>

            Ocurrió un error al realizar la operación.

            <button
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    <?php } ?>


    <!--=====================================================
    TABLA
    =====================================================-->

    <div class="card shadow border-0 rounded-4">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-success">

                        <tr>

                            <th>ID</th>

                            <th>Empleado</th>

                            <th>Departamento</th>

                            <th>Fecha de inicio</th>

                            <th>Fecha de término</th>

                            <th>Periodo</th>

                            <th>Días tomados</th>

                            <th width="180">Acciones</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach($resgistros as $resgistro){ ?>

                        <tr>


                            <!-- ID -->

                            <td>

                                <?php
                                echo $resgistro["idRegistro"];
                                ?>

                            </td>


                            <!-- EMPLEADO -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $resgistro["empleado"] ?? ""
                                );

                                ?>

                            </td>


                            <!-- DEPARTAMENTO -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $resgistro["departamento"] ?? ""
                                );

                                ?>

                            </td>


                            <!-- FECHA INICIO -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $resgistro["fechainicio"] ?? ""
                                );

                                ?>

                            </td>


                            <!-- FECHA TERMINO -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $resgistro["fechafinal"] ?? ""
                                );

                                ?>

                            </td>


                            <!-- PERIODO -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $resgistro["periodo"] ?? ""
                                );

                                ?>

                            </td>


                            <!-- DIAS TOMADOS -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $resgistro["diastomados"] ?? ""
                                );

                                ?>

                            </td>


                            <!-- ACCIONES -->

                            <td>

                                <a
                                    href="dashboard.php?page=detalleVacaciones&id=<?php echo $resgistro["idRegistro"]; ?>"
                                    class="btn btn-info btn-sm"
                                    title="Ver">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                            </td>


                        </tr>

                    <?php } ?>


                    <!--=================================================
                    SIN RESULTADOS
                    ==================================================-->

                    <?php if(count($resgistros) == 0){ ?>

                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-5">

                                <i class="bi bi-inbox fs-1"></i>

                                <br><br>

                                No existen vacaciones registradas
                                con los filtros seleccionados.

                            </td>

                        </tr>

                    <?php } ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>