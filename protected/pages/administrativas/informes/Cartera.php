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

class Cartera extends TPage {

    public function OnInit($param) {
        parent::OnInit($param);
        if (!$this->IsPostBack) {
            $this->CboClientes->DataSource = TercerosRecord::ObtenerClientes();
            $this->CboClientes->dataBind();
        }
    }

    public function GenerarInforme() {
        if ($this->CboClientes->SelectedValue != "") {
            $this->DGObligaciones->DataSource = ObligacionesRecord::ObligacionesTercero($this->CboClientes->SelectedValue);
            $this->DGObligaciones->dataBind();
        } else {
            LibGeneral::Error($this, "Debe seleccionar un cliente.");
        }
    }
    
    public function changePage($sender, $param) {

        $this->DGCotizacionesDet->CurrentPageIndex = $param->NewPageIndex; // recupera la página que ha sido seleccionada.

        $this->DGCotizacionesDet->DataSource = $thi->GenerarInforme();
        $this->DGCotizacionesDet->dataBind();
    }

}

?>
