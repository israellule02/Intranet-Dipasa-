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





/*=========================================

OBTENER DEPARTAMENTOS

=========================================*/



$sqlDepartamentos = $conexion->prepare("



    SELECT

        idDepartamento,

        nombre



    FROM departamentos



    ORDER BY nombre ASC



");



$sqlDepartamentos->execute();



$departamentos = $sqlDepartamentos->fetchAll(PDO::FETCH_ASSOC);



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



}elseif($_GET["error"] == "jefe"){



    echo "El jefe seleccionado no es válido.";



}elseif($_GET["error"] == "campos"){



    echo "Debes completar todos los campos obligatorios.";



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



<div class="alert alert-info mb-0">



<div class="d-flex align-items-center">



<div class="me-3">







</div>



<div>



<strong>

Días de descanso

</strong>



<br>



<span>



Los sábados y domingos se consideran

automáticamente como días de descanso

y no se descontarán de tus vacaciones.



</span>



</div>



</div>



</div>



</div>





<!-- =========================================

DÍAS DE DESCANSO AUTOMÁTICOS

========================================= -->



<input

type="hidden"

name="diasDescanso[]"

value="6">



<input

type="hidden"

name="diasDescanso[]"

value="0">





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



Total:



<span id="totalDias">0</span>



días



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

CORREO DE AUSENCIA

========================================= -->



<div class="col-12 mb-4">



<div class="card border-0 shadow-sm rounded-4">



<div class="card-body">



<div class="form-check form-switch">



<input

class="form-check-input"

type="checkbox"

id="enviarCorreoAusencia"

name="enviarCorreoAusencia"

value="1"

>



<label

class="form-check-label fw-bold"

for="enviarCorreoAusencia"

>



<i class="bi bi-envelope-paper-fill text-success"></i>



¿Desea mandar correo de ausencia automático?



</label>



</div>



<small class="text-muted d-block mt-2">



Si activa esta opción, se enviará un aviso de ausencia

a las áreas seleccionadas cuando Recursos Humanos

acepte sus vacaciones.



</small>



</div>



</div>



</div>





<!-- =========================================

ÁREAS PARA CORREO DE AUSENCIA

========================================= -->



<div

class="col-12 mb-4 d-none"

id="contenedorAreasAusencia"

>



<div class="card border-0 shadow-sm rounded-4">



<div class="card-header bg-success text-white">



<strong>



<i class="bi bi-people-fill"></i>



Seleccione las áreas que desea notificar



</strong>



</div>



<div class="card-body">



<div class="row">



<?php foreach($departamentos as $departamento){ ?>



<div class="col-md-4 col-lg-3 mb-3">



<div class="form-check">



<input

class="form-check-input area-ausencia"

type="checkbox"

name="areasAusencia[]"

value="<?php echo $departamento["idDepartamento"]; ?>"

id="area_<?php echo $departamento["idDepartamento"]; ?>"

>



<label

class="form-check-label"

for="area_<?php echo $departamento["idDepartamento"]; ?>"

>



<?php echo htmlspecialchars(

$departamento["nombre"]

); ?>



</label>



</div>



</div>



<?php } ?>



</div>





<div

id="mensajeAreasAusencia"

class="alert alert-warning d-none mt-3">



<i class="bi bi-exclamation-triangle-fill"></i>



Seleccione al menos un área para enviar el aviso.



</div>





<div

id="resumenAreasAusencia"

class="alert alert-success d-none mt-3 mb-0">



<i class="bi bi-check-circle-fill"></i>



<span id="textoResumenAreas">

</span>



</div>



</div>



</div>



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






<style>
    #loaderSolicitarVacaciones {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.62);
        z-index: 20000;
    }

    #loaderSolicitarVacaciones.mostrar {
        display: flex;
    }

    #loaderSolicitarVacaciones .loader-card {
        width: 100%;
        max-width: 390px;
        padding: 30px 26px;
        text-align: center;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 18px 55px rgba(0, 0, 0, 0.25);
    }

    #loaderSolicitarVacaciones .spinner-border {
        width: 3rem;
        height: 3rem;
    }
</style>

<div id="loaderSolicitarVacaciones" aria-hidden="true">
    <div class="loader-card">
        <div class="spinner-border text-success mb-3" role="status">
            <span class="visually-hidden">Procesando...</span>
        </div>
        <h5 class="fw-bold mb-2">Enviando solicitud</h5>
        <p class="text-muted mb-0">
            Estamos registrando tu solicitud de vacaciones. Por favor, espera y no cierres esta ventana.
        </p>
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

