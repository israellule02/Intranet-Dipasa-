<?php
session_start();

if(isset($_SESSION['usuario'])){
    header("Location: views/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Infranet Dipasa</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>

<div class="container">

    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-xl-7 col-lg-8 col-md-9 col-sm-11">

            <div class="card login-card shadow-lg border-0">

                <div class="card-header login-header">

                    <div class="text-center">

                        <div class="logo">

                            <img
                                src="./assets/img/logo.png"
                                alt="Logo"
                                class="logo-img">

                        </div>

                        <h3 class="text-white fw-bold mt-3">
                            Infranet Dipasa
                        </h3>

                    
                    </div>

                </div>

                <div class="card-body p-5">

                    <h2 class="text-center fw-bold">
                        Iniciar Sesión
                    </h2>

                    <p class="text-center text-muted mb-4">
                        Ingrese sus credenciales
                    </p>

                    <form action="controllers/LoginController.php" method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Usuario
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-person-fill"></i>

                                </span>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="usuario"
                                    placeholder="Ingrese su usuario"
                                    required>

                            </div>

                        </div>

                        <div class="mb-4">

                            <label class="form-label">
                                Contraseña
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-lock-fill"></i>

                                </span>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    placeholder="Ingrese su contraseña"
                                    required>

                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    id="mostrarPassword">

                                    <i
                                        id="iconoPassword"
                                        class="bi bi-eye-fill">
                                    </i>

                                </button>

                            </div>

                        </div>

                        <button
                            class="btn btn-success w-100 py-2 rounded-pill">

                            <i class="bi bi-box-arrow-in-right me-2"></i>

                            Iniciar sesión

                        </button>

                    </form>

                    <?php if(isset($_GET['error'])){ ?>

                        <div class="alert alert-danger mt-4 text-center">

                            Usuario o contraseña incorrectos.

                        </div>

                    <?php } ?>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

const boton = document.getElementById("mostrarPassword");
const password = document.getElementById("password");
const icono = document.getElementById("iconoPassword");

boton.addEventListener("click", function(){

    if(password.type==="password"){

        password.type="text";

        icono.classList.remove("bi-eye-fill");
        icono.classList.add("bi-eye-slash-fill");

    }else{

        password.type="password";

        icono.classList.remove("bi-eye-slash-fill");
        icono.classList.add("bi-eye-fill");

    }

});

</script>

<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>