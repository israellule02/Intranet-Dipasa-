<?php

session_start();

require_once("../assets/fpdf/fpdf.php");

date_default_timezone_set("America/Mexico_City");



if(!isset($_SESSION["datosPDF"])){

    die("No existen datos para generar el PDF.");

}




$datos = $_SESSION["datosPDF"];




if(!is_array($datos) || empty($datos)){

    die("No existen registros para generar el PDF.");

}



$pdf = new FPDF(
    "L",
    "mm",
    "Letter"
);

$pdf->SetMargins(
    10,
    10,
    10
);

$pdf->SetAutoPageBreak(
    true,
    10
);

$pdf->AliasNbPages();

$pdf->AddPage();




$pdf->SetTitle(
    utf8_decode("Reporte de consulta")
);

$pdf->SetAuthor(
    "DIPASA"
);




$pdf->Image(
    "../assets/img/logotipo.png",
    10,
    10,
    28
);




$pdf->SetXY(
    43,
    10
);

$pdf->SetFont(
    "Arial",
    "B",
    15
);

$pdf->Cell(
    180,
    7,
    utf8_decode("DIPASA"),
    0,
    1
);

$pdf->SetX(43);

$pdf->SetFont(
    "Arial",
    "",
    10
);

$pdf->Cell(
    180,
    6,
    utf8_decode("Reporte de consulta"),
    0,
    1
);



$pdf->SetXY(
    235,
    10
);

$pdf->SetFont(
    "Arial",
    "",
    9
);

$pdf->Cell(
    40,
    6,
    utf8_decode(
        "Fecha: ".date("d/m/Y")
    ),
    0,
    1,
    "R"
);




$pdf->Ln(15);

$pdf->SetFont(
    "Arial",
    "B",
    14
);

$pdf->Cell(
    0,
    8,
    utf8_decode("REPORTE DE CONSULTA"),
    0,
    1,
    "C"
);

$pdf->Ln(5);




$columnas = array_keys(
    $datos[0]
);

$cantidadColumnas = count(
    $columnas
);



$anchoTotal = 257;




$anchoColumna =
    $anchoTotal / $cantidadColumnas;




function calcularAlturaTexto(
    $pdf,
    $texto,
    $ancho,
    $altoLinea
){

    $texto = (string)$texto;

   

    $texto = utf8_decode($texto);

  

    $palabras = explode(
        " ",
        $texto
    );

    $lineaActual = "";

    $numeroLineas = 1;


    foreach($palabras as $palabra){

        $prueba = $lineaActual;

        if($prueba != ""){

            $prueba .= " ";

        }

        $prueba .= $palabra;



        $anchoTexto =
            $pdf->GetStringWidth($prueba);


        if($anchoTexto > ($ancho - 2)){

            $numeroLineas++;

            $lineaActual = $palabra;

        }else{

            $lineaActual = $prueba;

        }

    }



    if($numeroLineas < 1){

        $numeroLineas = 1;

    }


    return $numeroLineas * $altoLinea;

}



$pdf->SetFillColor(
    25,
    135,
    84
);

$pdf->SetTextColor(
    255,
    255,
    255
);

$pdf->SetFont(
    "Arial",
    "B",
    8
);

foreach($columnas as $columna){

    $titulo = ucfirst(
        str_replace(
            "_",
            " ",
            $columna
        )
    );

    $pdf->Cell(
        $anchoColumna,
        8,
        utf8_decode($titulo),
        1,
        0,
        "C",
        true
    );

}

$pdf->Ln();




$pdf->SetTextColor(
    0,
    0,
    0
);

$pdf->SetFont(
    "Arial",
    "",
    7
);



foreach($datos as $fila){




    $alturaFila = 7;


    foreach($columnas as $columna){

        $valor = $fila[$columna] ?? "";


        if($valor === null){

            $valor = "";

        }


        $alturaTexto =
            calcularAlturaTexto(
                $pdf,
                $valor,
                $anchoColumna,
                4
            );


        if($alturaTexto > $alturaFila){

            $alturaFila = $alturaTexto;

        }

    }


  

    if($alturaFila > 35){

        $alturaFila = 35;

    }



    if(
        $pdf->GetY() + $alturaFila >
        $pdf->GetPageHeight() - 15
    ){

        $pdf->AddPage();

    

        $pdf->SetFillColor(
            25,
            135,
            84
        );

        $pdf->SetTextColor(
            255,
            255,
            255
        );

        $pdf->SetFont(
            "Arial",
            "B",
            8
        );


        foreach($columnas as $columna){

            $titulo = ucfirst(
                str_replace(
                    "_",
                    " ",
                    $columna
                )
            );

            $pdf->Cell(
                $anchoColumna,
                8,
                utf8_decode($titulo),
                1,
                0,
                "C",
                true
            );

        }

        $pdf->Ln();


        $pdf->SetTextColor(
            0,
            0,
            0
        );

        $pdf->SetFont(
            "Arial",
            "",
            7
        );

    }




    $xInicial = $pdf->GetX();

    $yInicial = $pdf->GetY();


  

    foreach($columnas as $columna){

        $valor = $fila[$columna] ?? "";


        if($valor === null){

            $valor = "";

        }


        $valor = utf8_decode(
            (string)$valor
        );



        $x = $pdf->GetX();


    

        $pdf->Rect(
            $x,
            $yInicial,
            $anchoColumna,
            $alturaFila
        );



        $pdf->SetXY(
            $x + 1,
            $yInicial + 1
        );


        $pdf->MultiCell(
            $anchoColumna - 2,
            4,
            $valor,
            0,
            "L"
        );


     

        $pdf->SetXY(
            $x + $anchoColumna,
            $yInicial
        );

    }


  
    $pdf->SetXY(
        $xInicial,
        $yInicial + $alturaFila
    );

}




$pdf->Ln(5);

$pdf->SetFont(
    "Arial",
    "I",
    7
);

$pdf->Cell(
    0,
    4,
    utf8_decode(
        "Documento generado por INFRANETV2 - DIPASA"
    ),
    0,
    1,
    "C"
);




$nombreArchivo =
    "Reporte_".
    date("Ymd_His").
    ".pdf";




$pdf->Output(
    "I",
    $nombreArchivo
);

exit;