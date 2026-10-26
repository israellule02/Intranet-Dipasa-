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
    u.*,
    r.rol,
    d.nombre AS departamento,
    p.descripcion AS puesto
FROM usuarios u
INNER JOIN roles r
ON u.idRol = r.id
INNER JOIN departamentos d
ON u.idDep = d.idDepartamento
INNER JOIN puesto p
ON u.idPuesto = p.idPuesto
WHERE u.id=:id




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
href="dashboard.php?page=usuarios"
class="btn btn-outline-success mb-4">

<i class="bi bi-arrow-left"></i>

Regresar

</a>

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-success text-white">

<h3>

<i class="bi bi-pc-display"></i>

Informacion del usuario

</h3>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<label class="fw-bold ">
<i class="bi bi-person-check-fill"></i>
Nombre

</label>

<p>

<?php echo htmlspecialchars($dispositivo["nombre"]); ?>

</p>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">

Apellidos
<i class="bi bi-person-check-fill"></i>
</label>

<p>

<?php echo htmlspecialchars($dispositivo["apellidos"]); ?>

</p>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">
<i class="bi bi-person-square"></i>
Usuario

</label>

<p>

<?php echo htmlspecialchars($dispositivo["nomusuario"]); ?>

</p>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
<i class="bi bi-cake"></i>

Fecha de Nacimiento

</label>

<p>

<span class="badge bg-success">

<?php echo htmlspecialchars($dispositivo["fechaNacimiento"]); ?>

</span>

</p>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">
<i class="bi bi-door-closed"></i>
Fecha de ingreso

</label>

<p>

<span class="badge bg-success">

<?php echo htmlspecialchars($dispositivo["fechaIngreso"]); ?>

</span>

</p>

</div>


<div class="col-12 mb-3">

<label class="fw-bold">
<i class="bi bi-cash-coin"></i>
Numero de nomina 

</label>

<div class="badge bg-success">

<?php echo nl2br(htmlspecialchars($dispositivo["numNomina"])); ?>

</div>

</div>
<div class="col-md-6 mb-3">

<label class="fw-bold">
<i class="bi bi-buildings"></i>
Rol

</label>

<p>



<?php echo htmlspecialchars($dispositivo["rol"]); ?>



</p>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
<i class="bi bi-building-check"></i>
Departamento

</label>

<p>



<?php echo htmlspecialchars($dispositivo["departamento"]); ?>



</p>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">
<i class="bi bi-briefcase"></i>
Puesto

</label>

<p>



<?php echo htmlspecialchars($dispositivo["puesto"]); ?>



</p>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">
<i class="bi bi-envelope-check"></i>
Correo electronico


</label>

<p>



<?php 

if ($dispositivo["correo"] == null){
    echo htmlspecialchars("-");

}else{
    echo htmlspecialchars($dispositivo["correo"]);

}
 ?>



</p>

</div>


<div class="col-md-6 mb-3">

<label class="fw-bold">
<i class="bi bi-suitcase2"></i>
Dias de vacaciones disponibles 


</label>

<p>



<?php 
if ($dispositivo["diasVacaciones"] == null){

echo htmlspecialchars("-");
}else{
    echo htmlspecialchars($dispositivo["diasVacaciones"]);

}

 ?>



</p>

</div>


</div>

<hr>

<div class="text-end">

<a
href="dashboard.php?page=editarUsuario&id=<?php echo $dispositivo["id"]; ?>"
class="btn btn-warning">

<i class="bi bi-pencil-fill"></i>

Editar

</a>

<a
href="dashboard.php?page=usuarios"
class="btn btn-secondary">

<i class="bi bi-arrow-left-circle"></i>

Regresar

</a>

</div>

</div>

</div>

</div>