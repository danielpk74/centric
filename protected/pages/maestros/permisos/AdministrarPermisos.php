<?php

/**
 * Crud de Campáñas,
 * crear, editar e inactivar las campañas de contacto.
 * */
class AdministrarPermisos extends TPage {

    public $Accion;

    public function OnInit($param) {
        parent::OnInit($param);

        $this->LlenarGrid();
    }

    /**
     * Llena el grid de datos personas
     * */
    private function LlenarGrid() {
        $Departamento = new DepartamentosRecord();
        $Departamento = DepartamentosRecord::finder()->FindAll();

        $this->ADGDepartamentos->DataSource = $Departamento;
        $this->ADGDepartamentos->dataBind();
    }


    /**
     * Abre ModalPanel que permite ingresar un nuevo registro de un persona,
     * @param <type> $sender
     * @param <type> $param 
     * */
    public function CrearDepartamento($sender, $param) {
        $this->setViewState('Accion', '1');
        $this->mpnlDepartamentos->Show();
        $this->EnviarAccion();

        $this->TxtNombre->Enabled = true;
        $this->TxtNombre->Text = "";
        $this->TxtNombre->Focus();
    }

    /**
     * Abre ModalPanel con la posiblidad de editar la informacion de una persona
     * @param <type> $sender
     * @param <type> $param 
     * */
    public function EditarDepartamento($sender, $param) {
        //$this->Accion = 2;
        $this->setViewState('Accion', '2');
        //$this->Identificacion = $param->Item->DataItem->Identificacion;
        $this->setViewState('CodDepartamento', $param->Item->DataItem->CodDepartamento);

        $this->mpnlDepartamentos->Show();

        $this->EnviarAccion();

        $Departamento = DepartamentosRecord::finder()->findByPk($param->Item->DataItem->CodDepartamento);

        $this->TxtNombre->Text = $Departamento->NmDepartamento;
        $this->TxtNombre->Focus();
    }

    public function OcultarModal($sender, $param) {
        //echo$sender->Parent->ID->Hide();
        $this->mpnlDepartamentos->Hide();
    }

    private function EnviarAccion() {
        if ($this->getViewState('Accion') == 2)
            $this->ALblAccion->Text = "EDITAR DEPARTAMENTO";
        else
            $this->ALblAccion->Text = "INGRESAR DEPARTAMENTO";
    }

    /**
     * 
     * */
    public function GuardarDatos() {
        $Departamento = new DepartamentosRecord();

        // Si se esta editando la informacion de una persona.
        if ($this->getViewState('Accion') == 2) {
            $Departamento = DepartamentosRecord::finder()->findByPk($this->getViewState('CodDepartamento'));
//            $Departamento->CodDepartamento = $this->getViewState('CodDepartamento');
        }

        $Departamento->NmDepartamento = $this->TxtNombre->Text;

        $Transaccion = DepartamentosRecord::Transaccion();

        try {
            $Departamento->save();
            $this->mpnlDepartamentos->Hide();
            LibGeneral::Completado($this->Page, 'Se guardaron los datos del departamento <b>' . $this->TxtNombre->Text . '</b>');
        } catch (Exception $e) {
            LibGeneral::Error($this->Page, 'Se ha presentado un error durante la creacion de la campaña, intentelo nuevamente.');
            $Transaccion->rollBack();
        }
        $this->LlenarGrid();
    }

    /**
     * Inactiva una campaña
     * @param type $sender
     * @param type $param 
     * */
    public function InactivarDepartamento($sender, $param) {
        $Departamento = new DepartamentosRecord();
        $Departamento = DepartamentosRecord::finder()->findByPk($param->Item->DataItem->CodDepartamento);
        $Departamento->Inactivo = 1;
        $Transaccion = DepartamentosRecord::Transaccion();

        try {
            $Departamento->save();
            $Transaccion->commit();
            LibGeneral::Completado($this->Page, 'Se ha inactivado la campaña <b>' . $param->Item->DataItem->NmDepartamento . '</b>');
            $this->LlenarGrid();
        } catch (Exception $e) {
            $Transaccion->rollBack();

            LibGeneral::Error($this->Page, 'Se ha presentado un error durante la operacion.');

            $this->LlenarGrid();
        }
    }

    public function Buscar() {

        $Departamento = new DepartamentosRecord();

        if ($this->TxtNombreB->Text != "" && !$this->OptTodos->Checked) {
            $Departamento = DepartamentosRecord::ObtenerDepartamentosXParametros($this->TxtNombreB->Text);
        }

        if ($this->OptTodos->Checked) {
            $Departamento = DepartamentosRecord::finder()->findAll();
            $this->TxtNombreB->Text = "";
        }

        if ($this->TxtNombreB->Text || $this->OptTodos->Checked) {
            $LibGeneral = new LibGeneral();
            $LibGeneral->ResultadoBusqueda($this->Page, "ADGDepartamentos", $Departamento);
        }
        else
            LibGeneral::Error($this->Page, 'Debe ingresar un parametro de busqueda.');
    }

    public function changePage($sender, $param) {
        $this->ADGDepartamentos->CurrentPageIndex = $param->NewPageIndex; // recupera la página que ha sido seleccionada y que será mostrada.
        $this->LlenarGrid();
    }

    public function pagerCreated($sender, $param) {
        $param->Pager->Controls->insertAt(0, 'Page: ');
    }

}

?>
