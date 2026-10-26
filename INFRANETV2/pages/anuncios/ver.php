<?php

require_once("../config/conexion.php");

/*=========================================
        FILTROS
=========================================*/

$buscar = $_GET['buscar'] ?? '';
$desde  = $_GET['desde'] ?? '';
$hasta  = $_GET['hasta'] ?? '';
$orden  = $_GET['orden'] ?? 'DESC';

/*=========================================
        CONSULTA
=========================================*/

$sql = "
SELECT
    a.*,
    CONCAT(u.nombre,' ',u.apellidos) AS autor
FROM avisos a
INNER JOIN usuarios u
ON a.idUsuario = u.id
WHERE 1=1
";

if($buscar != ""){
    $sql .= " AND a.titulo LIKE :buscar";
}

if($desde != ""){
    $sql .= " AND a.fecha >= :desde";
}

if($hasta != ""){
    $sql .= " AND a.fecha <= :hasta";
}

$sql .= " ORDER BY a.fecha $orden, a.hora $orden";

$stmt = $conexion->prepare($sql);

if($buscar != ""){
    $stmt->bindValue(":buscar","%".$buscar."%");
}

if($desde != ""){
    $stmt->bindValue(":desde",$desde);
}

if($hasta != ""){
    $stmt->bindValue(":hasta",$hasta);
}

$stmt->execute();

$avisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">

            <i class="bi bi-megaphone-fill text-success"></i>

            Anuncios

        </h2>

       

    </div>

    <!--==========================
            FILTRO
    ===========================-->

    <div class="card shadow-sm border-0 rounded-4 mb-4">

        <div class="card-body">

            <form method="GET" action="dashboard.php">

                <input type="hidden" name="page" value="verAnuncios">

                <div class="row g-3">

                    <div class="col-lg-4">

                        <input
                        type="text"
                        class="form-control"
                        name="buscar"
                        placeholder="🔍 Buscar anuncio..."
                        value="<?php echo htmlspecialchars($buscar); ?>">

                    </div>

                    <div class="col-lg-2">

                        <input
                        type="date"
                        class="form-control"
                        name="desde"
                        value="<?php echo $desde; ?>">

                    </div>

                    <div class="col-lg-2">

                        <input
                        type="date"
                        class="form-control"
                        name="hasta"
                        value="<?php echo $hasta; ?>">

                    </div>

                    <div class="col-lg-2">

                        <select class="form-select" name="orden">

                            <option value="DESC" <?php if($orden=="DESC") echo "selected"; ?>>

                                Más recientes

                            </option>

                            <option value="ASC" <?php if($orden=="ASC") echo "selected"; ?>>

                                Más antiguos

                            </option>

                        </select>

                    </div>

                    <div class="col-lg-2 d-grid">

                        <button class="btn btn-success">

                            <i class="bi bi-funnel-fill"></i>

                            Filtrar

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <?php if(isset($_GET['ok'])){ ?>

        <div class="alert alert-success">

            <i class="bi bi-check-circle-fill"></i>

            El anuncio fue publicado correctamente.

        </div>

    <?php } ?>

    <div class="row">

        <?php

        if(count($avisos)>0){

            foreach($avisos as $aviso){

                if($aviso["imagen"]!=""){

                    $imagen="../assets/img/anuncios/".$aviso["imagen"];

                }else{

                    $imagen="../assets/img/no-imagen.png";

                }

        ?>

        <div class="col-lg-4 col-md-6 mb-4">

            <a
            href="dashboard.php?page=detalleAnuncio&id=<?php echo $aviso['idAvisos']; ?>"
            class="text-decoration-none text-dark">

                <div class="card shadow-lg border-0 rounded-4 h-100 anuncio-card">

                    <img
                    src="<?php echo $imagen; ?>"
                    class="card-img-top"
                    style="height:220px;object-fit:cover;">

                    <div class="card-body">

                        <h4 class="fw-bold">

                            <?php echo htmlspecialchars($aviso["titulo"]); ?>

                        </h4>

                        <div class="mb-2 text-muted">

                            <i class="bi bi-calendar3"></i>

                            <?php echo date("d/m/Y",strtotime($aviso["fecha"])); ?>

                            &nbsp;&nbsp;

                            <i class="bi bi-clock"></i>

                            <?php echo substr($aviso["hora"],0,5); ?>

                        </div>

                        <!-- AUTOR -->

                        <div class="mb-3">

                            <span class="badge bg-success">

                                <i class="bi bi-person-fill"></i>

                                <?php echo htmlspecialchars($aviso["autor"]); ?>

                            </span>

                        </div>

                        <p>

                            <?php

                            echo nl2br(htmlspecialchars(substr($aviso["contenido"],0,150)));

                            ?>

                            ...

                        </p>

                        <div class="text-end mt-3">

                            <span class="btn btn-outline-success btn-sm">

                                Ver más

                                <i class="bi bi-arrow-right-circle-fill"></i>

                            </span>

                        </div>

                    </div>

                </div>

            </a>

        </div>

        <?php

            }

        }else{

        ?>

        <div class="col-12">

            <div class="alert alert-info text-center p-5">

                <i class="bi bi-megaphone-fill fs-1"></i>

                <h4 class="mt-3">

                    No existen anuncios registrados.

                </h4>

            </div>

        </div>

        <?php } ?>

    </div>

</div>