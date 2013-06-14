<?php

/**
 * Crud de Campáñas,
 * crear, editar e inactivar las campañas de contacto.
 * */
class Campanas extends TPage {

    public $Accion;

    public function OnInit($param) {
        parent::OnInit($param);
        
        LibGeneral::EncabezadoCrud("Campañas","Crear Nueva Campaña",$this->Page);

        $this->LlenarGrid();
    }

    /**
     * Llena el grid de datos personas
     * */
    private function LlenarGrid() {
        $Campana = new CampanasRecord();
        $Campana = CampanasRecord::finder()->FindAll();

        $this->ADGCampanas->DataSource = $Campana;
        $this->ADGCampanas->dataBind();
    }
    
    /**
     * Abre ModalPanel que permite ingresar un nuevo registro de un persona,
     * @param <type> $sender
     * @param <type> $param 
     * */
    public function Crear($sender, $param) {
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
     * */
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

    public function OcultarModal() {
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
     * Almacena la informacion correspondiente a una campaña nueva.
     * */
    public function GuardarDatos() {
        $Campana = new CampanasRecord();

        // Si se esta editando la informacion de una persona.
        if ($this->getViewState('Accion') == 2) {
            $Campana = CampanasRecord::ObtenerCampana($this->getViewState('IdCampana'));
            $Campana->IdCampana = $this->getViewState('IdCampana');
        }

        $Campana->NombreCampana = $this->TxtNombre->Text;
        $Campana->Descripcion = $this->TxtComentarios->Text;
        $Campana->Usuario = $this->User->Name;
        $Campana->FhCreacion = date('Y-m-d H:i:s');
        $Campana->Inactiva = 0;
        $Campana->FhInactiva = '00-00-00 00:00:00';

        $Transaccion = CampanasRecord::Transaccion();

        try {
            $Campana->save();

            $Transaccion->commit();

            LibGeneral::Completado($this->Page, 'Se guardaron los datos de la campaña <b>' . $this->TxtNombre->Text . '</b>');

            $this->mpnlCampanas->Hide();

            $this->LlenarGrid();
            
            $this->OcultarModal($this);
        } catch (Exception $e) {

            LibGeneral::Error($this->Page, 'Se ha presentado un error durante la creacion de la campaña, intentelo nuevamente.');

            $Transaccion->rollBack();

            $this->LlenarGrid();
        }
    }

    /**
     * Actualiza el campo inactiva de una campaña a 1, esta no podra ser utilizada
     *  en la generacion de tareas para los usuarios.
     * */
    public function InactivarCampana($sender, $param) {
        $Campana = new CampanasRecord();
        $Campana = CampanasRecord::finder()->findByPk($param->Item->DataItem->IdCampana);

        $Campana->Inactiva = 1;

        $Transaccion = CampanasRecord::Transaccion();

        try {
            $Campana->save();

            LibGeneral::Completado($this->Page, 'Se ha inactivado la campaña <b>' . $param->Item->DataItem->NombreCampana . '</b>');

            $this->LlenarGrid();

            $Transaccion->commit();
        } catch (Exception $e) {
            $Transaccion->rollBack();

            LibGeneral::Error($this->Page, 'Se ha presentado un error durante la operacion.');

            $this->LlenarGrid();
        }
    }

    public function Buscar() {
        $Campana = new CampanasRecord();

        if ($this->TxtNombreB->Text != "" && !$this->OptTodos->Checked) {
            $Campana = CampanasRecord::ObtenerCampaniaXParametros($this->TxtNombreB->Text);
        }

        if ($this->OptTodos->Checked) {
            $Campana = CampanasRecord::finder()->findAll();
            $this->TxtNombreB->Text = "";
        }

        if ($this->TxtNombreB->Text || $this->OptTodos->Checked) {
            $LibGeneral = new LibGeneral();
            $LibGeneral->ResultadoBusqueda($this->Page, "ADGCampanas", $Campana);
        }
        else
             LibGeneral::Error($this->Page, 'Debe ingresar un parametro de busqueda.');
    }
    
    public function changePage($sender,$param) {
        $this->ADGCampanas->CurrentPageIndex=$param->NewPageIndex;// recupera la página que ha sido seleccionada y que será mostrada.
        $this->LlenarGrid();
    }

    public function pagerCreated($sender,$param) {
    	$param->Pager->Controls->insertAt(0,'Page: ');
    }
}

?>
