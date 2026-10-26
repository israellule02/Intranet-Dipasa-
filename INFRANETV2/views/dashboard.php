<?php
require_once("../includes/verificarSesion.php");

/*==========================
    PAGINAS
==========================*/

$pagina = $_GET['page'] ?? 'inicio';

switch($pagina){

    case "inicio":
        $contenido="../pages/inicio.php";
    break;

    case "usuarios":
        $contenido="../pages/control/usuarios.php";
    break;

    case "vacaciones":
        $contenido="../pages/tramites/solicitarvacOfi.php";
    break;

     case "vacaciones2":
        $contenido="../pages/tramites/vacaciones.php";
    break;

    case "permisos":
        $contenido="pages/tramites/permisos.php";
    break;

    case "incapacidades":
        $contenido="pages/tramites/incapacidades.php";
    break;

    case "pendientes":
        $contenido="pages/solicitudes/pendientes.php";
    break;

    case "aceptadas":
        $contenido="pages/solicitudes/aceptadas.php";
    break;

    case "rechazadas":
        $contenido="pages/solicitudes/rechazadas.php";
    break;

    case "verAnuncios":
    $contenido = "../pages/anuncios/ver.php";
break;

case "crearAnuncio":
    $contenido = "../pages/anuncios/crear.php";
break;

case "administrarAnuncios":
    $contenido = "../pages/anuncios/administrar.php";
break;


    
case "detalleAnuncio":
    $contenido = "../pages/anuncios/detalle.php";
break;

case "editarAnuncio":
    $contenido = "../pages/anuncios/editar.php";
break;

case "crearUsuario":
    $contenido = "../pages/control/crearUsuario.php";
break;

case "editarUsuario":
    $contenido = "../pages/control/editarUsuario.php";
break;
case "verUsuario":
    $contenido = "../pages/control/detalleUsuario.php";
break;



case "registrarAsignaciones":
    $contenido="../pages/Asignaciones/registrar.php";
break;

case "consultarAsignaciones":
    $contenido="../pages/Asignaciones/consultar.php";
break;


case "registrarPrestamo":
    $contenido="../pages/Prestamos/registrar.php";
break;
case "consultarPrestamo":
    $contenido="../pages/Prestamos/consultar.php";
break;

case "detallePrestamo":
    $contenido="../pages/Prestamos/detalle.php";
break;

case "editarPrestamo":
    $contenido = "../pages/Prestamos/editar.php";
break;



case "detalleAsignacion":
    $contenido="../pages/Asignaciones/detalle.php";
break;

case "cartaPrestamo":
    $contenido="../pages/prestamos/carta.php";
break;

case "editarAsignacion":
    $contenido = "../pages/Asignaciones/editar.php";
break;

case "estatusPrestamos":
    $contenido="../pages/prestamos/estatus.php";
break;


case "consultarDispositivo":
    $contenido="../pages/dispositivos/consultar.php";
break;


case "registrarDispositivo":
    $contenido="../pages/dispositivos/registrar.php";
break;


case "verDispositivo":
    $contenido="../pages/dispositivos/detalle.php";
break;


case "editarDispositivo":
    $contenido="../pages/dispositivos/editar.php";
break;


case "consultarCredenciales":
    $contenido="../pages/credenciales/consultar.php";
break;

case "registrarCredencial":
    $contenido="../pages/credenciales/registrar.php";
break;


case "detalleCredencial":
    $contenido="../pages/credenciales/detalle.php";
break;

case "editarCredencial":
    $contenido="../pages/credenciales/editar.php";
break;

case "SolicitudesVacacionesRH":
    $contenido = "../pages/Rh/solicitudes.php";
break;


case "solicitudesVacaciones":
    $contenido = "../pages/tramites/solicitudes.php";
break;

case "historialVacaciones":
    $contenido = "../pages/Rh/historial.php";
break;
case "detalleVacaciones":
    $contenido = "../pages/Rh/detalleRegistroVac.php";
break;


case "Registrardias":
    $contenido = "../pages/control/capturardiasvac.php";
break;



case "seguimientoVacaciones":
    $contenido = "../pages/tramites/seguimientoVac.php";
break;


case "ConsultarDias":
    $contenido = "../pages/Rh/consultarDias.php";
break;




    default:
        $contenido="pages/inicio.php";
    break;



}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Infranet Dipasa</title>

