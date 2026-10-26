<?php

session_start();

require_once("../config/conexion.php");
require_once("../assets/fpdf/fpdf.php");

date_default_timezone_set("America/Mexico_City");

/*=========================================
VALIDAR ID
=========================================*/

if(!isset($_GET["id"])){

    die("Asignación no encontrada.");

}

$id = intval($_GET["id"]);

/*=========================================
OBTENER ASIGNACIÓN
=========================================*/

$sql = $conexion->prepare("

SELECT

a.*,

CONCAT(u.nombre,' ',u.apellidos) AS empleado,

u.numNomina,

d.marca,

d.numDeSerie,

d.caracteristicas,

t.nombre AS tipoDispositivo,

e.nombre AS estado,

es.nombre AS estatus

FROM Asignaciones a

INNER JOIN usuarios u
ON a.idUsuario=u.id

INNER JOIN dispositivos d
ON a.idDispositivo=d.id

INNER JOIN pre_tipodedispositivo t
ON d.idTipoDis=t.id

INNER JOIN pre_estado e
ON a.idEstado=e.id

INNER JOIN pre_estatus es
ON a.idpreestatus=es.id

WHERE a.id=:id

");

$sql->bindParam(":id",$id,PDO::PARAM_INT);

$sql->execute();

$asignacion = $sql->fetch(PDO::FETCH_ASSOC);

if(!$asignacion){

    die("La asignación no existe.");

}

/*=========================================
USUARIO QUE IMPRIME
=========================================*/

$sqlUsuario = $conexion->prepare("

SELECT

CONCAT(nombre,' ',apellidos) AS encargado

FROM usuarios

WHERE id=:id

");

$sqlUsuario->bindParam(":id",$_SESSION["idUsuario"],PDO::PARAM_INT);

$sqlUsuario->execute();

$encargado = $sqlUsuario->fetch(PDO::FETCH_ASSOC);

/*=========================================
TIPO DE CARTA
=========================================*/

$esMobile = strtoupper(trim($asignacion["tipoDispositivo"]))=="MOBILE";

/*=========================================
CREAR PDF
=========================================*/

$pdf = new FPDF("P","mm","Letter");

$pdf->SetMargins(18,15,18);

$pdf->SetAutoPageBreak(true,18);

$pdf->AliasNbPages();

$pdf->AddPage();

$pdf->SetTitle("Carta Responsiva");

$pdf->SetAuthor("DIPASA");

$pdf->SetFont("Arial","",10);



/*=========================================
USUARIO QUE IMPRIME
=========================================*/

$sqlUsuario = $conexion->prepare("

SELECT

CONCAT(nombre,' ',apellidos) AS encargado

FROM usuarios

WHERE id = :id

");

$sqlUsuario->bindParam(":id",$_SESSION["idUsuario"],PDO::PARAM_INT);

$sqlUsuario->execute();

$encargado = $sqlUsuario->fetch(PDO::FETCH_ASSOC);

/*=========================================
TIPO DE CARTA
=========================================*/

$esMobile = strtoupper(trim($asignacion["tipoDispositivo"])) == "MOVIL";

/*=========================================
PDF
=========================================*/

$pdf = new FPDF("P","mm","Letter");

$pdf->SetMargins(18,15,18);

$pdf->SetAutoPageBreak(true,18);

$pdf->AliasNbPages();

$pdf->AddPage();

$pdf->SetTitle("Carta Responsiva");

$pdf->SetAuthor("DIPASA");

$pdf->SetFont("Arial","",10);

/*=========================================
LOGO
=========================================*/

$pdf->Image(
    "../assets/img/logotipo.png",
    18,
    12,
    28
);

/*=========================================
ENCABEZADO
=========================================*/

$pdf->SetXY(52,15);

$pdf->SetFont("Arial","B",14);

$pdf->Cell(
90,
6,
utf8_decode("DIPASA"),
0,
1
);

$pdf->SetX(52);

$pdf->SetFont("Arial","",9);

$pdf->Cell(
90,
5,
utf8_decode("Carta Responsiva de Resguardo de Equipo"),
0,
1
);

$pdf->SetXY(150,15);

$pdf->SetFont("Arial","",10);

$pdf->Cell(
40,
6,
utf8_decode("Fecha: ".date("d/m/Y")),
0,
1,
"R"
);

$pdf->Ln(15);

$pdf->SetFont("Arial","B",14);

$pdf->Cell(
0,
8,
utf8_decode("CARTA RESPONSIVA"),
0,
1,
"C"
);

$pdf->Ln(6);

$pdf->SetFont("Arial","",10);

$pdf->MultiCell(
0,
5,
utf8_decode("A QUIEN CORRESPONDA:")
);

$pdf->Ln(2);

if($esMobile){

    $texto="Por este conducto hago constar que con esta fecha recibo a mi cargo y custodia un DISPOSITIVO MOVIL con las siguientes características:";

}else{

    $texto="Por este conducto hago constar que con esta fecha recibo a mi cargo y custodia un dispositivo con las siguientes características:";

}

$pdf->MultiCell(
0,
5,
utf8_decode($texto)
);

$pdf->Ln(4);

/*=========================================
TABLA DE DATOS
=========================================*/

$pdf->SetFillColor(25,135,84);

$pdf->SetTextColor(255,255,255);

$pdf->SetFont("Arial","B",9);

$pdf->Cell(45,7,"Campo",1,0,"C",true);

$pdf->Cell(127,7,"Informacion",1,1,"C",true);

$pdf->SetFont("Arial","",9);

$pdf->SetTextColor(0,0,0);


$pdf->Cell(45,7,"Tipo",1,0);

$pdf->Cell(127,7,utf8_decode($asignacion["tipoDispositivo"]),1,1);

$pdf->Cell(45,7,"Marca",1,0);

$pdf->Cell(127,7,utf8_decode($asignacion["marca"]),1,1);

$pdf->Cell(45,7,"Serie",1,0);

$pdf->Cell(127,7,utf8_decode($asignacion["numDeSerie"]),1,1);

$pdf->Cell(45,7,"Estado",1,0);

$pdf->Cell(127,7,utf8_decode($asignacion["estado"]),1,1);

$pdf->Cell(45,7,"Fecha Inicio",1,0);

$pdf->Cell(
127,
7,
date("d/m/Y",strtotime($asignacion["fechainicio"])),
1,
1
);

$pdf->Cell(45,7,"Fecha Entrega",1,0);

if(!empty($asignacion["fechadeentregado"])){

    $fechaEntrega=date("d/m/Y",strtotime($asignacion["fechadeentregado"]));

}else{

    $fechaEntrega="Pendiente";

}

$pdf->Cell(
127,
7,
$fechaEntrega,
1,
1
);

$pdf->Ln(5);

/*=========================================
ESTATUS
=========================================*/

$asignado = " ";
$entregado = " ";

if($asignacion["idpreestatus"] == 1){
    $asignado = "X";
}

if($asignacion["idpreestatus"] == 2){
    $entregado = "X";
}

$pdf->SetFont("Arial","B",9);

$pdf->Cell(
45,
7,
"Estatus del equipo",
1,
0
);

$pdf->SetFont("Arial","",9);

$pdf->Cell(
127,
7,
utf8_decode("[ ".$asignado." ] Asignado      [ ".$entregado." ] Entregado"),
1,
1
);

$pdf->Ln(5);

/*=========================================
CARACTERÍSTICAS DEL DISPOSITIVO
=========================================*/

$pdf->SetFillColor(25,135,84);

$pdf->SetTextColor(255,255,255);

$pdf->SetFont("Arial","B",9);

$pdf->Cell(
172,
7,
utf8_decode("CARACTERÍSTICAS DEL DISPOSITIVO"),
1,
1,
"C",
true
);

$pdf->SetTextColor(0,0,0);

$pdf->SetFont("Arial","",9);

$pdf->MultiCell(
172,
5,
utf8_decode($asignacion["caracteristicas"]),
1
);

$pdf->Ln(5);

/*=========================================
TEXTO DE LA CARTA
=========================================*/

$pdf->SetFont("Arial","",9);

if($esMobile){

    $pdf->MultiCell(
        0,
        4,
utf8_decode(
"El dispositivo móvil junto con los accesorios es propiedad de la Empresa DIPASA, los cuales me han sido otorgados para uso exclusivo en actividades relacionadas con la Empresa.
Es mi responsabilidad, en caso de un excedente en el consumo de datos, algún fallo, pérdida, daño físico al Equipo, avisar al Departamento de Sistemas, si el evento tiene un costo por un mal uso, pagaré el importe generado.
El equipo de móvil que recibo a mi cargo y custodia invariablemente siempre estará a disposición de la empresa para cuando esta crea conveniente solicitar le sea devuelta, con los accesorios recibidos.
Aunado a lo anterior para el caso de pérdida o destrucción total por robo, extravío,  incendio, mojadura, etcétera, pagaré el precio de reposición/deducible que implique para la empresa.
"
)
    );

}else{

    $pdf->MultiCell(
        0,
        4,
utf8_decode(
"El dispositivo junto con los accesorios e INFORMACIÓN son propiedad de la Empresa DIPASA, los cuales me han sido otorgados para uso exclusivo en actividades relacionadas con la Empresa.
En caso de pérdida o filtración de información por mal uso, aceptaré las sanciones correspondientes, así como el pago de daños si estos se llegaran a generar.
Es mi responsabilidad, en caso de algún fallo, pérdida, daño físico al Equipo, avisar al Departamento de Sistemas, si el evento tiene un costo por un mal uso, pagaré el importe generado.
El dispositivo que recibo a mi cargo y custodia invariablemente siempre estará a disposición de la empresa para cuando esta crea conveniente solicitar le sea devuelta, con los accesorios recibidos.
Aunado a lo anterior para el caso de pérdida o destrucción total por robo, extravío,  incendio, mojadura, etcétera, pagaré el precio de reposición/deducible que implique para la empresa.
"
)
    );

}

$pdf->Ln(5);

/*=========================================
ATENTAMENTE
=========================================*/

$pdf->SetFont("Arial","B",10);

$pdf->Cell(
0,
5,
utf8_decode("ATENTAMENTE"),
0,
1,
"C"
);

$pdf->Ln(5);

/*=========================================
FIRMAS
=========================================*/

$pdf->SetFont("Arial","",10);

$pdf->Cell(80,6,"",0,0);
$pdf->Cell(12,6,"",0,0);
$pdf->Cell(80,6,"",0,1);

$pdf->Cell(
80,
5,
"__________________________________",
0,
0,
"C"
);

$pdf->Cell(12,5,"",0,0);

$pdf->Cell(
80,
5,
"__________________________________",
0,
1,
"C"
);

$pdf->SetFont("Arial","B",9);

$pdf->Cell(
80,
5,
utf8_decode("(Encargado) ".$encargado["encargado"]),
0,
0,
"C"
);

$pdf->Cell(12,5,"",0,0);

$pdf->Cell(
80,
5,
utf8_decode("(Usuario) ".$asignacion["empleado"]),
0,
1,
"C"
);

$pdf->SetFont("Arial","",8);

$pdf->Cell(
80,
4,
utf8_decode("Departamento de Sistemas"),
0,
0,
"C"
);

$pdf->Cell(12,4,"",0,0);

$pdf->Cell(
80,
4,
utf8_decode("Resguardante"),
0,
1,
"C"
);

/*=========================================
PIE
=========================================*/

$pdf->Ln(8);

$pdf->SetFont("Arial","I",7);

$pdf->MultiCell(
0,
3,
utf8_decode(
"Este documento acredita el resguardo del equipo propiedad de DIPASA y deberá conservarse para cualquier aclaración administrativa."
)
);

/*=========================================
GENERAR PDF
=========================================*/

$nombreArchivo = "Carta_Responsiva_Asignacion_".$asignacion["id"].".pdf";

$pdf->Output("I",$nombreArchivo);

exit;