<?php

require_once("../config/conexion.php");


/*=========================================
OBTENER USUARIOS
=========================================*/

$sqlUsuarios = $conexion->prepare("

    SELECT

        id,

        CONCAT(nombre, ' ', apellidos) AS nombreCompleto

    FROM usuarios

    ORDER BY nombre ASC, apellidos ASC

");

$sqlUsuarios->execute();

$usuarios = $sqlUsuarios->fetchAll(PDO::FETCH_ASSOC);


/*=========================================
OBTENER ÚLTIMO PERÍODO REGISTRADO
=========================================*/

/*=========================================
OBTENER PERÍODOS
=========================================*/

$sqlPeriodos = $conexion->prepare("

    SELECT

        idPeriodo,
        fechaInicio,
        fechaFinal,
        descripcion

    FROM periodosvac

    ORDER BY idPeriodo DESC

");

$sqlPeriodos->execute();

$periodos = $sqlPeriodos->fetchAll(PDO::FETCH_ASSOC);



?>


<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow border-0 rounded-4">

                <div class="card-header bg-success text-white">

                    <h3 class="mb-0">

                        <i class="bi bi-calendar-plus-fill"></i>

                        Registrar días de vacaciones

                    </h3>

                </div>


                <div class="card-body p-4">


                    


                        <form
                            action="../controllers/RegistrarDiasPeriodoController.php"
                            method="POST">


                            <!--=========================================
                            EMPLEADO
                            =========================================-->

<div class="col-md-6 mb-3">

<label class="form-label">
<i class="bi bi-person-fill"></i>
Empleado

</label>

<input
type="text"
id="buscarEmpleado"
class="form-control"
list="listaEmpleados"
placeholder="Buscar empleado..."
autocomplete="off"
required>

<datalist id="listaEmpleados">

<?php foreach($usuarios as $usuario){ ?>

<option
value="<?php echo htmlspecialchars($usuario["nombreCompleto"]); ?>"
data-id="<?php echo $usuario["id"]; ?>">
</option>

<?php } ?>

</datalist>

<input
type="hidden"
name="idUsuario"
id="idUsuario"
required>

</div>


                            <!--=========================================
                            PERÍODO
                            =========================================-->

                            <!--=========================================
PERÍODO
=========================================-->

<div class="mb-4">

    <label class="form-label fw-bold">

        <i class="bi bi-calendar-range-fill"></i>

        Seleccione el período

    </label>


    <select
        name="idPeriodo"
        class="form-select"
        required>

        <option value="">

            Seleccione un período...

        </option>


        <?php foreach($periodos as $periodo){ ?>

            <option
                value="<?php echo $periodo["idPeriodo"]; ?>">

                <?php

                echo htmlspecialchars(
                    $periodo["descripcion"]
                );

                ?>

                |

                <?php

                echo date(
                    "d/m/Y",
                    strtotime($periodo["fechaInicio"])
                );

                ?>

                -

                <?php

                echo date(
                    "d/m/Y",
                    strtotime($periodo["fechaFinal"])
                );

                ?>

            </option>

        <?php } ?>

    </select>


    <div class="form-text">

        Seleccione el período al que corresponden
        los días disponibles del empleado.

    </div>

</div>
                            <!--=========================================
                            DÍAS DISPONIBLES
                            =========================================-->

                            <div class="mb-4">

                                <label class="form-label fw-bold">

                                    <i class="bi bi-calendar-check-fill"></i>

                                    Días disponibles

                                </label>


                                <input
                                    type="number"
                                    name="diasDisponibles"
                                    class="form-control"
                                    min="0"
                                    step="0.5"
                                    placeholder="Ejemplo: 12"
                                    required>


                                <div class="form-text">

                                    Captura los días de vacaciones disponibles
                                    para este empleado.

                                </div>

                            </div>


                            <!--=========================================
                            BOTONES
                            =========================================-->

                            <div class="text-end">

                                <a
                                    href="dashboard.php?page=consultarDiasPeriodo"
                                    class="btn btn-secondary">

                                    <i class="bi bi-x-circle"></i>

                                    Cancelar

                                </a>


                                <button
                                    type="submit"
                                    class="btn btn-success">

                                    <i class="bi bi-save-fill"></i>

                                    Guardar días

                                </button>

                            </div>


                        </form>


                  


                </div>

            </div>

        </div>

    </div>

</div>

<script>


    /*=========================================
BUSCAR EMPLEADO
=========================================*/

const inputEmpleado = document.getElementById("buscarEmpleado");
const idUsuario = document.getElementById("idUsuario");

inputEmpleado.addEventListener("input", function(){

    idUsuario.value = "";

    document.querySelectorAll("#listaEmpleados option").forEach(function(opcion){

        if(opcion.value === inputEmpleado.value){

            idUsuario.value = opcion.dataset.id;

        }

    });

});

</script>