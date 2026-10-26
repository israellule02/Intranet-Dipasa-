<?php

require_once("../config/conexion.php");

/*=========================================
TIPOS DE DISPOSITIVO
=========================================*/

$sqlTipos = $conexion->prepare("
SELECT *
FROM pre_tipodedispositivo
ORDER BY nombre ASC
");

$sqlTipos->execute();

$tipos = $sqlTipos->fetchAll(PDO::FETCH_ASSOC);

/*=========================================
ESTATUS DEL DISPOSITIVO
=========================================*/

$sqlEstatus = $conexion->prepare("
SELECT *
FROM estatusdispositivos
ORDER BY nombre ASC
");

$sqlEstatus->execute();

$estatus = $sqlEstatus->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

<div class="row justify-content-center">

<div class="col-lg-9">

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-success text-white">

<h3 class="mb-0">

<i class="bi bi-pc-display-horizontal"></i>

Registrar dispositivo

</h3>

</div>

<div class="card-body p-4">

<form
action="../controllers/DispositivosController.php"
method="POST">

<div class="row">

<!-- Marca -->

<div class="col-md-6 mb-3">

<label class="form-label">

Marca

</label>

<input
type="text"
name="marca"
class="form-control"
required>

</div>

<!-- Número de Serie -->

<div class="col-md-6 mb-3">

<label class="form-label">

Número de serie

</label>

<input
type="text"
name="numdeserie"
class="form-control"
required>

</div>


<!-- FECHA DE INICIO -->

<div class="col-md-6 mb-3">

<label class="form-label">

Fecha de compra

</label>

<input
type="date"
name="fechadecompra"
class="form-control"
value="<?php echo date("Y-m-d"); ?>"
required>

</div>

<!-- Tipo -->

<div class="col-md-6 mb-3">

<label class="form-label">

Tipo de dispositivo

</label>

<select
name="idTipoDis"
class="form-select"
required>

<option value="">

Seleccione...

</option>

<?php foreach($tipos as $tipo){ ?>

<option value="<?php echo $tipo["id"]; ?>">

<?php echo htmlspecialchars($tipo["nombre"]); ?>

</option>

<?php } ?>

</select>

</div>

<!-- Estatus -->

<div class="col-md-6 mb-3">

<label class="form-label">

Estatus

</label>

<select
name="idestatusdis"
class="form-select"
required>

<option value="">

Seleccione...

</option>

<?php foreach($estatus as $item){ ?>

<option value="<?php echo $item["id"]; ?>">

<?php echo htmlspecialchars($item["nombre"]); ?>

</option>

<?php } ?>

</select>

</div>

<!-- Características -->

<div class="col-12 mb-4">

<label class="form-label">

Características

</label>

<textarea
name="caracteristicas"
rows="5"
class="form-control"
placeholder="Procesador, RAM, SSD, accesorios, modelo, observaciones..."
required></textarea>

</div>

<div class="col-12 text-end">

<a
href="dashboard.php?page=consultarDispositivo"
class="btn btn-secondary">

<i class="bi bi-x-circle"></i>

Cancelar

</a>

<button
type="submit"
class="btn btn-success">

<i class="bi bi-save-fill"></i>

Guardar dispositivo

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>