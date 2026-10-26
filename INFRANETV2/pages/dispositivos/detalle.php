<?php

require_once("../config/conexion.php");

if(!isset($_GET["id"])){

    echo "<div class='alert alert-danger'>Dispositivo no encontrado.</div>";
    exit();

}

$id = $_GET["id"];

/*=========================================
OBTENER DISPOSITIVO
=========================================*/

$sql = $conexion->prepare("

SELECT

d.*,

t.nombre AS tipo,

e.nombre AS estatus

FROM dispositivos d

INNER JOIN pre_tipodedispositivo t
ON d.idTipoDis=t.id

INNER JOIN estatusdispositivos e
ON d.idestatusdis=e.id

WHERE d.id=:id

");

$sql->bindParam(":id",$id,PDO::PARAM_INT);

$sql->execute();

$dispositivo = $sql->fetch(PDO::FETCH_ASSOC);

if(!$dispositivo){

    echo "<div class='alert alert-danger'>El dispositivo no existe.</div>";
    exit();

}

?>

<div class="container-fluid">

<a
href="dashboard.php?page=consultarDispositivo"
class="btn btn-outline-success mb-4">

<i class="bi bi-arrow-left"></i>

Regresar

</a>

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-success text-white">

<h3>

<i class="bi bi-pc-display"></i>

Detalle del dispositivo

</h3>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<label class="fw-bold">

Marca

</label>

<p>

<?php echo htmlspecialchars($dispositivo["marca"]); ?>

</p>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">

Número de serie

</label>

<p>

<?php echo htmlspecialchars($dispositivo["numdeserie"]); ?>

</p>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">

Tipo de dispositivo

</label>

<p>

<?php echo htmlspecialchars($dispositivo["tipo"]); ?>

</p>

</div>


<div class="col-12">

<label class="fw-bold">

Fecha de compra

</label>

<div class="border rounded p-3 bg-light">

<?php echo nl2br(htmlspecialchars($dispositivo["fechadecompra"])); ?>

</div>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">

Estatus

</label>

<p>

<span class="badge bg-success">

<?php echo htmlspecialchars($dispositivo["estatus"]); ?>

</span>

</p>

</div>

<div class="col-12">

<label class="fw-bold">

Características

</label>

<div class="border rounded p-3 bg-light">

<?php echo nl2br(htmlspecialchars($dispositivo["caracteristicas"])); ?>

</div>

</div>

</div>

<hr>

<div class="text-end">

<a
href="dashboard.php?page=editarDispositivo&id=<?php echo $dispositivo["id"]; ?>"
class="btn btn-warning">

<i class="bi bi-pencil-fill"></i>

Editar

</a>

<a
href="dashboard.php?page=consultarDispositivo"
class="btn btn-secondary">

<i class="bi bi-arrow-left-circle"></i>

Regresar

</a>

</div>

</div>

</div>

</div>