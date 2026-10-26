<?php

require_once("../config/conexion.php");

/*====================================================
    FILTROS
====================================================*/

$buscar = isset($_GET["buscar"])
    ? trim($_GET["buscar"])
    : "";

$tipo = isset($_GET["tipo"])
    ? trim($_GET["tipo"])
    : "";


/*====================================================
    TIPOS DE CREDENCIAL
====================================================*/

$sqlTipos = $conexion->prepare("
    SELECT
        id,
        nombre
    FROM tiposdecredeciales
    ORDER BY nombre ASC
");

$sqlTipos->execute();

$tipos = $sqlTipos->fetchAll(PDO::FETCH_ASSOC);


/*====================================================
    CONSULTA PRINCIPAL
====================================================*/

$sql = "
    SELECT

        c.id,
        c.identificador,
        c.idDispositivo,
        c.contraseña,

        tc.nombre AS tipoCredencial,

        d.id AS dispositivoId,
        d.marca,
        d.numdeserie,
        
        d.caracteristicas

    FROM credenciales c

    LEFT JOIN tiposdecredeciales tc
        ON c.idTipoDeCredencial = tc.id

    LEFT JOIN dispositivos d
        ON c.idDispositivo = d.id

    WHERE 1=1
";


/*====================================================
    FILTRO DE BÚSQUEDA
====================================================*/

$parametros = array();


if($buscar != ""){

    $sql .= "

        AND (

            c.identificador LIKE :buscar

            OR d.marca LIKE :buscar

            OR d.numdeserie LIKE :buscar

           

            OR CAST(d.id AS CHAR) LIKE :buscar

        )

    ";

    $parametros[":buscar"] = "%".$buscar."%";

}


/*====================================================
    FILTRO POR TIPO
====================================================*/

if($tipo != ""){

    $sql .= "

        AND c.idTipoDeCredencial = :tipo

    ";

    $parametros[":tipo"] = $tipo;

}


/*====================================================
    ORDEN
====================================================*/

$sql .= "

    ORDER BY c.id DESC

";


/*====================================================
    EJECUTAR
====================================================*/

$consulta = $conexion->prepare($sql);

foreach($parametros as $nombre => $valor){

    if($nombre == ":tipo"){

        $consulta->bindValue(
            $nombre,
            $valor,
            PDO::PARAM_INT
        );

    }else{

        $consulta->bindValue(
            $nombre,
            $valor,
            PDO::PARAM_STR
        );

    }

}

$consulta->execute();

$credenciales = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

    <!--=================================================
        ENCABEZADO
    =================================================-->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                <i class="bi bi-key-fill text-success"></i>

                Credenciales

            </h2>

            <p class="text-muted mb-0">

                Consulta de credenciales registradas

            </p>

        </div>

        <a
            href="dashboard.php?page=registrarCredencial"
            class="btn btn-success">

            <i class="bi bi-plus-circle"></i>

            Nueva credencial

        </a>

    </div>


    <!--=================================================
        FILTROS
    =================================================-->

    <div class="card shadow border-0 rounded-4 mb-4">

        <div class="card-header bg-success text-white">

            <h5 class="mb-0">

                <i class="bi bi-search"></i>

                Buscar credencial

            </h5>

        </div>

        <div class="card-body">

            <form
                method="GET"
                action="dashboard.php">

                <input
                    type="hidden"
                    name="page"
                    value="consultarCredenciales">


                <div class="row g-3">


                    <!--=================================
                        BUSCAR
                    =================================-->

                    <div class="col-md-7">

                        <label class="form-label fw-bold">

                            Buscar

                        </label>

                        <input
                            type="text"
                            name="buscar"
                            class="form-control"
                            value="<?php echo htmlspecialchars($buscar); ?>"
                            placeholder="Identificador, marca, número de serie o ID del dispositivo...">

                    </div>


                    <!--=================================
                        TIPO
                    =================================-->

                    <div class="col-md-3">

                        <label class="form-label fw-bold">

                            Tipo de credencial

                        </label>

                        <select
                            name="tipo"
                            class="form-select">

                            <option value="">

                                Todos

                            </option>

                            <?php foreach($tipos as $item){ ?>

                                <option
                                    value="<?php echo $item["id"]; ?>"
                                    <?php
                                    if($tipo == $item["id"]){
                                        echo "selected";
                                    }
                                    ?>>

                                    <?php
                                    echo htmlspecialchars(
                                        $item["nombre"]
                                    );
                                    ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>


                    <!--=================================
                        BOTONES
                    =================================-->

                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-success w-100">

                            <i class="bi bi-search"></i>

                            Buscar

                        </button>

                    </div>


                </div>

            </form>

        </div>

    </div>


    <!--=================================================
        RESULTADOS
    =================================================-->

    <div class="card shadow border-0 rounded-4">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between">

                <h5 class="mb-0">

                    Resultados

                </h5>

                <span class="badge bg-success">

                    <?php echo count($credenciales); ?>

                    registros

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-success">

                        <tr>

                            <th class="text-center">

                                ID

                            </th>

                            <th>

                                Identificador

                            </th>

                            <th>

                                Tipo

                            </th>

                            <th>

                                Dispositivo

                            </th>

                            <th>

                                Acción

                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if(count($credenciales) > 0){ ?>


                        <?php foreach($credenciales as $credencial){ ?>

                        <tr>


                            <!-- ID -->

                            <td class="text-center">

                                <?php

                                echo $credencial["id"];

                                ?>

                            </td>


                            <!-- IDENTIFICADOR -->

                            <td>
                                
                                <span class="fw-bold">

                                    <?php
                                     if(!empty($credencial["identificador"])){

                                    echo htmlspecialchars(
                                        $credencial["identificador"]
                                    );
                                     }else {

                                     
                                ?>

                                    <span class="text-muted">

                                        Sin identificador ligado

                                    </span>

                                <?php


                                     }
                                    ?>

                                </span>

                            </td>


                            <!-- TIPO -->

                            <td>

                                <?php

                                if(!empty($credencial["tipoCredencial"])){

                                    echo htmlspecialchars(
                                        $credencial["tipoCredencial"]
                                    );

                                }else{

                                    echo '<span class="text-muted">
                                            Sin tipo
                                          </span>';

                                }

                                ?>

                            </td>


                            <!-- DISPOSITIVO -->

                            <td>

                                <?php

                                if(!empty($credencial["dispositivoId"])){

                                ?>

                                    <div>

                                        <strong>

                                            <?php

                                            echo htmlspecialchars(
                                                $credencial["marca"]
                                            );

                                            ?>

                                        </strong>

                                    </div>

                                    <small class="text-muted">

                                        Serie:

                                        <?php

                                        echo htmlspecialchars(
                                            !empty($credencial["numdeserie"])
                                            ? $credencial["numdeserie"]
                                            : $credencial["numereserie"]
                                        );

                                        ?>

                                    </small>

                                     <small class="text-muted">

                                        ID DISPOSITIVO: 

                                        <?php

                                        echo htmlspecialchars(
                                            !empty($credencial["idDispositivo"])
                                            ? $credencial["idDispositivo"]
                                            : $credencial["idDispositivoS"]
                                        );

                                        ?>

                                    </small>

                                <?php

                                }else{

                                ?>

                                    <span class="text-muted">

                                        Sin dispositivo ligado

                                    </span>

                                <?php

                                }

                                ?>

                            </td>


                            <!-- ACCIÓN -->

                            <td>

                                <a
                                    href="dashboard.php?page=detalleCredencial&id=<?php echo $credencial["id"]; ?>"
                                    class="btn btn-sm btn-outline-success">

                                    <i class="bi bi-eye"></i>

                                    Ver

                                </a>

                                
                                <a 
                                href="../controllers/EliminarCredencial.php?id=<?php echo $credencial["id"]; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Eliminar esta credencial?');">

                                <i class="bi bi-trash-fill"></i>

                                </a>


                                 <a
                                href="dashboard.php?page=editarCredencial&id=<?php echo $credencial["id"]; ?>"
                                class="btn btn-warning btn-sm"
                                title="Editar">

                                <i class="bi bi-pencil-fill"></i>

                            </a>
                            </td>


                        </tr>

                        <?php } ?>


                    <?php }else{ ?>

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-5">

                                <i
                                    class="bi bi-search fs-1 text-muted">
                                </i>

                                <p class="text-muted mt-2 mb-0">

                                    No se encontraron credenciales.

                                </p>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>