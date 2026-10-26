<?php

require_once("../config/conexion.php");

$buscar = $_GET["buscar"] ?? "";
$tipo = $_GET["tipo"] ?? "";
$estatus = $_GET["estatus"] ?? "";

/*=========================================
TIPOS
=========================================*/

$sqlTipos = $conexion->prepare("
SELECT *
FROM pre_tipodedispositivo
ORDER BY nombre
");

$sqlTipos->execute();

$tipos = $sqlTipos->fetchAll(PDO::FETCH_ASSOC);

/*=========================================
ESTATUS
=========================================*/

$sqlEstatus = $conexion->prepare("
SELECT *
FROM estatusdispositivos
ORDER BY nombre
");

$sqlEstatus->execute();

$estatuses = $sqlEstatus->fetchAll(PDO::FETCH_ASSOC);

/*=========================================
CONSULTA
=========================================*/

$sql = "

SELECT

d.*,

t.nombre AS tipo,

e.nombre AS estatus

FROM dispositivos d

INNER JOIN pre_tipodedispositivo t
ON d.idTipoDis=t.id

INNER JOIN estatusdispositivos e
ON d.idestatusdis=e.id

WHERE 1=1

";

if($buscar!=""){

    $sql.="

    AND(

        d.id LIKE :buscar

        OR d.numdeserie LIKE :buscar

        OR d.marca LIKE :buscar

    )

    ";

}

if($tipo!=""){

    $sql.=" AND d.idTipoDis=:tipo";

}

if($estatus!=""){

    $sql.=" AND d.idestatusdis=:estatus";

}

$sql.=" ORDER BY d.id DESC";

$stmt=$conexion->prepare($sql);

if($buscar!=""){

    $stmt->bindValue(":buscar","%".$buscar."%");

}

if($tipo!=""){

    $stmt->bindValue(":tipo",$tipo);

}

if($estatus!=""){

    $stmt->bindValue(":estatus",$estatus);

}

$stmt->execute();

$dispositivos=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2 class="fw-bold">

<i class="bi bi-pc-display text-success"></i>

Control de dispositivos

</h2>

<a
href="dashboard.php?page=registrarDispositivo"
class="btn btn-success">

<i class="bi bi-plus-circle-fill"></i>

Nuevo dispositivo

</a>

</div>

<?php if(isset($_GET["guardado"])){ ?>

<div class="alert alert-success alert-dismissible fade show">

Dispositivo registrado correctamente.

<button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<?php if(isset($_GET["editado"])){ ?>

<div class="alert alert-success alert-dismissible fade show">

Dispositivo actualizado correctamente.

<button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<?php if(isset($_GET["eliminado"])){ ?>

<div class="alert alert-danger alert-dismissible fade show">

Dispositivo eliminado correctamente.

<button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<div class="card shadow border-0 rounded-4 mb-4">

<div class="card-body">

<form method="GET" action="dashboard.php">

<input
type="hidden"
name="page"
value="consultarDispositivo">

<div class="row g-3">

<div class="col-md-4">

<input
type="text"
name="buscar"
class="form-control"
placeholder="Marca, ID o número de serie"
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

<?php foreach($estatuses as $e){ ?>

<option
value="<?php echo $e["id"]; ?>"
<?php if($estatus==$e["id"]) echo "selected"; ?>>

<?php echo htmlspecialchars($e["nombre"]); ?>

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


        <div class="card-header bg-success text-white">

            <strong>

                Total de Dispositivos:
                <?php echo count($dispositivos); ?>

            </strong>

        </div>

<div class="card shadow border-0 rounded-4">

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-success">

<tr>

<th>ID</th>

<th>Marca</th>

<th>Serie</th>

<th>Tipo</th>

<th>Estatus</th>

<th width="200">

Acciones

</th>

</tr>

</thead>

<tbody>

<?php foreach($dispositivos as $dispositivo){ ?>

<tr>

<td>

<?php echo $dispositivo["id"]; ?>

</td>

<td>

<?php echo htmlspecialchars($dispositivo["marca"]); ?>

</td>

<td>

<?php echo htmlspecialchars($dispositivo["numdeserie"]); ?>

</td>

<td>

<?php echo htmlspecialchars($dispositivo["tipo"]); ?>

</td>

<td>

<span class="badge bg-success">

<?php echo htmlspecialchars($dispositivo["estatus"]); ?>

</span>

</td>

<td>

<a
href="dashboard.php?page=verDispositivo&id=<?php echo $dispositivo["id"]; ?>"
class="btn btn-info btn-sm">

<i class="bi bi-eye-fill"></i>

</a>

<a
href="dashboard.php?page=editarDispositivo&id=<?php echo $dispositivo["id"]; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-fill"></i>

</a>

<a
href="../controllers/EliminarDispositivo.php?id=<?php echo $dispositivo["id"]; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('¿Eliminar este dispositivo?');">

<i class="bi bi-trash-fill"></i>

</a>

</td>

</tr>

<?php } ?>

<?php if(count($dispositivos)==0){ ?>

<tr>

<td colspan="6" class="text-center py-5">

<i class="bi bi-inbox fs-1"></i>

<br><br>

No existen dispositivos registrados.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>