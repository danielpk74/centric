<?php

/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * */
class Usuarios extends TPage {

    public $Accion;

    public function OnInit($param) {
        parent::OnInit($param);

        LibGeneral::EncabezadoCrud("Usuarios","Crear Nuevo Usuario",$this->Page);
        
        $this->LlenarGrid();
    }

    /**
     * Llena el grid de datos personas
     * */
    private function LlenarGrid() {
        $Usuarios = new UsuariosRecord();
        $Usuarios = UsuariosRecord::finder()->FindAll();

        $this->ADGUsuarios->DataSource = $Usuarios;
        $this->ADGUsuarios->dataBind();
    }

    /**
     * Abre ModalPanel que permite ingresar un nuevo registro de un persona,
     * @param <type> $sender
     * @param <type> $param 
     */
    public function Crear($sender, $param) {
        $this->setViewState('Accion', '1');
        $this->mpnlPersonas->Show();
        $this->EnviarAccion();

        $this->TxtUsuario->Enabled = true;
        $this->TxtUsuario->Text = "";
        $this->TxtUsuario->Focus();
    }

    /**
     * Abre ModalPanel con la posiblidad de editar la informacion de una persona
     * @param <type> $sender
     * @param <type> $param 
     * */
    public function EditarUsuario($sender, $param) {
        //$this->Accion = 2;
        $this->setViewState('Accion', '2');
        //$this->Identificacion = $param->Item->DataItem->Identificacion;
        $this->setViewState('Usuario', $param->Item->DataItem->Usuario);

        $this->mpnlPersonas->Show();

        $this->EnviarAccion();

        $Usuario = UsuariosRecord::ObtenerUsuario($param->Item->DataItem->Usuario);

        $this->TxtNombres->Text = $Usuario->Nombres;
        $this->TxtApellidos->Text = $Usuario->Apellidos;
        $this->TxtUsuario->Text = $Usuario->Usuario;
        $this->CboTipo->SelectedValue = $Usuario->Tipo;
        $this->TxtTelefono1->Text = $Usuario->Telefono1;
        $this->TxtTelefono2->Text = $Usuario->Telefono2;
        $this->TxtDireccion->Text = $Usuario->Direccion;

        $this->TxtUsuario->Enabled = false;
    }

    public function OcultarModal($sender, $param) {
        //echo$sender->Parent->ID->Hide();
        $this->mpnlPersonas->Hide();
    }

    private function EnviarAccion() {
        if ($this->getViewState('Accion') == 2)
            $this->ALblAccion->Text = "EDITAR USUARIO";
        else
            $this->ALblAccion->Text = "INGRESAR USUARIO";
    }

    public function GuardarDatos() {
        $Usuario = new UsuariosRecord();

        // Si se esta editando la informacion de una persona.
        if ($this->getViewState('Accion') == 2) {
            $Usuario = UsuariosRecord::ObtenerUsuario($this->getViewState('Usuario'));
//            $Usuario->Usuario = $this->getViewState('Usuario');
        }
        // si se esta ingresando una nueva
        else
            $Usuario->Usuario = $this->TxtUsuario->Text;

        $Usuario->Contrasena = md5($this->TxtContrasena->Text);
        $Usuario->Nombres = $this->TxtNombres->Text;
        $Usuario->Apellidos = $this->TxtApellidos->Text;
        $Usuario->Tipo = $this->CboTipo->SelectedValue;
        $Usuario->Telefono1 = $this->TxtTelefono1->Text;
        $Usuario->Telefono2 = $this->TxtTelefono2->Text;
        $Usuario->Direccion = $this->TxtDireccion->Text;

        $LibGeneral = new LibGeneral();

        $Transaccion = UsuariosRecord::Transaccion();

        try {
            $Usuario->save();
            $LibGeneral->Completado($this->Page, 'Se han guardado los datos de el usuario <b>' . $this->getViewState('Usuario') . "</b>.");
            $Transaccion->commit();
        } catch (Exception $e) {
            $LibGeneral->Error($this->Page, "Se produjo un error en la operacion, vuelva a intentarlo. " . $e->getMessage());
            $Transaccion->rollBack();
        }

        $this->mpnlPersonas->Hide();

        $this->LlenarGrid();
    }

    public function InactivarUsuario($sender, $param) {
        $Usuario = new UsuariosRecord();
        $Usuario = UsuariosRecord::finder()->findByPk($param->Item->DataItem->Usuario);

        if ($Usuario->Inactivo != 1) {
            $Usuario->Inactivo = 1;
            try {
                $Usuario->save();
                LibGeneral::Completado($this->Page, 'Se ha inactivado el usuario <b>' . $param->Item->DataItem->Usuario . "</b>.");
            } catch (Exception $e) {
                LibGeneral::Error($this->Page, "Se produjo un error en la operacion, vuelva a intentarlo.");
            }
        }
        else
            LibGeneral::Error($this->Page, "El usuario ya se encuentra inactivo.");


        $this->LlenarGrid();
    }

    public function Buscar() {

        $Usuario = new UsuariosRecord();

        if ($this->TxtIdentificacionBuscar->Text != "" && !$this->OptTodos->Checked) {
            $Usuario = UsuariosRecord::finder()->findAllBy_Usuario($this->TxtIdentificacionBuscar->Text);
        }

        if ($this->TxtNombre->Text != "" && !$this->OptTodos->Checked) {
            $Usuario = UsuariosRecord::ObtenerUsuariosXParametros($this->TxtNombre->Text);
        }

        if ($this->OptTodos->Checked) {
            $Usuario = UsuariosRecord::finder()->findAll();
            $this->TxtNombre->Text = "";
            $this->TxtIdentificacionBuscar->Text = "";
        }

        if ($this->TxtNombre->Text != "" || $this->TxtIdentificacionBuscar->Text != "" || $this->OptTodos->Checked) {
            $LibGeneral = new LibGeneral();
            $LibGeneral->ResultadoBusqueda($this->Page, "ADGUsuarios", $Usuario);
        }
        else
            LibGeneral::Error($this->Page, 'Debe ingresar un parametro de busqueda.');
    }
    
    public function changePage($sender,$param) {
        $this->ADGUsuarios->CurrentPageIndex=$param->NewPageIndex;// recupera la página que ha sido seleccionada y que será mostrada.
        $this->LlenarGrid();
    }

    public function pagerCreated($sender,$param) {
    	$param->Pager->Controls->insertAt(0,'Page: ');
    }

}

?>
