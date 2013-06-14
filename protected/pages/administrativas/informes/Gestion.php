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
class Gestion extends TPage {

    private static $cartera;
    
    public function OnInit($param) {
        parent::OnInit($param);
        if (!$this->IsPostBack) {
//            $this->CboGestores->DataSource = UsuariosRecord::ObtenerGestores();
//            $this->CboGestores->dataBind();
            
            $Gestion= new GestionRecord();
            
            $Gestion= GestionRecord::ObtenerCarteraTotal();
                    
            
            
            self::$cartera=$Gestion[0]->Cartera;
            
            $this->CarteraSum->Text=self::$cartera;
            $this->CboClientes->DataSource = TercerosRecord::ObtenerClientes();
            $this->CboClientes->dataBind();
        }
    }

    public function GenerarInforme() {
//        if ($this->CboClientes->SelectedValue != "") {
//            $this->DGObligaciones->DataSource = ObligacionesRecord::ObligacionesTercero($this->CboClientes->SelectedValue);
//            $this->DGObligaciones->dataBind();
//        } else {
//            LibGeneral::Error($this, "Debe seleccionar un cliente.");
//        }
        
        if ($this->CboClientes->SelectedValue != "") {
            $this->DGGestion->DataSource = GestionRecord::InformeGestion();
            $this->DGGestion->dataBind();
        } else {
            LibGeneral::Error($this, "Debe seleccionar un cliente.");
        }
    }
    
    public function OnItemDataBound($sender,$param)
    {
        if($param->Item->ItemType=="Item" || $param->Item->ItemType=="AlternatingItem")
        {
            $param->Item->ClmParticipacion->Text= number_format($param->Item->ClmDeuda->Text/$this->CarteraSum->Text,2);
        }
    }
    
    public function changePage($sender, $param) {

        $this->DGCotizacionesDet->CurrentPageIndex = $param->NewPageIndex; // recupera la página que ha sido seleccionada.

        $this->DGCotizacionesDet->DataSource = $thi->GenerarInforme();
        $this->DGCotizacionesDet->dataBind();
    }

}

?>
