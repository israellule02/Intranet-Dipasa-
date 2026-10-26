<?php

require_once("../config/conexion.php");

if(!isset($_GET["id"])){

    echo "<div class='alert alert-danger'>No se especificó el anuncio.</div>";
    return;

}

$id = $_GET["id"];

$sql = $conexion->prepare("
SELECT *
FROM avisos
WHERE idAvisos = :id
");

$sql->bindParam(":id",$id,PDO::PARAM_INT);

$sql->execute();

$aviso = $sql->fetch(PDO::FETCH_ASSOC);

if(!$aviso){

    echo "<div class='alert alert-danger'>El anuncio no existe.</div>";
    return;

}

?>

<div class="container-fluid">

    <div class="card shadow-lg border-0 rounded-4">

        <div class="card-header bg-warning">

            <h3 class="mb-0">

                <i class="bi bi-pencil-square"></i>

                Editar anuncio

            </h3>

        </div>

        <div class="card-body">

            <form
            action="../controllers/EditarAnuncioController.php"
            method="POST"
            enctype="multipart/form-data">

                <input
                type="hidden"
                name="idAvisos"
                value="<?php echo $aviso['idAvisos']; ?>">

                <input
                type="hidden"
                name="imagenActual"
                value="<?php echo $aviso['imagen']; ?>">

                <div class="row">

                    <!-- TITULO -->

                    <div class="col-md-12 mb-4">

                        <label class="form-label">

                            Título

                        </label>

                        <input
                        type="text"
                        class="form-control form-control-lg"
                        name="titulo"
                        value="<?php echo htmlspecialchars($aviso['titulo']); ?>"
                        required>

                    </div>

                    <!-- CONTENIDO -->

                    <div class="col-md-12 mb-4">

                        <label class="form-label">

                            Contenido

                        </label>

                        <textarea
                        class="form-control"
                        rows="7"
                        name="contenido"
                        required><?php echo htmlspecialchars($aviso['contenido']); ?></textarea>

                    </div>

                    <!-- FECHA -->

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Fecha

                        </label>

                        <input
                        type="date"
                        class="form-control"
                        name="fecha"
                        value="<?php echo $aviso['fecha']; ?>"
                        required>

                    </div>

                    <!-- HORA -->

                    <div class="col-md-6 mb-4">

                        <label class="form-label">

                            Hora

                        </label>

                        <input
                        type="time"
                        class="form-control"
                        name="hora"
                        value="<?php echo substr($aviso['hora'],0,5); ?>"
                        required>

                    </div>

                    <!-- IMAGEN -->

                    <div class="col-md-6">

                        <label class="form-label">

                            Imagen actual

                        </label>

                        <br>

                        <?php

                        if($aviso["imagen"]!=""){

                        ?>

                        <img
                        src="../assets/img/anuncios/<?php echo $aviso["imagen"]; ?>"
                        id="preview"
                        class="img-fluid rounded shadow"
                        style="max-height:260px;">

                        <?php

                        }else{

                        ?>

                        <img
                        src="../assets/img/no-imagen.png"
                        id="preview"
                        class="img-fluid rounded shadow"
                        style="max-height:260px;">

                        <?php } ?>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">

                            Cambiar imagen

                        </label>

                        <input
                        type="file"
                        class="form-control"
                        id="imagen"
                        name="imagen"
                        accept="image/*">

                        <small class="text-muted">

                            Si no seleccionas una imagen se conservará la actual.

                        </small>

                    </div>

                    <!-- BOTONES -->

                    <div class="col-md-12 mt-5 text-end">

                        <a
                        href="dashboard.php?page=administrarAnuncios"
                        class="btn btn-secondary btn-lg">

                            <i class="bi bi-arrow-left"></i>

                            Cancelar

                        </a>

                        <button
                        class="btn btn-warning btn-lg">

                            <i class="bi bi-floppy-fill"></i>

                            Guardar cambios

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

const imagen = document.getElementById("imagen");
const preview = document.getElementById("preview");

imagen.addEventListener("change",function(){

    const archivo=this.files[0];

    if(archivo){

        preview.src=URL.createObjectURL(archivo);

    }

});

</script>