CORREO DE AUSENCIA

=========================================*/



const checkboxCorreoAusencia =

document.getElementById("enviarCorreoAusencia");



const contenedorAreasAusencia =

document.getElementById("contenedorAreasAusencia");



const mensajeAreasAusencia =

document.getElementById("mensajeAreasAusencia");



const resumenAreasAusencia =

document.getElementById("resumenAreasAusencia");



const textoResumenAreas =

document.getElementById("textoResumenAreas");





/*=========================================

MOSTRAR / OCULTAR ÁREAS

=========================================*/



checkboxCorreoAusencia.addEventListener(

"change",

function(){



    if(this.checked){



        contenedorAreasAusencia

            .classList.remove("d-none");



    }else{



        contenedorAreasAusencia

            .classList.add("d-none");



        mensajeAreasAusencia

            .classList.add("d-none");



        resumenAreasAusencia

            .classList.add("d-none");





        document

        .querySelectorAll(".area-ausencia")

        .forEach(function(checkbox){



            checkbox.checked = false;



        });



    }



});





/*=========================================

ACTUALIZAR RESUMEN DE ÁREAS

=========================================*/



function actualizarResumenAreas(){



    const seleccionadas =

        document.querySelectorAll(

            ".area-ausencia:checked"

        );





    if(seleccionadas.length === 0){



        resumenAreasAusencia

            .classList.add("d-none");



        return;



    }





    const nombres = [];





    seleccionadas.forEach(function(checkbox){



        const label =

            document.querySelector(

                'label[for="' +

                checkbox.id +

                '"]'

            );



        if(label){



            nombres.push(

                label.innerText.trim()

            );



        }



    });





    textoResumenAreas.innerHTML =

        "<strong>Áreas seleccionadas:</strong> " +

        nombres.join(", ");





    resumenAreasAusencia

        .classList.remove("d-none");





    mensajeAreasAusencia

        .classList.add("d-none");



}





/*=========================================

EVENTOS ÁREAS

=========================================*/



document

.querySelectorAll(".area-ausencia")

.forEach(function(checkbox){



    checkbox.addEventListener(

        "change",

        actualizarResumenAreas

    );



});





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



    /*

        6 = Sábado

        0 = Domingo

    */



    return [6, 0];



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

EVENTOS FECHAS

=========================================*/



fechaInicio.addEventListener(

    "change",

    generarDiasVacaciones

);





fechaFinal.addEventListener(

    "change",

    generarDiasVacaciones

);





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





        /*=====================================

        VALIDAR DÍAS

        =====================================*/



        if(!total || total <= 0){



            event.preventDefault();



            alert(

                "Debes seleccionar al menos un día de vacaciones."

            );



            return;



        }





        /*=====================================

        VALIDAR ÁREAS DE AUSENCIA

        =====================================*/



        if(checkboxCorreoAusencia.checked){



            const areasSeleccionadas =

                document.querySelectorAll(

                    ".area-ausencia:checked"

                );





            if(areasSeleccionadas.length === 0){



                event.preventDefault();





                mensajeAreasAusencia

                    .classList.remove("d-none");





                contenedorAreasAusencia

                    .scrollIntoView({

                        behavior: "smooth",

                        block: "center"

                    });





                return;



            }



        }

        /*
         * CONFIRMACIÓN Y BÚFER DE CARGA
         * La confirmación se ejecuta después de validar el formulario.
         * Si el usuario cancela, se cancela el envío y el búfer permanece oculto.
         */
        const loaderVacaciones = document.getElementById("loaderSolicitarVacaciones");

        const confirmarSolicitud = window.confirm(
            "¿Estás seguro de que deseas enviar tu solicitud de vacaciones?"
        );

        if (!confirmarSolicitud) {
            event.preventDefault();

            if (loaderVacaciones) {
                loaderVacaciones.classList.remove("mostrar");
                loaderVacaciones.setAttribute("aria-hidden", "true");
            }

            return;
        }

        if (loaderVacaciones) {
            loaderVacaciones.classList.add("mostrar");
            loaderVacaciones.setAttribute("aria-hidden", "false");
        }

        btnSolicitar.disabled = true;
        btnSolicitar.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Enviando solicitud...';

    }

);





</script>