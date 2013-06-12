<?php

/**
 * Crud de Campáñas,
 * crear, editar e inactivar las campañas de contacto.
 **/
class Campanas extends TPage {

    public $Accion;

    public function OnInit($param) {
        parent::OnInit($param);

        $this->LlenarGrid();
    }

    /**
     * Llena el grid de datos personas
     **/
    private function LlenarGrid() {
        $Campana = new CampanasRecord();
        $Campana = CampanasRecord::finder()->FindAll();

        $this->ADGCampanas->DataSource = $Campana;
        $this->ADGCampanas->dataBind();
    }

    public function pagerCreated($sender, $param) {
        
    }

    /**
     * Abre ModalPanel que permite ingresar un nuevo registro de un persona,
     * @param <type> $sender
     * @param <type> $param 
     **/
    public function CrearCampana($sender, $param) {
        $this->setViewState('Accion', '1');
        $this->mpnlCampanas->Show();
        $this->EnviarAccion();

        $this->TxtNombre->Enabled = true;
        $this->TxtNombre->Text = "";
        $this->TxtComentarios->Text = "";
        $this->TxtNombre->Focus();
    }

    /**
     * Abre ModalPanel con la posiblidad de editar la informacion de una persona
     * @param <type> $sender
     * @param <type> $param 
     **/
    public function EditarCampana($sender, $param) {
        //$this->Accion = 2;
        $this->setViewState('Accion', '2');
        //$this->Identificacion = $param->Item->DataItem->Identificacion;
        $this->setViewState('IdCampana', $param->Item->DataItem->IdCampana);

        $this->mpnlCampanas->Show();

        $this->EnviarAccion();

        $Campana = CampanasRecord::ObtenerCampana($param->Item->DataItem->IdCampana);

        $this->TxtNombre->Text = $Campana->NombreCampana;
        $this->TxtComentarios->Text = $Campana->Descripcion;
        
        $this->TxtNombre->Focus();
    }

    public function OcultarModal($sender, $param) {
        //echo$sender->Parent->ID->Hide();
        $this->mpnlCampanas->Hide();
    }

    private function EnviarAccion() {
        if ($this->getViewState('Accion') == 2)
            $this->ALblAccion->Text = "EDITAR CAMPAÑA";
        else
            $this->ALblAccion->Text = "INGRESAR CAMPAÑA";
    }
    
    /**
     * 
     **/
    public function GuardarDatos() {
        $Campana = new CampanasRecord();

        // Si se esta editando la informacion de una persona.
        if ($this->getViewState('Accion') == 2) {
            $Campana = CampanasRecord::ObtenerCampana($this->getViewState('IdCampana'));
            $Campana->IdCampana = $this->getViewState('IdCampana');
        }

        $Campana->NombreCampana = $this->TxtNombre->Text;
        $Campana->Descripcion = $this->TxtComentarios->Text;

        $Campana->save();

        $this->mpnlCampanas->Hide();

        $this->LlenarGrid();
    }

    /**
     * Inactiva una campaña
     * @param type $sender
     * @param type $param 
     **/
    public function InactivarCampana($sender, $param) {
        $Usuario = new CampanasRecord();
        $Usuario = CamapanasRecord::finder()->findByPk($param->Item->DataItem->IdCampana);
        $Usuario->Inactivo = 1;
        $Usuario->save();

        $this->LlenarGrid();
    }

    public function Buscar() {

        $Criteria = new TActiveRecordCriteria;

        if ($this->OptNombre->Checked) {
            $Criteria->Condition = "NombreCampana LIKE '%:Nombres%'";
            $Criteria->Parameters[':Nombres'] = $this->TxtNombreB->Text;
        }

        $Criteria->OrdersBy['NombreCampana'] = 'Desc';
        //$Criteria->Limit = 10;
        //$Criteria->Offset = 20;        

        $Campana = new CampanasRecord();
        $Campana = CampanasRecord::finder()->FindAll($Criteria);

        $this->ADGCampanas->DataSource = $Campana;
        $this->ADGCampanas->dataBind();
    }
}
?>
