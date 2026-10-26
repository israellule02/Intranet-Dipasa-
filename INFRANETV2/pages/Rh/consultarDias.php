<?php

require_once("../config/conexion.php");


/*=========================================
FILTROS
=========================================*/

$buscar = trim($_GET["buscar"] ?? "");

$departamento = $_GET["departamento"] ?? "";


/*=========================================
OBTENER DEPARTAMENTOS
=========================================*/

$sqlDepartamentos = $conexion->prepare("
    SELECT
        idDepartamento,
        nombre
    FROM departamentos
    ORDER BY nombre ASC
");

$sqlDepartamentos->execute();

$departamentos = $sqlDepartamentos->fetchAll(PDO::FETCH_ASSOC);


/*=========================================
CONSULTAR DIAS DISPONIBLES
=========================================*/

$sql = "
    SELECT

        dp.id,    

        CONCAT(u.nombre, ' ', u.apellidos) AS empleado,

        d.nombre AS departamento,

        p.descripcion AS periodo,

        p.fechaInicio,

        p.fechaFinal,
         dp.diasDisponibles

    FROM diasperiodos dp

    INNER JOIN usuarios u
        ON dp.idUsuario = u.id

    LEFT JOIN departamentos d
        ON u.idDep = d.idDepartamento

    LEFT JOIN periodosvac p
        ON dp.idPeriodo = p.idPeriodo

    WHERE 1=1
";


/*=========================================
BUSCAR POR EMPLEADO
=========================================*/

if($buscar != ""){

    $sql .= "

        AND (
            u.nombre LIKE :buscar
            OR u.apellidos LIKE :buscar
            OR CONCAT(u.nombre, ' ', u.apellidos) LIKE :buscar
        )

    ";

}


/*=========================================
FILTRAR POR DEPARTAMENTO
=========================================*/

if($departamento != ""){

    $sql .= "

        AND u.idDep = :departamento

    ";

}


/*=========================================
ORDEN
=========================================*/

$sql .= "

    ORDER BY
        u.nombre ASC,
        u.apellidos ASC,
        p.fechaInicio ASC

";


/*=========================================
PREPARAR CONSULTA
=========================================*/

$stmt = $conexion->prepare($sql);


/*=========================================
PARAMETRO BUSQUEDA
=========================================*/

if($buscar != ""){

    $stmt->bindValue(
        ":buscar",
        "%".$buscar."%",
        PDO::PARAM_STR
    );

}


/*=========================================
PARAMETRO DEPARTAMENTO
=========================================*/

if($departamento != ""){

    $stmt->bindValue(
        ":departamento",
        $departamento,
        PDO::PARAM_INT
    );

}


/*=========================================
EJECUTAR
=========================================*/

$stmt->execute();

$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
$_SESSION["datosPDF"] = $registros;
?>


<div class="container-fluid">


    <!--=========================================
    TITULO
    =========================================-->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">

            <i class="bi bi-calendar-check text-success"></i>

            Días de vacaciones disponibles

        </h2>

    </div>



    <!--=========================================
    FILTROS
    =========================================-->

    <div class="card shadow border-0 rounded-4 mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="dashboard.php"
            >

                <input
                    type="hidden"
                    name="page"
                    value="ConsultarDias"
                >


                <div class="row g-3 align-items-end">


    <!--=================================
    BUSCAR EMPLEADO
    =================================-->

    <div class="col-md-4">

        <label class="form-label fw-semibold">

            <i class="bi bi-search"></i>

            Buscar empleado

        </label>

        <input
            type="text"
            name="buscar"
            class="form-control"
            placeholder="Nombre o apellido..."
            value="<?php echo htmlspecialchars($buscar); ?>"
        >

    </div>



    <!--=================================
    DEPARTAMENTO
    =================================-->

    <div class="col-md-3">

        <label class="form-label fw-semibold">

            <i class="bi bi-building"></i>

            Departamento

        </label>

        <select
            name="departamento"
            class="form-select"
        >

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



    <!--=================================
    FILTRAR
    =================================-->

    <div class="col-md-2 d-grid">

        <button
            type="submit"
            class="btn btn-success"
        >

            <i class="bi bi-funnel-fill"></i>

            Filtrar

        </button>

    </div>



    <!--=================================
    LIMPIAR
    =================================-->

    <div class="col-md-2 d-grid">

        <a
            href="dashboard.php?page=ConsultarDias"
            class="btn btn-outline-secondary"
        >

            <i class="bi bi-x-circle"></i>

            Limpiar

        </a>

    </div>



    <!--=================================
    GENERAR INFORME
    =================================-->

<a
    href="../controllers/PdfController.php"
    target="_blank"
    class="btn btn-primary"
>
    <i class="bi bi-file-earmark-pdf-fill"></i>

</a>


</div>

            </form>

        </div>

    </div>



    <!--=========================================
    RESULTADOS
    =========================================-->

    <div class="card shadow border-0 rounded-4">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-success">

                        <tr>

                            <th>ID</th>

                            <th>Empleado</th>

                            <th>Departamento</th>

                            <th>Periodo</th>

                            <th>Fecha inicio</th>

                            <th>Fecha final</th>

                            <th>Días disponibles</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach($registros as $registro){ ?>

                        <tr>


                            <!-- ID -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $registro["id"]
                                );

                                ?>

                            </td>



                            <!-- EMPLEADO -->

                            <td>

                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $registro["empleado"]
                                    );

                                    ?>

                                </strong>

                            </td>



                            <!-- DEPARTAMENTO -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $registro["departamento"]
                                    ?? "Sin departamento"
                                );

                                ?>

                            </td>



                            <!-- PERIODO -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $registro["periodo"]
                                    ?? "Sin periodo"
                                );

                                ?>

                            </td>



                            <!-- FECHA INICIO -->

                            <td>

                                <?php

                                if(
                                    !empty(
                                        $registro["fechaInicio"]
                                    )
                                ){

                                    echo date(
                                        "d/m/Y",
                                        strtotime(
                                            $registro["fechaInicio"]
                                        )
                                    );

                                }else{

                                    echo "-";

                                }

                                ?>

                            </td>



                            <!-- FECHA FINAL -->

                            <td>

                                <?php

                                if(
                                    !empty(
                                        $registro["fechaFinal"]
                                    )
                                ){

                                    echo date(
                                        "d/m/Y",
                                        strtotime(
                                            $registro["fechaFinal"]
                                        )
                                    );

                                }else{

                                    echo "-";

                                }

                                ?>

                            </td>



                            <!-- DIAS DISPONIBLES -->

                            <td>

                                <span
                                    class="badge bg-success fs-6"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $registro["diasDisponibles"]
                                    );

                                    ?>

                                    días

                                </span>

                            </td>


                        </tr>

                    <?php } ?>



                    <!--=================================
                    SIN RESULTADOS
                    =================================-->

                    <?php if(count($registros) == 0){ ?>

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5"
                            >

                                <i
                                    class="bi bi-inbox fs-1 text-secondary"
                                ></i>

                                <br><br>


                                <?php

                                if(
                                    $buscar != "" ||
                                    $departamento != ""
                                ){

                                ?>

                                    No se encontraron registros
                                    con los filtros seleccionados.

                                <?php

                                }else{

                                ?>

                                    No existen días disponibles
                                    registrados.

                                <?php } ?>


                            </td>

                        </tr>

                    <?php } ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>