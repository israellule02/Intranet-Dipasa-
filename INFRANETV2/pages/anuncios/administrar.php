<?php

require_once("../config/conexion.php");

$buscar = $_GET['buscar'] ?? '';

$sql = "
SELECT
    a.*,
    CONCAT(u.nombre,' ',u.apellidos) AS autor
FROM avisos a
INNER JOIN usuarios u
ON a.idUsuario = u.id
";

if($buscar != ""){
    $sql .= " WHERE a.titulo LIKE :buscar";
}

$sql .= " ORDER BY a.fecha DESC, a.hora DESC";

$stmt = $conexion->prepare($sql);

if($buscar != ""){
    $stmt->bindValue(":buscar","%".$buscar."%");
}

$stmt->execute();

$avisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">

            <i class="bi bi-gear-fill text-success"></i>

            Administrar anuncios

        </h2>

        <a
        href="dashboard.php?page=crearAnuncio"
        class="btn btn-success">

            <i class="bi bi-plus-circle-fill"></i>

            Nuevo anuncio

        </a>

    </div>

    <!-- ===========================
            MENSAJES
    ============================ -->

    <?php if(isset($_GET["ok"])){ ?>

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle-fill"></i>

            El anuncio fue creado correctamente.

            <button class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    <?php } ?>

    <?php if(isset($_GET["editado"])){ ?>

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-pencil-square"></i>

            El anuncio fue actualizado correctamente.

            <button class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    <?php } ?>

    <?php if(isset($_GET["eliminado"])){ ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-trash-fill"></i>

            El anuncio fue eliminado correctamente.

            <button class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    <?php } ?>

    <?php if(isset($_GET["error"])){ ?>

        <div class="alert alert-warning alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill"></i>

            Ocurrió un error.

            <button class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    <?php } ?>

    <!-- ===========================
            BUSCADOR
    ============================ -->

    <div class="card shadow-sm border-0 rounded-4 mb-4">

        <div class="card-body">

            <form method="GET" action="dashboard.php">

                <input
                type="hidden"
                name="page"
                value="administrarAnuncios">

                <div class="row">

                    <div class="col-md-10">

                        <input
                        type="text"
                        class="form-control"
                        name="buscar"
                        placeholder="Buscar por título..."
                        value="<?php echo htmlspecialchars($buscar); ?>">

                    </div>

                    <div class="col-md-2 d-grid">

                        <button class="btn btn-success">

                            <i class="bi bi-search"></i>

                            Buscar

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <!-- ===========================
            TABLA
    ============================ -->

    <div class="card shadow border-0 rounded-4">

        <div class="card-header bg-success text-white">

            <strong>

                Total de anuncios:

                <?php echo count($avisos); ?>

            </strong>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-success">

                        <tr>

    <th>ID</th>

    <th>Imagen</th>

    <th>Título</th>

    <th>Autor</th>

    <th>Fecha</th>

    <th>Hora</th>

    <th class="text-center">Acciones</th>

</tr>

                    </thead>

                    <tbody>

                    <?php

                    if(count($avisos)>0){

                        foreach($avisos as $aviso){

                            if($aviso["imagen"]!=""){

                                $imagen="../assets/img/anuncios/".$aviso["imagen"];

                            }else{

                                $imagen="../assets/img/no-imagen.png";

                            }

                    ?>

                        <tr>

                            <td>

                                <?php echo $aviso["idAvisos"]; ?>

                            </td>

                            <td>

                                <img
                                src="<?php echo $imagen; ?>"
                                width="80"
                                height="60"
                                style="object-fit:cover;"
                                class="rounded shadow">

                            </td>

                            <td>

                                <?php echo htmlspecialchars($aviso["titulo"]); ?>

                            </td>

                            <td>

    <span class="badge bg-success">

        <i class="bi bi-person-fill"></i>

        <?php echo htmlspecialchars($aviso["autor"]); ?>

    </span>

</td>

                            <td>

                                <?php echo date("d/m/Y",strtotime($aviso["fecha"])); ?>

                            </td>

                            <td>

                                <?php echo substr($aviso["hora"],0,5); ?>

                            </td>

                            

                            <td class="text-center">

                            <a
href="dashboard.php?page=detalleAnuncio&id=<?php echo $aviso['idAvisos']; ?>"
class="btn btn-info btn-sm text-white"
title="Ver">

    <i class="bi bi-eye-fill"></i>

</a>

                                <a
                                href="dashboard.php?page=editarAnuncio&id=<?php echo $aviso['idAvisos']; ?>"
                                class="btn btn-warning btn-sm"
                                title="Editar">

                                    <i class="bi bi-pencil-fill"></i>

                                </a>

                                <a
                                href="../controllers/EliminarAnuncio.php?id=<?php echo $aviso['idAvisos']; ?>"
                                class="btn btn-danger btn-sm"
                                title="Eliminar"
                                onclick="return confirm('¿Está seguro de eliminar este anuncio?');">

                                    <i class="bi bi-trash-fill"></i>

                                </a>

                            </td>

                        </tr>

                    <?php

                        }

                    }else{

                    ?>

                        <tr>

                            <td colspan="7" class="text-center py-5">

                                <i class="bi bi-megaphone fs-1 text-secondary"></i>

                                <br><br>

                                No hay anuncios registrados.

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>