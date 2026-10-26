<?php

require_once("../config/conexion.php");

/*=========================================
USUARIOS
=========================================*/

$sqlUsuarios = $conexion->prepare("
SELECT
    id,
    CONCAT(nombre,' ',apellidos) AS nombreCompleto
FROM usuarios
ORDER BY nombre ASC
");

$sqlUsuarios->execute();

$usuarios = $sqlUsuarios->fetchAll(PDO::FETCH_ASSOC);


/*=========================================
DISPOSITIVOS
=========================================*/

$sqlDispositivos = $conexion->prepare("

SELECT

d.id,
d.marca,
d.numDeSerie,
t.nombre AS tipo

FROM dispositivos d

INNER JOIN Pre_tipoDeDispositivo t
ON d.idTipoDis = t.id

ORDER BY d.id, d.numDeSerie ,t.nombre,d.marca

");

$sqlDispositivos->execute();

$dispositivos = $sqlDispositivos->fetchAll(PDO::FETCH_ASSOC);


/*=========================================
ESTADOS
=========================================*/

$sqlEstado = $conexion->prepare("

SELECT *

FROM pre_estado

ORDER BY nombre

");

$sqlEstado->execute();

$estados = $sqlEstado->fetchAll(PDO::FETCH_ASSOC);


/*=========================================
ESTATUS
=========================================*/

$sqlEstatus = $conexion->prepare("

SELECT *

FROM pre_estatus

ORDER BY nombre

");

$sqlEstatus->execute();

$estatuses = $sqlEstatus->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-success text-white">

<h3 class="mb-0">

<i class="bi bi-pc-display"></i>

Registrar Prestamo

</h3>

</div>

<div class="card-body">

<form
action="../controllers/PrestamosController.php"
method="POST">

<div class="row">

<!-- EMPLEADO -->

<div class="col-md-6 mb-3">

<label class="form-label">

Empleado

</label>

<input
type="text"
id="buscarEmpleado"
class="form-control"
list="listaEmpleados"
placeholder="Buscar empleado..."
autocomplete="off"
required>

<datalist id="listaEmpleados">

<?php foreach($usuarios as $usuario){ ?>

<option
value="<?php echo htmlspecialchars($usuario["nombreCompleto"]); ?>"
data-id="<?php echo $usuario["id"]; ?>">
</option>

<?php } ?>

</datalist>

<input
type="hidden"
name="idUsuario"
id="idUsuario"
required>

</div>

<!-- DISPOSITIVO -->

<div class="col-md-6 mb-3">

<label class="form-label">

Dispositivo

</label>

<input
type="text"
id="buscarDispositivo"
class="form-control"
list="listaDispositivos"
placeholder="Buscar por marca o número de serie..."
autocomplete="off"
required>

<datalist id="listaDispositivos">

<?php foreach($dispositivos as $dispositivo){ ?>

<option

value="<?php echo htmlspecialchars($dispositivo["id"]." | ".$dispositivo["tipo"]." | ".$dispositivo["marca"]." | ".$dispositivo["numDeSerie"]); ?>"

data-id="<?php echo $dispositivo["id"]; ?>">

</option>

<?php } ?>

</datalist>

<input
type="hidden"
name="idDispositivo"
id="idDispositivo"
required>

</div>



<!-- ESTADO DEL DISPOSITIVO -->

<div class="col-md-6 mb-3">

<label class="form-label">

Estado del dispositivo

</label>

<select
name="idEstado"
class="form-select"
required>

<option value="">

Seleccione...

</option>

<?php foreach($estados as $estado){ ?>

<option
value="<?php echo $estado["id"]; ?>">

<?php echo htmlspecialchars($estado["nombre"]); ?>

</option>

<?php } ?>

</select>

</div>


<!-- FECHA DE INICIO -->

<div class="col-md-6 mb-3">

<label class="form-label">

Fecha de inicio

</label>

<input
type="date"
name="fechainicio"
class="form-control"
value="<?php echo date("Y-m-d"); ?>"
required>

</div>


<div class="col-md-6 mb-3">

<label class="form-label">

Fecha de entrega

</label>

<input
type="date"
name="fechadeentregado"
class="form-control"
value="<?php echo date("Y-m-d"); ?>"
required>

</div>









<div class="col-12 text-end mt-4">

<a
href="dashboard.php?page=consultarPrestamo"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Cancelar

</a>

<button
type="submit"
class="btn btn-success">

<i class="bi bi-save-fill"></i>

Guardar asignación

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script>

/*=========================================
BUSCAR EMPLEADO
=========================================*/

const inputEmpleado = document.getElementById("buscarEmpleado");
const idUsuario = document.getElementById("idUsuario");

inputEmpleado.addEventListener("input", function(){

    idUsuario.value = "";

    document.querySelectorAll("#listaEmpleados option").forEach(function(opcion){

        if(opcion.value === inputEmpleado.value){

            idUsuario.value = opcion.dataset.id;

        }

    });

});


/*=========================================
BUSCAR DISPOSITIVO
=========================================*/

const inputDispositivo = document.getElementById("buscarDispositivo");
const idDispositivo = document.getElementById("idDispositivo");

inputDispositivo.addEventListener("input", function(){

    idDispositivo.value = "";

    document.querySelectorAll("#listaDispositivos option").forEach(function(opcion){

        if(opcion.value === inputDispositivo.value){

            idDispositivo.value = opcion.dataset.id;

        }

    });

});


/*=========================================
VALIDAR FORMULARIO
=========================================*/

document.querySelector("form").addEventListener("submit", function(e){

    if(idUsuario.value == ""){

        alert("Seleccione un empleado válido de la lista.");

        inputEmpleado.focus();

        e.preventDefault();

        return;

    }

    if(idDispositivo.value == ""){

        alert("Seleccione un dispositivo válido de la lista.");

        inputDispositivo.focus();

        e.preventDefault();

        return;

    }

});

</script>