<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">



</head>

<body>

<div class="wrapper">

    <!--=====================================
                SIDEBAR
    ======================================-->

    <aside class="sidebar ">

        <div class="sidebar-header">

            <img
            src="../assets/img/logotipo.png"
            class="logo-sidebar"
            alt="Logo">

            <h5>Intranet Dipasa</h5>

        </div>

        <ul class="menu">

            <!--================ INICIO ================-->

            <li>

                <a href="dashboard.php?page=inicio">

                    <i class="bi bi-house-door-fill"></i>

                    <span>Inicio</span>

                </a>

            </li>

            <!--================ ANUNCIOS ================-->

<li class="submenu">

    <a href="#" class="submenu-btn">

        <div>

            <i class="bi bi-megaphone-fill"></i>

            <span>Anuncios</span>

        </div>

        <i class="bi bi-chevron-down flecha"></i>

    </a>

    <ul class="submenu-list">

        <li>

            <a href="dashboard.php?page=verAnuncios">

                <i class="bi bi-eye-fill"></i>

                Ver anuncios

            </a>

        </li>

         <?php if($_SESSION["idRol"] != "4" ){ ?>

        <li>

            <a href="dashboard.php?page=crearAnuncio">

                <i class="bi bi-plus-circle-fill"></i>

                Crear anuncio

            </a>

        </li>
         <?php } ?>
          <?php if($_SESSION["idRol"] == "1" ){ ?>
        <li>
        

            <a href="dashboard.php?page=administrarAnuncios">

                <i class="bi bi-gear-fill"></i>

                Administrar anuncios

            </a>

        </li>
       <?php } ?>
    </ul>

</li>

           <!--================ CONTROL ================-->

<?php
if (
    $_SESSION["idRol"] == "2" ||
    $_SESSION["idRol"] == "1" ||
    ($_SESSION["idRol"] == "5" && $_SESSION["idDep"] == "9") || ($_SESSION["idRol"] == "5" && $_SESSION["idDep"] == "13")
) {
?>

<li class="submenu">

    <a href="#" class="submenu-btn">

        <div>
            <i class="bi bi-people-fill"></i>
            <span>Control</span>
        </div>

        <i class="bi bi-chevron-down flecha"></i>

    </a>

    <ul class="submenu-list">

        <!-- USUARIOS -->
        <li>
            <a href="dashboard.php?page=usuarios">

                <i class="bi bi-person-fill"></i>
                <span>Usuarios</span>

            </a>
        </li>

        <!-- CAPTURAR DÍAS -->
        <li>
            <a href="dashboard.php?page=Registrardias">

                <i class="bi bi-calendar-plus"></i>
                <span>Capturar días de vacaciones</span>

            </a>
        </li>

    </ul>

</li>

<?php
}
?>



            <!--================ TRAMITES ================-->

            <li class="submenu">

                <a href="#" class="submenu-btn">

                    <div>

                        <i class="bi bi-folder2-open"></i>

                        <span>Trámites</span>

                    </div>

                    <i class="bi bi-chevron-down flecha"></i>

                </a>

                <ul class="submenu-list">

                 <li>

                        <a href="dashboard.php?page=vacaciones">

                                 <i class="bi bi-airplane"></i>

                             Solicitar Vacaciones

                        </a>

                    </li>



                     <li>

                        <a href="dashboard.php?page=seguimientoVacaciones">

                          <i class="bi bi-send-check-fill"></i>

                           Seguimiento

                        </a>

                    </li>

    <?php if($_SESSION["idRol"] == "5" ){ ?>


                     <li>

                        <a href="dashboard.php?page=solicitudesVacaciones">

                          <i class="bi bi-envelope-open-fill"></i>

                           Solicitudes de vacaciones

                        </a>

                    </li>

                    
  <?php } ?>
                    

                </ul>

            </li>


            <!--================ CONTROL DE DISPOSITIVOS ================-->
