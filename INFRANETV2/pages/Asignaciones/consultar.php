<?php

require_once("../config/conexion.php");

$buscar = $_GET["buscar"] ?? "";
$tipo = $_GET["tipo"] ?? "";
$estatus = $_GET["estatus"] ?? "";

/*==========================
TIPOS DE DISPOSITIVO
==========================*/

$sqlTipos = $conexion->prepare("
SELECT *
FROM pre_tipodedispositivo
ORDER BY nombre
");

$sqlTipos->execute();
$tipos = $sqlTipos->fetchAll(PDO::FETCH_ASSOC);

/*==========================
ESTATUS
==========================*/

$sqlEstatus = $conexion->prepare("
SELECT *
FROM pre_estatus
ORDER BY nombre
");

$sqlEstatus->execute();
$estatuses = $sqlEstatus->fetchAll(PDO::FETCH_ASSOC);



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


/*==========================
CONSULTAR ASIGNACIONES
==========================*/

$sql = "

SELECT

a.id,


CONCAT(u.nombre,' ',u.apellidos) AS empleado,
de.nombre as departamento,

d.id AS idDispositivo,

d.marca,

d.caracteristicas,

d.numDeSerie,

t.nombre AS tipo,


a.fechainicio,

a.fechadeentregado,

es.nombre AS estatus

FROM asignaciones a

INNER JOIN usuarios u
ON a.idUsuario = u.id

INNER JOIN departamentos de
ON u.idDep = de.idDepartamento

INNER JOIN dispositivos d
ON a.idDispositivo = d.id

INNER JOIN pre_tipodedispositivo t
ON d.idTipoDis = t.id

INNER JOIN pre_estatus es
ON a.idPreEstatus = es.id



WHERE 1=1

";

/*==========================
BUSCADOR
==========================*/

if($buscar != ""){

    $sql .= "

    AND (

        d.id LIKE :buscar

        OR d.marca LIKE :buscar

        OR d.caracteristicas LIKE :buscar

        OR d.numDeSerie LIKE :buscar

        OR u.nombre LIKE :buscar

        OR u.apellidos LIKE :buscar

    )

    ";

}

/*==========================
FILTRO TIPO
==========================*/

if($tipo != ""){

    $sql .= "

    AND d.idTipoDis = :tipo

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


/*==========================
FILTRO ESTATUS
==========================*/

if($estatus != ""){

    $sql .= "

    AND a.idPreEstatus = :estatus

    ";

}

$sql .= "

ORDER BY u.idDep

";

$stmt = $conexion->prepare($sql);

/*==========================
PARAMETROS
==========================*/

if($buscar != ""){

    $stmt->bindValue(
        ":buscar",
        "%".$buscar."%"
    );

}

if($tipo != ""){

    $stmt->bindValue(
        ":tipo",
        $tipo,
        PDO::PARAM_INT
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

if($estatus != ""){

    $stmt->bindValue(
        ":estatus",
        $estatus,
        PDO::PARAM_INT
    );

}

$stmt->execute();

$asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);


$_SESSION["datosPDF"] = $asignaciones;
?>



<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">

            <i class="bi bi-pc-display text-success"></i>

            Consultar asignaciones

        </h2>

<div>
        
        <!-- PDF -->

<a
    href="../controllers/PdfController.php"
    target="_blank"
    class="btn btn-primary"
>
    <i class="bi bi-file-earmark-pdf-fill"></i>
   
</a>

        <a
            href="dashboard.php?page=registrarAsignaciones"
            class="btn btn-success ml-1">

            <i class="bi bi-plus-circle-fill"></i>

            Nueva asignación

        </a>
</div>
    </div>

    <div class="card shadow border-0 rounded-4 mb-4  ">

        <div class="card-body">

            <form method="GET" action="dashboard.php">
                

                <input
                    type="hidden"
                    name="page"
                    value="consultarAsignaciones">

                <div class="row g-3">

                    <div class="col-md-4">

                        <input
                            type="text"
                            name="buscar"
                            class="form-control"
                            placeholder="Id dis ,empleado, marca, serie o características..."
                            value="<?php echo htmlspecialchars($buscar); ?>">

                    </div>

                    <div class="col-md-2  " >

                        <select
                            name="tipo"
                            class="form-select">

                            <option value="">

                                Todos los tipos

                            </option>

                            <?php foreach($tipos as $t){ ?>

                                <option
                                    value="<?php echo $t["id"]; ?>"
                                    <?php if($tipo==$t["id"]) echo "selected"; ?>>

                                    <?php echo htmlspecialchars($t["nombre"]); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <select
                            name="estatus"
                            class="form-select">

                            <option value="">

                                Todos los estatus

                            </option>

                            <?php foreach($estatuses as $es){ ?>

                                <option
                                    value="<?php echo $es["id"]; ?>"
                                    <?php if($estatus==$es["id"]) echo "selected"; ?>>

                                    <?php echo htmlspecialchars($es["nombre"]); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>



                    
    <!--=================================
    DEPARTAMENTO
    =================================-->

    <div class="col-ml-1  " >

    

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


    <div class="">
                    <div class="col-md-2 d-grid mt-2 ml-1">

                        <button class="btn btn-success">

                            <i class="bi bi-funnel-fill"></i>

                            Filtrar

                        </button>

                    </div>



                    
    <!--=================================
    LIMPIAR
    =================================-->

    <div class="col-md-2 mt-2 d-grid">

        <a
            href="dashboard.php?page=consultarAsignaciones"
            class="btn btn-outline-secondary"
        >

            <i class="bi bi-x-circle"></i>

            Limpiar

        </a>

    </div>
</div>

                </div>

            </form>

        </div>

    </div>

    <?php if(isset($_GET["editado"])){ ?>

<div class="alert alert-success alert-dismissible fade show">

    <i class="bi bi-check-circle-fill"></i>

    La asignación fue actualizada correctamente.

    <button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<?php if(isset($_GET["eliminado"])){ ?>

<div class="alert alert-danger alert-dismissible fade show">

    <i class="bi bi-trash-fill"></i>

    La asignación fue eliminada correctamente.

    <button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<?php if(isset($_GET["error"])){ ?>

<div class="alert alert-warning alert-dismissible fade show">

    <i class="bi bi-exclamation-triangle-fill"></i>

    Ocurrió un error al realizar la operación.

    <button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>



        <div class="card-header bg-success text-white">

            <strong>

                Total de registros:
                <?php echo count($asignaciones); ?>

            </strong>

        </div>

<div class="card shadow border-0 rounded-4">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-success">

                    <tr>

                        <th>ID</th>

                        <th>Empleado</th>

                        <th>Tipo</th>
                        <th>ID. dispositivo</th>

                        <th>Marca</th>

                      

                        <th>No. Serie</th>

                        <th>Fecha inicio</th>

                        <th>Fecha entrega</th>

                        <th>Estatus</th>

                        <th width="180">Acciones</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach($asignaciones as $asignacion){ ?>

                    <tr>

                        <td>

                            <?php echo $asignacion["id"]; ?>

                        </td>

                        <td>

                            <?php echo htmlspecialchars($asignacion["empleado"]); ?>

                        </td>

                        <td>

                            <?php echo htmlspecialchars($asignacion["tipo"]); ?>

                        </td>

                           <td>

                            <?php echo htmlspecialchars($asignacion["idDispositivo"]); ?>

                        </td>

                        <td>

                            <?php echo htmlspecialchars($asignacion["marca"]); ?>

                        </td>

                       

                        <td>

                            <?php echo htmlspecialchars($asignacion["numDeSerie"]); ?>

                        </td>

                       <td>

<?php

if(
    !empty($asignacion["fechainicio"]) &&
    $asignacion["fechainicio"]!="0000-00-00"
){

    echo date("d/m/Y",strtotime($asignacion["fechainicio"]));

}else{

    echo "-";

}

?>

</td>

<td>

<?php

if(
    !empty($asignacion["fechadeentregado"]) &&
    $asignacion["fechadeentregado"]!="0000-00-00"
){

    echo date("d/m/Y",strtotime($asignacion["fechadeentregado"]));

}else{

    echo "-";

}

?>

</td>

                        <td>

                            <span class="badge bg-success">

                                <?php echo htmlspecialchars($asignacion["estatus"]); ?>

                            </span>

                        </td>

                        <td>

                            <a
                                href="dashboard.php?page=detalleAsignacion&id=<?php echo $asignacion["id"]; ?>"
                                class="btn btn-info btn-sm"
                                title="Ver">

                                <i class="bi bi-eye-fill"></i>

                            </a>

                            <a
                                href="dashboard.php?page=editarAsignacion&id=<?php echo $asignacion["id"]; ?>"
                                class="btn btn-warning btn-sm"
                                title="Editar">

                                <i class="bi bi-pencil-fill"></i>

                            </a>

                            <a
                                href="../controllers/EliminarAsignacion.php?id=<?php echo $asignacion["id"]; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Desea eliminar esta asignación?');"
                                title="Eliminar">

                                <i class="bi bi-trash-fill"></i>

                            </a>

                        </td>

                    </tr>

                <?php } ?>

                <?php if(count($asignaciones)==0){ ?>

                    <tr>

                        <td colspan="10" class="text-center py-5">

                            <i class="bi bi-inbox fs-1"></i>

                            <br><br>

                            No existen asignaciones registradas.

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
