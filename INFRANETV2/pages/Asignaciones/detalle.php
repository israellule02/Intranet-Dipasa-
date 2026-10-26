<?php

require_once("../config/conexion.php");

if(!isset($_GET["id"])){

    header("Location: dashboard.php?page=consultarAsignaciones");
    exit();

}

$id = $_GET["id"];

$sql = $conexion->prepare("

SELECT

a.*,

CONCAT(u.nombre,' ',u.apellidos) AS empleado,
d.id AS idDispositivo,

d.marca,

d.caracteristicas,

d.numDeSerie,

t.nombre AS tipo,

e.nombre AS estado,

pe.nombre AS estatus

FROM asignaciones a

INNER JOIN usuarios u
ON a.idUsuario = u.id

INNER JOIN dispositivos d
ON a.idDispositivo = d.id

INNER JOIN pre_tipodedispositivo t
ON d.idTipoDis = t.id

INNER JOIN pre_estado e
ON a.idEstado = e.id

INNER JOIN pre_estatus pe
ON a.idpreestatus = pe.id

WHERE a.id = :id

");

$sql->bindParam(":id",$id,PDO::PARAM_INT);

$sql->execute();

$asignacion = $sql->fetch(PDO::FETCH_ASSOC);

if(!$asignacion){

    echo "<div class='alert alert-danger'>
            La asignación no existe.
          </div>";

    exit();

}

?>


<div class="container-fluid">

<a
href="dashboard.php?page=consultarAsignaciones"
class="btn btn-outline-success mb-4">

<i class="bi bi-arrow-left"></i>

Regresar

</a>

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-success text-white">

<h3>

<i class="bi bi-pc-display"></i>

Detalle de la asignación

</h3>

</div>

<div class="card-body">

<div class="row">

<!-- EMPLEADO -->

<div class="col-md-6 mb-3">

<label class="fw-bold">

Empleado

</label>

<p>

<?php echo htmlspecialchars($asignacion["empleado"]); ?>

</p>

</div>

<!-- TIPO -->

<div class="col-md-6 mb-3">

<label class="fw-bold">

Tipo de dispositivo

</label>

<p>

<?php echo htmlspecialchars($asignacion["tipo"]); ?>

</p>

</div>



<div class="col-md-6 mb-3">

<label class="fw-bold">

ID del dispositivo

</label>

<p>

<?php echo htmlspecialchars($asignacion["idDispositivo"]); ?>

</p>

</div>

<!-- MARCA -->

<div class="col-md-6 mb-3">

<label class="fw-bold">

Marca

</label>

<p>

<?php echo htmlspecialchars($asignacion["marca"]); ?>

</p>

</div>

<!-- SERIE -->

<div class="col-md-6 mb-3">

<label class="fw-bold">

Número de serie

</label>

<p>

<?php echo htmlspecialchars($asignacion["numDeSerie"]); ?>

</p>

</div>

<!-- FECHA INICIO -->

<div class="col-md-6 mb-3">

<label class="fw-bold">

Fecha de inicio

</label>

<p>

<?php

echo !empty($asignacion["fechainicio"])
? date("d/m/Y",strtotime($asignacion["fechainicio"]))
: "-";

?>

</p>

</div>

<!-- FECHA ENTREGA -->

<div class="col-md-6 mb-3">

<label class="fw-bold">

Fecha de entrega

</label>

<p>

<?php

if(
    empty($asignacion["fechadeentregado"]) ||
    $asignacion["fechadeentregado"]=="0000-00-00"
){

    echo "Pendiente";

}else{

    echo date("d/m/Y",strtotime($asignacion["fechadeentregado"]));

}

?>

</p>

</div>

<!-- ESTADO -->

<div class="col-md-6 mb-3">

<label class="fw-bold">

Estado físico

</label>

<p>

<span class="badge bg-primary">

<?php echo htmlspecialchars($asignacion["estado"]); ?>

</span>

</p>

</div>

<!-- ESTATUS -->

<div class="col-md-6 mb-3">

<label class="fw-bold">

Estatus

</label>

<p>

<?php

$color = ($asignacion["idpreestatus"]==1)
? "success"
: "secondary";

?>

<span class="badge bg-<?php echo $color; ?>">

<?php echo htmlspecialchars($asignacion["estatus"]); ?>

</span>

</p>

</div>

<!-- CARACTERÍSTICAS -->

<div class="col-12">

<label class="fw-bold">

Características del dispositivo

</label>

<div class="border rounded p-3 bg-light">

<?php echo nl2br(htmlspecialchars($asignacion["caracteristicas"])); ?>

</div>

</div>

</div>

<hr>


<hr>

<div class="text-end">

<div class="text-end">

<?php if($asignacion["idpreestatus"] == 1){ ?>

<a
href="../controllers/EntregarAsignacionController.php?id=<?php echo $asignacion["id"]; ?>"
class="btn btn-success"
onclick="return confirm('¿Desea marcar este dispositivo como entregado?');">

<i class="bi bi-check-circle-fill"></i>

Entregar dispositivo

</a>

<?php } ?>

<a
href="../controllers/GenerarCartaAsignacion.php?id=<?php echo $asignacion["id"]; ?>"
class="btn btn-danger"
target="_blank">

<i class="bi bi-file-earmark-pdf-fill"></i>

Generar Carta Responsiva

</a>

</div>

<a
href="dashboard.php?page=editarAsignacion&id=<?php echo $asignacion["id"]; ?>"
class="btn btn-warning">

<i class="bi bi-pencil-fill"></i>

Editar

</a>

</div>

</div>

</div>

</div>