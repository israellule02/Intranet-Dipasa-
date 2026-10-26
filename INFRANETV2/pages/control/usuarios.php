<?php

require_once("../config/conexion.php");

$buscar = $_GET['buscar'] ?? '';

$sql = "
SELECT
    u.*,
    r.rol,
    d.nombre AS departamento,
    p.descripcion AS puesto
FROM usuarios u
INNER JOIN roles r
ON u.idRol = r.id
INNER JOIN departamentos d
ON u.idDep = d.idDepartamento
INNER JOIN puesto p
ON u.idPuesto = p.idPuesto
WHERE 1=1
";

if($buscar!=""){

    $sql .= " AND (
                u.nombre LIKE :buscar
                OR u.apellidos LIKE :buscar
                OR u.nomusuario LIKE :buscar
                OR u.numNomina LIKE :buscar
                OR p.idPuesto LIKE :buscar
              )";

}

$sql .= " ORDER BY u.nombre,u.apellidos";

$stmt = $conexion->prepare($sql);

if($buscar!=""){

    $stmt->bindValue(":buscar","%".$buscar."%");

}

$stmt->execute();

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">

            <i class="bi bi-people-fill text-success"></i>

            Usuarios

        </h2>

        <a href="dashboard.php?page=crearUsuario"
        class="btn btn-success">

            <i class="bi bi-person-plus-fill"></i>

            Nuevo usuario

        </a>

    </div>

    <?php if(isset($_GET["ok"])){ ?>

        <div class="alert alert-success alert-dismissible fade show">

            Usuario registrado correctamente.

            <button class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    <?php } ?>

    <?php if(isset($_GET["editado"])){ ?>

        <div class="alert alert-success alert-dismissible fade show">

            Usuario actualizado correctamente.

            <button class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    <?php } ?>

    <?php if(isset($_GET["eliminado"])){ ?>

        <div class="alert alert-danger alert-dismissible fade show">

            Usuario eliminado correctamente.

            <button class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    <?php } ?>

    <?php if(isset($_GET["error"])){ ?>

        <div class="alert alert-warning alert-dismissible fade show">

            Ocurrió un error.

            <button class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    <?php } ?>



    <div class="card shadow border-0 rounded-4 mb-4">

        <div class="card-body">

            <form method="GET" action="dashboard.php">

                <input
                type="hidden"
                name="page"
                value="usuarios">

                <div class="row">

                    <div class="col-md-10">

                        <input
                        type="text"
                        class="form-control"
                        name="buscar"
                        placeholder="Buscar por nombre, usuario o nómina..."
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



    <div class="card shadow border-0 rounded-4">

        <div class="card-header bg-success text-white">

            <strong>

                Total de usuarios:
                <?php echo count($usuarios); ?>

            </strong>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-success">

                        <tr>

                            <th>ID</th>

                            <th>Nombre</th>

                            <th>Usuario</th>

                            <th>Nómina</th>

                            <th>Rol</th>

                            <th>Departamento</th>

                            <th>Puesto</th>

                            <th>Ingreso</th>

                            <th class="text-center">

                                Acciones

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    if(count($usuarios)>0){

                        foreach($usuarios as $usuario){

                    ?>

                        <tr>

                            <td>

                                <?php echo $usuario["id"]; ?>

                            </td>

                            <td>

                                <strong>

                                    <?php

                                    echo $usuario["nombre"]." ".$usuario["apellidos"];

                                    ?>

                                </strong>

                            </td>

                            <td>

                                <?php echo $usuario["nomusuario"]; ?>

                            </td>

                            <td>

                                <?php echo $usuario["numNomina"]; ?>

                            </td>

                            <td>

                                <span class="badge bg-success">

                                    <?php echo $usuario["rol"]; ?>

                                </span>

                            </td>

                            <td>

                                <?php echo $usuario["departamento"]; ?>

                            </td>

                            <td>

                                <?php echo $usuario["puesto"]; ?>

                            </td>

                            <td>

                                <?php

                                echo date("d/m/Y",strtotime($usuario["fechaIngreso"]));

                                ?>

                            </td>

                            <td class="text-center">

                            <a
                            href="dashboard.php?page=verUsuario&id=<?php echo $usuario["id"]; ?>"
                            class="btn btn-info btn-sm">

                            <i class="bi bi-eye-fill"></i>

                            </a>

                                <a
                                href="dashboard.php?page=editarUsuario&id=<?php echo $usuario["id"]; ?>"
                                class="btn btn-warning btn-sm">

                                    <i class="bi bi-pencil-fill"></i>

                                </a>

                                <a
                                href="../controllers/EliminarUsuario.php?id=<?php echo $usuario["id"]; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Eliminar este usuario?')">

                                    <i class="bi bi-trash-fill"></i>

                                </a>

                            </td>

                        </tr>

                    <?php

                        }

                    }else{

                    ?>

                    <tr>

                        <td colspan="9" class="text-center py-5">

                            <i class="bi bi-people-fill fs-1 text-secondary"></i>

                            <br><br>

                            No existen usuarios registrados.

                        </td>

                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>