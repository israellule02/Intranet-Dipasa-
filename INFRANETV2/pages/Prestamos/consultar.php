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

/*==========================
CONSULTAR ASIGNACIONES
==========================*/

$sql = "

SELECT

a.id,

a.fechainicio,

a.fechadeentregado,

CONCAT(u.nombre,' ',u.apellidos) AS empleado,

d.id AS idDispositivo,

d.marca,

d.caracteristicas,

d.numDeSerie,

t.nombre AS tipo,

es.nombre AS estatus

FROM prestamos a

INNER JOIN usuarios u
ON a.idUsuario = u.id

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

/*==========================
FILTRO ESTATUS
==========================*/

if($estatus != ""){

    $sql .= "

    AND a.idPreEstatus = :estatus

    ";

}

$sql .= "

ORDER BY a.id DESC

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

if($estatus != ""){

    $stmt->bindValue(
        ":estatus",
        $estatus,
        PDO::PARAM_INT
    );

}

$stmt->execute();

$asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">

            <i class="bi bi-pc-display text-success"></i>

            Consultar Prestamos

        </h2>

        <a
            href="dashboard.php?page=registrarPrestamo"
            class="btn btn-success">

            <i class="bi bi-plus-circle-fill"></i>

            Nuevo Prestamo

        </a>

    </div>

    <div class="card shadow border-0 rounded-4 mb-4">

        <div class="card-body">

            <form method="GET" action="dashboard.php">

                <input
                    type="hidden"
                    name="page"
                    value="consultarPrestamo">

                <div class="row g-3">

                    <div class="col-md-4">

                        <input
                            type="text"
                            name="buscar"
                            class="form-control"
                            placeholder=" Id dis, empleado, marca, serie o características..."
                            value="<?php echo htmlspecialchars($buscar); ?>">

                    </div>

                    <div class="col-md-3">

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
                                    <?php if($estatus==$es["id"]) echo "selected"; ?>>

                                    <?php echo htmlspecialchars($es["nombre"]); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="col-md-2 d-grid">

                        <button class="btn btn-success">

                            <i class="bi bi-funnel-fill"></i>

                            Filtrar

                        </button>

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
                                href="dashboard.php?page=detallePrestamo&id=<?php echo $asignacion["id"]; ?>"
                                class="btn btn-info btn-sm"
                                title="Ver">

                                <i class="bi bi-eye-fill"></i>

                            </a>

                            <a
                                href="dashboard.php?page=editarPrestamo&id=<?php echo $asignacion["id"]; ?>"
                                class="btn btn-warning btn-sm"
                                title="Editar">

                                <i class="bi bi-pencil-fill"></i>

                            </a>

                            <a
                                href="../controllers/EliminarPrestamos.php?id=<?php echo $asignacion["id"]; ?>"
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

                            No existen prestamos registradas.

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