<?php if(($_SESSION["idRol"] == "2"  and $_SESSION["idDep"] == "13") or ($_SESSION["idRol"] == "5"  and $_SESSION["idDep"] == "13")){ ?>
<li class="submenu">

    <a href="#" class="submenu-btn">

        <div>

            <i class="bi bi-laptop"></i>

            <span>Control de dispositivos</span>

        </div>

        <i class="bi bi-chevron-down flecha"></i>


        

    </a>

    <ul class="submenu-list">


        

        <li>

            <a href="dashboard.php?page=consultarDispositivo">

                 <i class="bi bi-pc"></i>

            <span>Consultar dispositivos</span>

            

            </a>

        </li>


        <li>

            <a href="dashboard.php?page=consultarAsignaciones">

                <i class="bi bi-ethernet"></i>

                Consultar Asignaciones

            </a>

        </li>


         <li>

            <a href="dashboard.php?page=consultarPrestamo">

                <i class="bi bi-pc-display-horizontal"></i>

                Consultar Prestamos

            </a>

        </li>


        <li>

            <a href="dashboard.php?page=consultarCredenciales">

                <i class="bi bi-person-lock"></i>

                Consultar Credenciales

                   

            </a>

        </li>

    </ul>

</li>
<?php } ?>

            

        <!--================ RECURSOS HUMANOS ================-->

<?php if($_SESSION["idDep"] == "9"){ ?>

<li class="submenu">

    <!-- MENÚ PRINCIPAL RH -->
    <a href="#" class="submenu-btn">

        <div>
            <i class="bi bi-building-gear"></i>
            <span>Recursos humanos</span>
        </div>

        <i class="bi bi-chevron-down flecha"></i>

    </a>


    <!--================ SUBMENÚ RH ================-->

    <ul class="submenu-list">


        <!--================ VACACIONES ================-->

        <li class="submenu">

            <a href="#" class="submenu-btn">

                <div>

                    <i class="bi bi-calendar-check"></i>

                    <span>Vacaciones</span>

                </div>

                <i class="bi bi-chevron-down flecha"></i>

            </a>


            <!-- OPCIONES DE VACACIONES -->

            <ul class="submenu-list">


                <!-- SOLICITUDES -->

                <li>

                    <a href="dashboard.php?page=SolicitudesVacacionesRH">

                        <i class="bi bi-brightness-high"></i>

                        <span>Solicitudes</span>

                    </a>

                </li>


                <!-- HISTORIAL -->

                <li>

                    <a href="dashboard.php?page=historialVacaciones">

                        <i class="bi bi-folder2-open"></i>

                        <span>Historial</span>

                    </a>

                </li>


            </ul>

        </li>


    </ul>

</li>

<?php } ?>

            <!--================ CERRAR ================-->

            <li class="cerrar">

                <a href="../logout.php">

                    <i class="bi bi-box-arrow-left"></i>

                    <span>Cerrar sesión</span>

                </a>

            </li>

        </ul>

    </aside>

    <!--=====================================
            CONTENIDO
    ======================================-->

    <main class="main-content">

        <!--=========== TOPBAR ===========-->

        <nav class="topbar">

            <button
            class="btn btn-light"
            id="btnMenu">

                <i class="bi bi-list"></i>

            </button>

            <div class="usuario">

                <i class="bi bi-person-circle"></i>

                <span>

                    <?php echo $_SESSION['nombre']; ?>

                </span>

            </div>

        </nav>

        <!--=========== CONTENIDO ===========-->

        <div class="contenido">

            <?php

                if(file_exists($contenido)){

                    include($contenido);

                }else{

                    echo '

                    <div class="alert alert-danger">

                        Página no encontrada.

                    </div>

                    ';

                }

            ?>

        </div>

    </main>

</div>



<script src="../assets/js/bootstrap.bundle.min.js"></script>

<script src="../assets/js/dashboard.js"></script>





</body>




</html>