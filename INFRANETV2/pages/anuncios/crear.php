<div class="container-fluid">

<div class="card shadow-lg border-0 rounded-4">

<div class="card-header bg-success text-white">

<h3>

<i class="bi bi-megaphone-fill"></i>

Crear anuncio

</h3>

</div>

<div class="card-body">

<form action="../controllers/AnunciosController.php"
      method="POST"
      enctype="multipart/form-data">

<div class="row">

<div class="col-md-12 mb-4">

<label class="form-label">

Título

</label>

<input
type="text"
class="form-control form-control-lg"
name="titulo"
required>

</div>

<div class="col-md-12 mb-4">

<label class="form-label">

Contenido

</label>

<textarea
class="form-control"
rows="7"
name="contenido"
required></textarea>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">

Fecha

</label>

<input
type="date"
class="form-control"
name="fecha"
value="<?php echo date('Y-m-d');?>"
required>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">

Hora

</label>

<input
type="time"
class="form-control"
name="hora"
value="<?php echo date('H:i');?>"
required>

</div>

<div class="col-md-12">

<label class="form-label">

Imagen

</label>

<div class="upload-area">

<i class="bi bi-image-fill"></i>

<p>

Seleccione una imagen

</p>

<input
type="file"
name="imagen"
id="imagen"
accept="image/*">

</div>

</div>

<div class="col-md-12 mt-4 text-center">

<img
id="preview"
src=""
style="display:none;max-height:250px;border-radius:15px;">

</div>

<div class="col-md-12 mt-4 text-end">

<button
class="btn btn-success btn-lg">

<i class="bi bi-check-circle-fill"></i>

Guardar anuncio

</button>

</div>

</div>

</form>

</div>

</div>

</div>

<script>

const imagen=document.getElementById("imagen");

const preview=document.getElementById("preview");

imagen.addEventListener("change",function(e){

const archivo=e.target.files[0];

if(archivo){

preview.src=URL.createObjectURL(archivo);

preview.style.display="block";

}

});

</script>