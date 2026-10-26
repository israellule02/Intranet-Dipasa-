<?php

require_once("../config/conexion.php");

if(!isset($_GET["id"])){
    header("Location: dashboard.php?page=usuarios");
    exit();
}

$id = $_GET["id"];

/*==============================
    USUARIO
==============================*/

$sql = $conexion->prepare("
SELECT *
FROM usuarios
WHERE id=:id
");

$sql->bindParam(":id",$id);
$sql->execute();

$usuario = $sql->fetch(PDO::FETCH_ASSOC);

if(!$usuario){
    echo "<div class='alert alert-danger'>El usuario no existe.</div>";
    exit();
}

/*==============================
    ROLES
==============================*/

$roles = $conexion->query("
SELECT *
FROM roles
ORDER BY rol
")->fetchAll(PDO::FETCH_ASSOC);

/*==============================
    DEPARTAMENTOS
==============================*/

$departamentos = $conexion->query("
SELECT *
FROM departamentos
ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

/*==============================
    PUESTOS
==============================*/

$puestos = $conexion->query("
SELECT *
FROM puesto
ORDER BY descripcion
")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card shadow-lg border-0 rounded-4">

<div class="card-header bg-warning">

<h3 class="mb-0">

<i class="bi bi-pencil-square"></i>

Editar usuario

</h3>

</div>

<div class="card-body p-4">

<form
action="../controllers/EditarUsuario.php"
method="POST">

<input
type="hidden"
name="id"
value="<?php echo $usuario["id"]; ?>">

<div class="row">

<!--==========================
NOMBRE
===========================-->

<div class="col-md-6 mb-3">

<label class="form-label">

Nombre

</label>

<input
type="text"
name="nombre"
class="form-control"
required
value="<?php echo htmlspecialchars($usuario["nombre"]); ?>">

</div>

<!--==========================
APELLIDOS
===========================-->

<div class="col-md-6 mb-3">

<label class="form-label">

Apellidos

</label>

<input
type="text"
name="apellidos"
class="form-control"
required
value="<?php echo htmlspecialchars($usuario["apellidos"]); ?>">

</div>

<!--==========================
USUARIO
===========================-->

<div class="col-md-6 mb-3">

<label class="form-label">

Nombre de usuario

</label>

<input
type="text"
name="nomusuario"
class="form-control"
required
value="<?php echo htmlspecialchars($usuario["nomusuario"]); ?>">

</div>

<!--==========================
CONTRASEÑA
===========================-->

<div class="col-md-6 mb-3">

<label class="form-label">

Contraseña

</label>

<div class="input-group">

<input
type="password"
name="contrasena"
id="password"
class="form-control"
required
value="<?php echo htmlspecialchars($usuario["contrasena"]); ?>">

<button
type="button"
class="btn btn-outline-secondary"
onclick="mostrarPassword()">

<i
id="iconoPassword"
class="bi bi-eye-fill"></i>

</button>

</div>

</div>

<!--==========================
NÓMINA
===========================-->

<div class="col-md-6 mb-3">

<label class="form-label">

Número de nómina

</label>

<input
type="number"
name="numNomina"
class="form-control"
required
value="<?php echo $usuario["numNomina"]; ?>">

</div>


<!--==========================
correo
===========================-->
<div class="col-md-6 mb-3">

<label class="form-label">

Correo electronico 
</label>

<input
type="text"
name="correo"
class="form-control"

value="<?php echo htmlspecialchars($usuario["correo"]); ?>">

</div>



<!--==========================
ROL
===========================-->

<div class="col-md-6 mb-3">

<label class="form-label">

Rol

</label>

<select
name="idRol"
class="form-select"
required>

<?php foreach($roles as $rol){ ?>

<option
value="<?php echo $rol["id"]; ?>"
<?php if($usuario["idRol"]==$rol["id"]) echo "selected"; ?>>

<?php echo $rol["rol"]; ?>

</option>

<?php } ?>

</select>

</div>

<!--==========================
DEPARTAMENTO
===========================-->

<div class="col-md-6 mb-3">

<label class="form-label">

Departamento

</label>

<select
name="idDep"
class="form-select"
required>

<?php foreach($departamentos as $dep){ ?>

<option
value="<?php echo $dep["idDepartamento"]; ?>"
<?php if($usuario["idDep"]==$dep["idDepartamento"]) echo "selected"; ?>>

<?php echo $dep["nombre"]; ?>

</option>

<?php } ?>

</select>

</div>

<!--==========================
PUESTO
===========================-->

<div class="col-md-6 mb-3">

<label class="form-label">

Puesto

</label>

<select
name="idPuesto"
class="form-select"
required>

<?php foreach($puestos as $puesto){ ?>

<option
value="<?php echo $puesto["idPuesto"]; ?>"
<?php if($usuario["idPuesto"]==$puesto["idPuesto"]) echo "selected"; ?>>

<?php echo $puesto["descripcion"]; ?>

</option>

<?php } ?>

</select>

</div>

<!--==========================
FECHA NACIMIENTO
===========================-->

<div class="col-md-6 mb-3">

<label class="form-label">

Fecha de nacimiento

</label>

<input
type="date"
name="fechaNacimiento"
class="form-control"
required
value="<?php echo $usuario["fechaNacimiento"]; ?>">

</div>

<!--==========================
FECHA INGRESO
===========================-->

<div class="col-md-6 mb-4">

<label class="form-label">

Fecha de ingreso

</label>

<input
type="date"
name="fechaIngreso"
class="form-control"
required
value="<?php echo $usuario["fechaIngreso"]; ?>">

</div>


</div>

<div class="text-end">

<a
href="dashboard.php?page=usuarios"
class="btn btn-secondary">

<i class="bi bi-arrow-left-circle"></i>

Cancelar

</a>

<button
type="submit"
class="btn btn-warning">

<i class="bi bi-save-fill"></i>

Guardar cambios

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script>

function mostrarPassword(){

let input=document.getElementById("password");
let icono=document.getElementById("iconoPassword");

if(input.type==="password"){

input.type="text";

icono.classList.remove("bi-eye-fill");
icono.classList.add("bi-eye-slash-fill");

}else{

input.type="password";

icono.classList.remove("bi-eye-slash-fill");
icono.classList.add("bi-eye-fill");

}

}

</script>