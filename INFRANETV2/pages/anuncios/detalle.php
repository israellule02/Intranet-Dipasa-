<?php

require_once("../config/conexion.php");

$id = $_GET['id'] ?? 0;

/*=========================================
        OBTENER ANUNCIO
=========================================*/

$sql = $conexion->prepare("
SELECT
    a.*,
    CONCAT(u.nombre,' ',u.apellidos) AS autor
FROM avisos a
INNER JOIN usuarios u
    ON a.idUsuario = u.id
WHERE a.idAvisos = :id
");

$sql->bindParam(":id",$id,PDO::PARAM_INT);

$sql->execute();

$aviso = $sql->fetch(PDO::FETCH_ASSOC);

if(!$aviso){

    echo '
    <div class="alert alert-danger m-4">
        El anuncio no existe.
    </div>
    ';

    exit();

}

?>

<div class="container-fluid">

    <a href="dashboard.php?page=verAnuncios"
    class="btn btn-outline-success mb-4">

        <i class="bi bi-arrow-left"></i>

        Regresar

    </a>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

        <?php if(!empty($aviso["imagen"])){ ?>

            <img
            src="../assets/img/anuncios/<?php echo $aviso["imagen"]; ?>"
            style="width:100%;height:420px;object-fit:cover;">

        <?php } ?>

        <div class="card-body p-5">

            <h2 class="fw-bold mb-3">

                <?php echo htmlspecialchars($aviso["titulo"]); ?>

            </h2>

            <div class="row mb-4">

                <div class="col-md-4">

                    <p class="text-muted mb-2">

                        <i class="bi bi-calendar-event-fill text-success"></i>

                        <strong>Fecha:</strong>

                        <?php echo date("d/m/Y",strtotime($aviso["fecha"])); ?>

                    </p>

                </div>

                <div class="col-md-4">

                    <p class="text-muted mb-2">

                        <i class="bi bi-clock-fill text-success"></i>

                        <strong>Hora:</strong>

                        <?php echo substr($aviso["hora"],0,5); ?>

                    </p>

                </div>

                <div class="col-md-4">

                    <p class="text-muted mb-2">

                        <i class="bi bi-person-circle text-success"></i>

                        <strong>Publicado por:</strong>

                        <?php echo htmlspecialchars($aviso["autor"]); ?>

                    </p>

                </div>

            </div>

            <hr>

            <div
            class="mt-4"
            style="font-size:18px;line-height:1.9;text-align:justify;">

                <?php echo nl2br(htmlspecialchars($aviso["contenido"])); ?>

            </div>

        </div>

    </div>

</div>