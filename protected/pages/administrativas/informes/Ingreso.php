<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ingreso
 *
 * @author daniel
 */
prado::using("Application.pages.imprimir.InfIngresoUsuario");

//prado::using("Application.librerias.fpdf.fpdf");

//require('fpdf.php');

class Ingreso extends TPage {

    public function OnInit($param) {
        parent::OnInit($param);
        if (!$this->IsPostBack) {

            $this->CboUsuarios->DataSource = UsuariosRecord::Usuarios();
            $this->CboUsuarios->dataBind();
        }
    }

    public function GenerarInforme() {
        if ($this->CboUsuarios->SelectedValue != "") {
            
//            $PDF = new InfIngresoUsuario();
//            $PDF->Generar();
            $this->Response->Redirect ('?page=imprimir.Prueba');
            
            // Creación del objeto de la clase heredada
            // Creación del objeto de la clase heredada
//            $pdf = new InfIngresoUsuario();
//            $pdf->AliasNbPages();
//            $pdf->AddPage();
//            $pdf->SetFont('Times', '', 12);
//            for ($i = 1; $i <= 40; $i++)
//                $pdf->Cell(0, 10, 'Imprimiendo línea número ' . $i, 0, 1);
//            $pdf->Output('Imprimiendo línea número.pdf','D');
        } else {
            
        }
    }

}

?>
