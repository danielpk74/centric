<?php

/**
 * Crud de Campáñas,
 * crear, editar e inactivar las campañas de contacto.
 * */
class Ciudades extends TPage {

    public $Accion;

    public function OnInit($param) {
        parent::OnInit($param);

        $this->LlenarGrid();

        $this->CboDepartamentos->DataSource = DepartamentosRecord::finder()->findAll();
        $this->CboDepartamentos->dataBind();
    }

    /**
     * Carga el departamento seleccionado en el combo.
     * @param type $CodDepartamento 
     * */
    private function CargarDepartamento($CodDepartamento) {
        $this->CboDepartamentos->SelectedValue = $CodDepartamento;
    }

    /**
     * Llena el grid de datos personas
     * */
    private function LlenarGrid() {
        $Ciudad = new CiudadesRecord();
        $Ciudad = CiudadesRecord::finder()->With_Departamentos()->FindAll();

        $this->ADGCiudades->DataSource = $Ciudad;
        $this->ADGCiudades->dataBind();
    }

    /**
     * Abre ModalPanel que permite ingresar un nuevo registro de un persona,
     * @param <type> $sender
     * @param <type> $param 
     * */
    public function CrearCiudad($sender, $param) {
        $this->setViewState('Accion', '1');
        $this->mpnlCiudades->Show();
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
    public function EditarCiudad($sender, $param) {
        //$this->Accion = 2;
        $this->setViewState('Accion', '2');
        //$this->Identificacion = $param->Item->DataItem->Identificacion;
        $this->setViewState('CodCiudad', $param->Item->DataItem->CodCiudad);

        $this->mpnlCiudades->Show();

        $this->EnviarAccion();

        $Ciudad = new CiudadesRecord();
        $Ciudad = CiudadesRecord::ObtenerCiudad($param->Item->DataItem->CodCiudad);

        $this->CargarDepartamento($Ciudad->CodDepartamento);

        $this->TxtNombre->Text = $Ciudad->NmCiudad;
//        $this->TxtComentarios->Text = $Ciudad->Descripcion;

        $this->TxtNombre->Focus();
    }

    public function OcultarModal($sender, $param) {
        //echo$sender->Parent->ID->Hide();
        $this->mpnlCiudades->Hide();
    }

    private function EnviarAccion() {
        if ($this->getViewState('Accion') == 2)
            $this->ALblAccion->Text = "EDITAR CAMPAÑA";
        else
            $this->ALblAccion->Text = "INGRESAR CAMPAÑA";
    }

    /**
     * Almacena en la base de datos la informacion correspondiente a una ciudad.
     **/
    public function GuardarDatos() {

        // Si se esta editando la informacion de una persona.
        if ($this->getViewState('Accion') == 2) {
            $Ciudad = CiudadesRecord::ObtenerCiudad($this->getViewState('CodCiudad'));
//            $Ciudad->CodCiudad = $this->getViewState('CodCiudad');
        }

        $Ciudad = new CiudadesRecord();
        
        $Ciudad->NmCiudad = $this->TxtNombre->Text;
        $Ciudad->CodDepartamento = $this->CboDepartamentos->SelectedValue;
        
        $this->mpnlCiudades->Hide();
        
        $Transaccion = CiudadesRecord::Transaccion('CiudadesRecord');

        try {
            $Ciudad->save();
            $Transaccion->commit();
            LibGeneral::Completado($this->Page, 'Se han guardado los datos la ciudad <b>' . $this->TxtNombre->Text . '</b>');
        } catch (Exception $e) {
            $Transaccion->rollBack();
            LibGeneral::Error($this->Page, 'Se ha presentado un error durante la operacion, intentelo nuevamente.' . $e->getMessage() );
        }

        $this->mpnlCiudades->Hide();

        $this->LlenarGrid();
    }

    /**
     * Inactiva una campaña
     * @param type $sender
     * @param type $param 
     * */
    public function InactivarCiudad($sender, $param) {
        $Usuario = new CiudadesRecord();
        $Usuario = CamapanasRecord::finder()->findByPk($param->Item->DataItem->CodCiudad);
        $Usuario->Inactivo = 1;
        $Usuario->save();

        $this->LlenarGrid();
    }
    
    public function Buscar() {

        $Ciudad = new CiudadesRecord();

        if ($this->TxtNombreB->Text != "" && !$this->OptTodos->Checked) {
            $Ciudad = CiudadesRecord::ObtenerCiudadesXParametros($this->TxtNombreB->Text);
        }
        
        if($this->OptTodos->Checked) {
            $Ciudad = CiudadesRecord::finder()->findAll();
            $this->TxtNombreB->Text = "";    
        }
            
      if ($this->TxtNombreB->Text || $this->OptTodos->Checked) {        
        $LibGeneral = new LibGeneral();
        $LibGeneral->ResultadoBusqueda($this->Page,"ADGCiudades", $Ciudad);
      }
      else
            LibGeneral::Error($this->Page, 'Debe ingresar un parametro de busqueda.');
    }
    
    public function changePage($sender,$param) {
        $this->ADGCiudades->CurrentPageIndex=$param->NewPageIndex;// recupera la página que ha sido seleccionada y que será mostrada.
        $this->LlenarGrid();
    }

    public function pagerCreated($sender,$param) {
    	$param->Pager->Controls->insertAt(0,'Page: ');
    }   
}

?>
