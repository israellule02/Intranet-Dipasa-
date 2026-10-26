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

SELECT *

FROM dispositivos

WHERE id=:id

");

$sql->bindParam(":id",$id,PDO::PARAM_INT);

$sql->execute();

$dispositivo = $sql->fetch(PDO::FETCH_ASSOC);

if(!$dispositivo){

    echo "<div class='alert alert-danger'>El dispositivo no existe.</div>";
    exit();

}

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

?>

<div class="container-fluid">

<div class="row justify-content-center">

<div class="col-lg-9">

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-warning">

<h3 class="mb-0">

<i class="bi bi-pencil-fill"></i>

Editar dispositivo

</h3>

</div>

<div class="card-body">

<form
action="../controllers/EditarDispositivo.php"
method="POST">

<input
type="hidden"
name="id"
value="<?php echo $dispositivo["id"]; ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Marca

</label>

<input
type="text"
name="marca"
class="form-control"
value="<?php echo htmlspecialchars($dispositivo["marca"]); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Número de serie

</label>

<input
type="text"
name="numdeserie"
class="form-control"
value="<?php echo htmlspecialchars($dispositivo["numdeserie"]); ?>"
required>

</div>


<div class="col-md-6 mb-3">

<label class="form-label">

Fecha de compra

</label>

<input
type="date"
name="fechadecompra"
class="form-control"
value="<?php

                                        echo htmlspecialchars(
                                            $dispositivo[
                                                "fechadecompra"
                                            ] ?? "",
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );

                                    ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Tipo

</label>

<select
name="idTipoDis"
class="form-select"
required>

<?php foreach($tipos as $tipo){ ?>

<option
value="<?php echo $tipo["id"]; ?>"
<?php if($tipo["id"]==$dispositivo["idTipoDis"]) echo "selected"; ?>>

<?php echo htmlspecialchars($tipo["nombre"]); ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Estatus

</label>

<select
name="idestatusdis"
class="form-select"
required>

<?php foreach($estatuses as $estatus){ ?>

<option
value="<?php echo $estatus["id"]; ?>"
<?php if($estatus["id"]==$dispositivo["idestatusdis"]) echo "selected"; ?>>

<?php echo htmlspecialchars($estatus["nombre"]); ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-12 mb-4">

<label class="form-label">

Características

</label>

<textarea
name="caracteristicas"
rows="6"
class="form-control"
required><?php echo htmlspecialchars($dispositivo["caracteristicas"]); ?></textarea>

</div>

<div class="col-12 text-end">

<a
href="dashboard.php?page=consultarDispositivo"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Cancelar

</a>

<button
type="submit"
class="btn btn-warning">

<i class="bi bi-save-fill"></i>

Actualizar dispositivo

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>