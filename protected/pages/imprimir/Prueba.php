<?php

prado::using("Application.librerias.fpdf.fpdf");

class Prueba extends TPage{

    public function OnInit($param) {

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(40, 10, '¡Hola, Mundo!');
        $pdf->Output('PRUEBA.pdf','D');
    }

}

?>
