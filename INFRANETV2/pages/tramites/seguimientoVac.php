<?php

require_once("../config/conexion.php");

$buscar = $_GET["buscar"] ?? "";
$tipo = $_GET["tipo"] ?? "";
$estatus = $_GET["estatus"] ?? "";

/*==========================
Estatus
==========================*/

$sqlEstatus = $conexion->prepare("
SELECT *
FROM estatusvac
ORDER BY nombre
");


$idUsuario = $_SESSION["idUsuario"];


$sqlEstatus->execute();
$estatuses = $sqlEstatus->fetchAll(PDO::FETCH_ASSOC);

/*==========================
CONSULTAR ASIGNACIONES
==========================*/

$sql = "

SELECT

r.idRegistro,
r.idjefe,
r.idValidacionRh,
r.idValidacionJefe,
P.descripcion as periodo,
r.fechainicio,

r.fechafinal,
u.idDep ,

r.diastomados,
d.nombre as departamento ,
r.idUsuario,

CONCAT(u.nombre,' ',u.apellidos) AS empleado,


validacionJefe.nombre AS validacionJefe,
validacionRh.nombre AS validacionRh

FROM registrovac r

INNER JOIN usuarios u
ON r.idUsuario = u.id
INNER JOIN departamentos d
ON u.idDep = d.idDepartamento

LEFT JOIN estatusvac validacionRh
on r.idValidacionRh = validacionRh.id

LEFT JOIN  periodosvac p
on r.idPeriodo = p.idPeriodo


LEFT JOIN estatusvac validacionJefe
on r.idValidacionJefe = validacionJefe.id


WHERE r.idUsuario = ?

";






/*==========================
FILTRO ESTATUS
==========================*/

if($estatus != ""){

    $sql .= "

    AND r.idValidacionJefe = :estatus

    ";

}

$sql .= "

ORDER BY r.idRegistro DESC

";

$stmt = $conexion->prepare($sql);

/*==========================
PARAMETROS
==========================*/

if($buscar != ""){

    $stmt->bindValue(
        ":buscar",
        "%".$buscar."%"
    );

}


if($estatus != ""){

    $stmt->bindValue(
        ":estatus",
        $estatus,
        PDO::PARAM_INT
    );

}


$stmt->execute([$idUsuario]);

$resgistros = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">

            <i class="bi bi-luggage-fill text-success "></i>

            Seguimiento de solicitudes de vacaciones 
        </h2>

    

    </div>

    <?php if(isset($_GET["aceptado"])){ ?>

    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle-fill"></i>

        La solicitud de vacaciones fue aceptada correctamente.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php } ?>


<?php if(isset($_GET["rechazado"])){ ?>

    <div class="alert alert-danger alert-dismissible fade show">

        <i class="bi bi-ban-fill"></i>

        La solicitud de vacaciones fue rechazada correctamente.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php } ?>

    

    <?php if(isset($_GET["editado"])){ ?>

<div class="alert alert-success alert-dismissible fade show">

    <i class="bi bi-check-circle-fill"></i>

    El registro fue actualizada correctamente.

    <button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<?php if(isset($_GET["eliminado"])){ ?>

<div class="alert alert-danger alert-dismissible fade show">

    <i class="bi bi-trash-fill"></i>

    El registro fue eliminada correctamente.

    <button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<?php if(isset($_GET["error"])){ ?>

<div class="alert alert-warning alert-dismissible fade show">

    <i class="bi bi-exclamation-triangle-fill"></i>

    Ocurrió un error al realizar la operación.

    <button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<div class="card shadow border-0 rounded-4">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-success">

                    <tr>

                        <th>ID</th>

                        <th>Empleado</th>
                        <th>Periodo</th>
                       
                        <th>Fecha de inicio</th>
                        <th>Fecha de termino</th>
                          <th>Validacion Jefe de area</th>
                           <th>Validacion Recursos humanos</th>


    
                        <th>Dias tomados</th>

                        

                     

                    </tr>

                </thead>

                <tbody>

                <?php foreach($resgistros as $resgistro){ ?>

                    <tr>

                        <td>

                            <?php echo  $resgistro["idRegistro"]; ?>

                        </td>

                        <td>

                            <?php echo htmlspecialchars($resgistro["empleado"]); ?>

                        </td>
                        <td>

                            <?php echo htmlspecialchars( $resgistro["periodo"]); ?>

                        </td>
                      

                        <td>

                            <?php echo htmlspecialchars( $resgistro["fechainicio"]); ?>

                        </td>

                        <td>

                            <?php echo htmlspecialchars( $resgistro["fechafinal"]); ?>

                        </td>


                         <td>

                            <?php echo htmlspecialchars( $resgistro["validacionJefe"]); ?>

                        </td>

                        <td>

                            <?php echo htmlspecialchars( $resgistro["validacionRh"]); ?>

                        </td>


                        <td>

                            <?php echo htmlspecialchars( $resgistro["diastomados"]); ?>

                        </td>

                      <td>

    


  

</td>


                     


                    </tr>

                <?php } ?>

                <?php if(count($resgistros)==0){ ?>

                    <tr>

                        <td colspan="10" class="text-center py-5">

                            <i class="bi bi-inbox fs-1"></i>

                            <br><br>

                            No existen solicitudes pendientes de vacaciones por el momento.

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
