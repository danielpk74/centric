<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IngresoUsuario
 *
 * @author daniel
 */
prado::using("Application.librerias.fpdf.fpdf");

class InfIngresoUsuario {

// Cabecera de página
    function Header() {
        // Logo
        $this->Image('/opt/lampp/htdocs/centric/img/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30, 10, 'Title', 1, 0, 'C');
        // Salto de línea
        $this->Ln(20);
    }

    public function Generar() {
        $pdf = new FPDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times', '', 12);
        for ($i = 1; $i <= 40; $i++)
            $pdf->Cell(0, 10, 'Imprimiendo línea número ' . $i, 0, 1);
        $pdf->Output('Imprimiendo línea número.pdf', 'D');
    }

// Pie de página
    function Footer() {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

}

?>
