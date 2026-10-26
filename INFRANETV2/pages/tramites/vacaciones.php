<?php

require_once("../config/conexion.php");

if(session_status() === PHP_SESSION_NONE){

    session_start();

}


/*=========================================
OBTENER JEFES DE ÁREA
=========================================*/

$sqlJefes = $conexion->prepare("

    SELECT

        id,

        CONCAT(nombre, ' ', apellidos) AS nombreCompleto

    FROM usuarios

    WHERE idRol = 5

    ORDER BY nombre ASC

");

$sqlJefes->execute();

$jefesdearea = $sqlJefes->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card shadow border-0 rounded-4">

<!-- =========================================
ENCABEZADO
========================================= -->

<div class="card-header bg-success text-white">

<h3 class="mb-0">

<i class="bi bi-sun-fill"></i>

Solicitar vacaciones

</h3>

</div>


<div class="card-body p-4">


<!-- =========================================
MENSAJES DE ERROR
========================================= -->

<?php if(isset($_GET["error"])){ ?>

<div class="alert alert-danger alert-dismissible fade show">

<i class="bi bi-exclamation-triangle-fill"></i>

<?php

if($_GET["error"] == "fechas"){

    echo "La fecha de término debe ser posterior a la fecha de inicio.";

}elseif($_GET["error"] == "pasado"){

    echo "La fecha de inicio no puede ser anterior al día de hoy.";

}elseif($_GET["error"] == "sin_dias"){

    echo "El periodo seleccionado no contiene días que puedan descontarse de vacaciones.";

}elseif($_GET["error"] == "dias_insuficientes"){

    echo "No tienes días suficientes para hacer la solicitud.";

}elseif($_GET["error"] == "sin_seleccion"){

    echo "Debes seleccionar al menos un día de vacaciones.";

}else{

    echo "Ocurrió un error al registrar la solicitud.";

}

?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

<?php } ?>


<form
action="../controllers/SolicitarVacacionesController.php"
method="POST"
id="formVacaciones">


<div class="row">


<!-- =========================================
FECHA DE INICIO
========================================= -->

<div class="col-md-6 mb-3">

<label class="form-label fw-bold">

Fecha de inicio

</label>

<input
type="date"
name="fechainicio"
id="fechainicio"
class="form-control"
min="<?php echo date("Y-m-d"); ?>"
required>

</div>


<!-- =========================================
FECHA DE TÉRMINO
========================================= -->

<div class="col-md-6 mb-3">

<label class="form-label fw-bold">

Fecha de término

</label>

<input
type="date"
name="fechafinal"
id="fechafinal"
class="form-control"
min="<?php echo date("Y-m-d"); ?>"
required>

<small class="text-muted">

El día de término se considera como día de regreso y no se descuenta.

</small>

</div>


<!-- =========================================
DÍAS DE DESCANSO
========================================= -->

<div class="col-12 mb-4">

<label class="form-label fw-bold">

¿Qué días de la semana descansas?

</label>


<div class="border rounded-3 p-3 bg-light">

<div class="row">


<div class="col-md-3 col-6 mb-2">

<div class="form-check">

<input
class="form-check-input dia-descanso"
type="checkbox"
value="1"
id="lunes">

<label
class="form-check-label"
for="lunes">

Lunes

</label>

</div>

</div>


<div class="col-md-3 col-6 mb-2">

<div class="form-check">

<input
class="form-check-input dia-descanso"
type="checkbox"
value="2"
id="martes">

<label
class="form-check-label"
for="martes">

Martes

</label>

</div>

</div>


<div class="col-md-3 col-6 mb-2">

<div class="form-check">

<input
class="form-check-input dia-descanso"
type="checkbox"
value="3"
id="miercoles">

<label
class="form-check-label"
for="miercoles">

Miércoles

</label>

</div>

</div>


<div class="col-md-3 col-6 mb-2">

<div class="form-check">

<input
class="form-check-input dia-descanso"
type="checkbox"
value="4"
id="jueves">

<label
class="form-check-label"
for="jueves">

Jueves

</label>

</div>

</div>


<div class="col-md-3 col-6 mb-2">

<div class="form-check">

<input
class="form-check-input dia-descanso"
type="checkbox"
value="5"
id="viernes">

<label
class="form-check-label"
for="viernes">

Viernes

</label>

</div>

</div>


<div class="col-md-3 col-6 mb-2">

<div class="form-check">

<input
class="form-check-input dia-descanso"
type="checkbox"
value="6"
id="sabado">

<label
class="form-check-label"
for="sabado">

Sábado

</label>

</div>

</div>


<div class="col-md-3 col-6 mb-2">

<div class="form-check">

<input
class="form-check-input dia-descanso"
type="checkbox"
value="0"
id="domingo">

<label
class="form-check-label"
for="domingo">

Domingo

</label>

</div>

</div>


</div>


<small class="text-muted">

Selecciona los días que normalmente no trabajas.
Estos días no se descontarán de tus vacaciones.

</small>

</div>

</div>


<!-- =========================================
LISTA DE DÍAS
========================================= -->

<div class="col-12 mb-4">

<div
id="contenedorDias"
class="d-none">


<div class="card border-0 shadow-sm rounded-4">

<div class="card-header bg-light">

<div class="d-flex justify-content-between align-items-center">

<div>

<strong>

<i class="bi bi-calendar3"></i>

Selecciona los días de vacaciones

</strong>

<br>

<small class="text-muted">

Puedes combinar días completos y medios días.

</small>

</div>

<div>

<span class="badge bg-success fs-6">

Total: <span id="totalDias">0</span> días

</span>

</div>

</div>

</div>


<div class="card-body p-0">


<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead class="table-success">

<tr>

<th>

Fecha

</th>

<th>

Día

</th>

<th class="text-center">

Tipo de vacaciones

</th>

<th class="text-center">

Valor

</th>

</tr>

</thead>


<tbody id="listaDias">

</tbody>

</table>

</div>

</div>

</div>

</div>


<!-- MENSAJE SIN DÍAS -->

<div
id="mensajeSinDias"
class="alert alert-warning d-none">

<i class="bi bi-info-circle-fill"></i>

No existen días laborables disponibles dentro del periodo seleccionado.

</div>

</div>


<!-- =========================================
JEFE DE ÁREA
========================================= -->

<div class="col-md-12 mb-3">

<label class="form-label fw-bold">

Seleccione el jefe para la autorización

</label>


<select
name="idJefe"
class="form-select"
required>

<option value="">

Seleccione...

</option>


<?php foreach($jefesdearea as $jefe){ ?>

<option value="<?php echo $jefe["id"]; ?>">

<?php echo htmlspecialchars(
$jefe["nombreCompleto"]
); ?>

</option>

<?php } ?>

</select>

</div>


<!-- =========================================
RESUMEN
========================================= -->

<div class="col-12 mb-4">

<div class="alert alert-success mb-0">

<div class="row align-items-center">

<div class="col-md-8">

<strong>

<i class="bi bi-calendar-check-fill"></i>

Días que se descontarán de vacaciones:

</strong>

<br>

<small>

Los días de descanso y días festivos no se toman en cuenta.

Puedes combinar días completos y medios días.

</small>

</div>


<div class="col-md-4 text-md-end">

<h2
class="mb-0 fw-bold"
id="diasTomadosVista">

0

</h2>

<small>

días

</small>

</div>

</div>

</div>


<input
type="hidden"
name="diasCalculados"
id="diasCalculados"
value="0">


</div>


<!-- =========================================
BOTONES
========================================= -->

<div class="col-12 text-end">

<a
href="dashboard.php?page=inicio"
class="btn btn-secondary">

<i class="bi bi-x-circle"></i>

Cancelar

</a>


<button
type="submit"
class="btn btn-success"
id="btnSolicitar"
disabled>

<i class="bi bi-send-fill"></i>

Solicitar vacaciones

</button>

</div>


</div>

</form>

</div>

</div>

</div>

</div>

</div>


<script>

/*=========================================
ELEMENTOS
=========================================*/

const fechaInicio =
document.getElementById("fechainicio");

const fechaFinal =
document.getElementById("fechafinal");

const contenedorDias =
document.getElementById("contenedorDias");

const listaDias =
document.getElementById("listaDias");

const mensajeSinDias =
document.getElementById("mensajeSinDias");

const totalDias =
document.getElementById("totalDias");

const diasTomadosVista =
document.getElementById("diasTomadosVista");

const diasCalculados =
document.getElementById("diasCalculados");

const btnSolicitar =
document.getElementById("btnSolicitar");


/*=========================================
FESTIVOS
=========================================*/

function obtenerFestivos(anio){

    return [

        anio + "-01-01",

        anio + "-02-02",

        anio + "-03-16",

        anio + "-05-01",

        anio + "-09-16",

        anio + "-11-16",

        anio + "-12-25"

    ];

}


/*=========================================
FORMATO FECHA
=========================================*/

function formatoFecha(fecha){

    const anio =
        fecha.getFullYear();

    const mes =
        String(
            fecha.getMonth() + 1
        ).padStart(2,"0");

    const dia =
        String(
            fecha.getDate()
        ).padStart(2,"0");

    return anio + "-" + mes + "-" + dia;

}


/*=========================================
NOMBRE DEL DÍA
=========================================*/

function nombreDia(numero){

    const dias = [

        "Domingo",

        "Lunes",

        "Martes",

        "Miércoles",

        "Jueves",

        "Viernes",

        "Sábado"

    ];

    return dias[numero];

}


/*=========================================
FORMATO FECHA VISIBLE
=========================================*/

function fechaVisible(fecha){

    return fecha.toLocaleDateString(
        "es-MX",
        {
            day: "2-digit",
            month: "2-digit",
            year: "numeric"
        }
    );

}


/*=========================================
OBTENER DESCANSOS
=========================================*/

function obtenerDescansos(){

    let descansos = [];

    document
    .querySelectorAll(".dia-descanso:checked")
    .forEach(function(elemento){

        descansos.push(
            parseInt(elemento.value)
        );

    });

    return descansos;

}


/*=========================================
CALCULAR DÍAS
=========================================*/

function generarDiasVacaciones(){

    listaDias.innerHTML = "";

    contenedorDias.classList.add("d-none");

    mensajeSinDias.classList.add("d-none");

    totalDias.innerText = "0";

    diasTomadosVista.innerText = "0";

    diasCalculados.value = "0";

    btnSolicitar.disabled = true;


    if(!fechaInicio.value || !fechaFinal.value){

        return;

    }


    const inicio =
        new Date(
            fechaInicio.value + "T00:00:00"
        );

    const final =
        new Date(
            fechaFinal.value + "T00:00:00"
        );


    if(final <= inicio){

        return;

    }


    const descansos =
        obtenerDescansos();


    const festivos =
        obtenerFestivos(
            inicio.getFullYear()
        );


    let fechaActual =
        new Date(inicio);


    let cantidadDias = 0;


    /*=========================================
    RECORRER HASTA UN DÍA ANTES DEL REGRESO
    =========================================*/

    while(fechaActual < final){

        const diaSemana =
            fechaActual.getDay();

        const fechaTexto =
            formatoFecha(fechaActual);


        const esDescanso =
            descansos.includes(
                diaSemana
            );


        const esFestivo =
            festivos.includes(
                fechaTexto
            );


        /*=========================================
        SOLO MOSTRAR DÍAS LABORABLES
        =========================================*/

        if(!esDescanso && !esFestivo){

            cantidadDias++;


            const fila =
                document.createElement("tr");


            /*=====================================
            FECHA
            =====================================*/

            const celdaFecha =
                document.createElement("td");

            celdaFecha.innerHTML = `

                <strong>
                    ${fechaVisible(fechaActual)}
                </strong>

            `;


            /*=====================================
            DÍA
            =====================================*/

            const celdaDia =
                document.createElement("td");

            celdaDia.innerText =
                nombreDia(diaSemana);


            /*=====================================
            SELECT
            =====================================*/

            const celdaTipo =
                document.createElement("td");

            celdaTipo.className =
                "text-center";


            const select =
                document.createElement("select");

            select.className =
                "form-select form-select-sm tipo-dia";


            /*
            IMPORTANTE:

            El nombre queda como:

            diasSolicitud[2026-09-22]

            */

            select.name =
                "diasSolicitud[" +
                fechaTexto +
                "]";


            select.innerHTML = `

                <option value="0">
                    No solicitar
                </option>

                <option value="0.5">
                    Medio día
                </option>

                <option value="1" selected>
                    Día completo
                </option>

            `;


            celdaTipo.appendChild(
                select
            );


            /*=====================================
            VALOR
            =====================================*/

            const celdaValor =
                document.createElement("td");

            celdaValor.className =
                "text-center";


            const badge =
                document.createElement("span");

            badge.className =
                "badge bg-success valor-dia";


            badge.innerText =
                "1 día";


            celdaValor.appendChild(
                badge
            );


            /*=====================================
            ACTUALIZAR VALOR
            =====================================*/

            select.addEventListener(
                "change",
                function(){

                    actualizarValorFila(
                        select,
                        badge
                    );

                    calcularTotal();

                }
            );


            fila.appendChild(
                celdaFecha
            );

            fila.appendChild(
                celdaDia
            );

            fila.appendChild(
                celdaTipo
            );

            fila.appendChild(
                celdaValor
            );


            listaDias.appendChild(
                fila
            );

        }


        fechaActual.setDate(
            fechaActual.getDate() + 1
        );

    }


    /*=========================================
    MOSTRAR RESULTADO
    =========================================*/

    if(cantidadDias === 0){

        mensajeSinDias.classList.remove(
            "d-none"
        );

        return;

    }


    contenedorDias.classList.remove(
        "d-none"
    );


    calcularTotal();

}


/*=========================================
ACTUALIZAR VALOR DE FILA
=========================================*/

function actualizarValorFila(
    select,
    badge
){

    const valor =
        parseFloat(
            select.value
        );


    if(valor === 1){

        badge.innerText =
            "1 día";

        badge.className =
            "badge bg-success valor-dia";

    }else if(valor === 0.5){

        badge.innerText =
            "0.5 días";

        badge.className =
            "badge bg-warning text-dark valor-dia";

    }else{

        badge.innerText =
            "No solicitado";

        badge.className =
            "badge bg-secondary valor-dia";

    }

}


/*=========================================
CALCULAR TOTAL
=========================================*/

function calcularTotal(){

    let total = 0;


    document
    .querySelectorAll(".tipo-dia")
    .forEach(function(select){

        total +=
            parseFloat(
                select.value
            );

    });


    total =
        Math.round(
            total * 2
        ) / 2;


    totalDias.innerText =
        total;


    diasTomadosVista.innerText =
        total;


    diasCalculados.value =
        total;


    btnSolicitar.disabled =
        total <= 0;

}


/*=========================================
EVENTOS
=========================================*/

fechaInicio.addEventListener(
    "change",
    generarDiasVacaciones
);


fechaFinal.addEventListener(
    "change",
    generarDiasVacaciones
);


document
.querySelectorAll(".dia-descanso")
.forEach(function(elemento){

    elemento.addEventListener(
        "change",
        generarDiasVacaciones
    );

});


/*=========================================
VALIDAR FORMULARIO
=========================================*/

document
.getElementById("formVacaciones")
.addEventListener(
    "submit",
    function(event){

        const total =
            parseFloat(
                diasCalculados.value
            );


        if(!total || total <= 0){

            event.preventDefault();

            alert(
                "Debes seleccionar al menos un día de vacaciones."
            );

            return;

        }

    }
);

</script>