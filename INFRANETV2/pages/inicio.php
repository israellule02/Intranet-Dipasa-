<?php

require_once("../config/conexion.php");

/*=========================================
    OBTENER DATOS DEL USUARIO
=========================================*/

$idUsuario = $_SESSION['idUsuario'];

$sqlUsuario = $conexion->prepare("
SELECT
    CONCAT(u.nombre,' ',u.apellidos) AS nombreCompleto,
    u.fechaIngreso,
    u.fechaNacimiento,
    d.nombre AS departamento,
    u.diasVacaciones
FROM usuarios u
INNER JOIN departamentos d
ON d.idDepartamento = u.idDep
WHERE u.id = :id
");

$sqlUsuario->bindParam(":id",$idUsuario,PDO::PARAM_INT);
$sqlUsuario->execute();

$usuario = $sqlUsuario->fetch(PDO::FETCH_ASSOC);


/*=========================================
    CALCULAR ANTIGÜEDAD
=========================================*/

$fechaIngreso = new DateTime($usuario['fechaIngreso']);
$fechaActual  = new DateTime();

$antiguedad = $fechaIngreso->diff($fechaActual);

$textoAntiguedad = "";

if($antiguedad->y > 0){

    $textoAntiguedad .= $antiguedad->y." año";

    if($antiguedad->y>1){
        $textoAntiguedad.="s";
    }

}

if($antiguedad->m>0){

    if($textoAntiguedad!=""){
        $textoAntiguedad.=" ";
    }

    $textoAntiguedad.=$antiguedad->m." mes";

    if($antiguedad->m>1){
        $textoAntiguedad.="es";
    }

}

if($textoAntiguedad==""){

    $textoAntiguedad="Menos de un mes";

}


/*=========================================
    AVISOS DE ESTA SEMANA
=========================================*/

$sqlAvisos = $conexion->prepare("
SELECT
    a.*,
    CONCAT(u.nombre,' ',u.apellidos) AS autor
FROM avisos a
INNER JOIN usuarios u
    ON a.idUsuario = u.id
WHERE YEARWEEK(a.fecha,1)=YEARWEEK(CURDATE(),1)
ORDER BY a.fecha DESC, a.hora DESC
");

$sqlAvisos->execute();

$avisos = $sqlAvisos->fetchAll(PDO::FETCH_ASSOC);


/*=========================================
    SALUDO
=========================================*/

date_default_timezone_set("America/Mexico_City");

$hora = date("H");

if($hora<12){

    $saludo="Buenos días";

}elseif($hora<19){

    $saludo="Buenas tardes";

}else{

    $saludo="Buenas noches";

}

?>

<div class="container-fluid">

    <div class="row mb-4">

        <div class="col-12">

            <h2 class="fw-bold">

                <i class="bi bi-house-door-fill text-success"></i>

                Inicio

            </h2>

            <p class="text-secondary fs-5">

                <?php echo $saludo; ?>,

                <strong>

                    <?php echo htmlspecialchars($usuario['nombreCompleto']); ?>

                </strong>

                

            </p>

        </div>

    </div>

    <div class="row g-4 align-items-stretch">

        <!--=====================================
                PERFIL
        ======================================-->

        <div class="col-lg-4">

            <div class="card border-0 shadow rounded-4 h-100 perfil-card">

                <div class="card-body">

                    <div class="text-center">

                        <div class="avatar mb-3">

                            <i class="bi bi-person-fill"></i>

                        </div>

                        <h4>

                            <?php echo htmlspecialchars($usuario['nombreCompleto']); ?>

                        </h4>

                        <span class="badge bg-success">

                            Empleado

                        </span>

                    </div>

                    <hr>

                    <div class="mt-4">

                        <p>

                            <i class="bi bi-building text-success"></i>

                            <strong> Departamento</strong>

                            <br>

                            <?php echo htmlspecialchars($usuario['departamento']); ?>

                        </p>

                        <p>

                            <i class="bi bi-calendar-check text-success"></i>

                            <strong> Fecha de ingreso</strong>

                            <br>

                            <?php echo date("d/m/Y",strtotime($usuario['fechaIngreso'])); ?>

                        </p>

                        <p>

                            <i class="bi bi-cake2-fill text-success"></i>

                            <strong> Fecha de nacimiento</strong>

                            <br>

                            <?php echo date("d/m/Y",strtotime($usuario['fechaNacimiento'])); ?>

                        </p>

                        <p class="mb-0">

                            <i class="bi bi-award-fill text-success"></i>

                            <strong> Antigüedad</strong>

                            <br>

                            <?php echo $textoAntiguedad; ?>

                        </p>



                          <p class="mb-0 mt-3">

                            <i class="bi bi-cloud-moon-fill  text-success"></i>

                            <strong> Dias de vacaciones disponibles 

                            </strong>

                            <br>

                            <?php echo htmlspecialchars($usuario['diasVacaciones']); ?>


                        </p>

                    </div>

                </div>

            </div>

        </div>

        <!--=====================================
                AVISOS
        ======================================-->

        <div class="col-lg-8">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-header bg-success text-white rounded-top-4">

                    <h5 class="mb-0">

                        <i class="bi bi-megaphone-fill"></i>

                        Últimos avisos

                    </h5>

                </div>

                <div class="card-body">

                    <?php if(count($avisos)>0){ ?>

                        <?php foreach($avisos as $aviso){ ?>

                           <a href="dashboard.php?page=detalleAnuncio&id=<?php echo $aviso['idAvisos']; ?>"
class="text-decoration-none text-dark">

<div class="aviso">
    

                                <h5>

                                    📌

                                    <?php echo htmlspecialchars($aviso['titulo']); ?>

                                </h5>

                                <small class="text-muted">

                                    <i class="bi bi-calendar3"></i>

                                    <?php echo date("d/m/Y",strtotime($aviso['fecha'])); ?>

                                    &nbsp;&nbsp;

                                    <i class="bi bi-clock"></i>

                                    <?php echo $aviso['hora']; ?>

                                </small>
                                <div class="mb-2">

    <span class="badge bg-success">

        <i class="bi bi-person-fill"></i>

        <?php echo htmlspecialchars($aviso["autor"]); ?>

    </span>

</div>

                                <p class="mt-2 mb-0">

                                    <?php

                                    echo nl2br(htmlspecialchars($aviso['contenido']));

                                    ?>

                                </p>

                            </div>
</a>
                            <hr>

                        <?php } ?>

                    <?php }else{ ?>

                        <div class="alert alert-info text-center">

                            <i class="bi bi-megaphone"></i>

                            <br><br>

                            No hay avisos registrados esta semana.

                        </div>

                    <?php } ?>

                </div>

            </div>

        </div>

    </div>

</div>