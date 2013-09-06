<?php

class Terceros extends TPage {

    public $Accion;

    public function OnInit($param) {
        parent::OnInit($param);

        LibGeneral::EncabezadoCrud("Terceros", "Crear Nuevo Tercero", $this->Page);

        if (!$this->IsPostBack)
            $this->LlenarGrid();
    }

    private function LlenarGrid() {
        $Terceros = new TercerosRecord();
        $Terceros = TercerosRecord::finder()->With_Ciudades()->FindAll();

        $this->ADGTerceros->DataSource = $Terceros;
        $this->ADGTerceros->dataBind();
    }

    private function LlenarGridTerceros() {
        $Terceros = new TercerosRecord();
        $Terceros = TercerosRecord::ObtenerClientes();

        $this->ADGTerceros->DataSource = $Terceros;
        $this->ADGTerceros->dataBind();
    }

    private function LlenarGridMorosos() {
        $Terceros = new TercerosRecord();
        $Terceros = TercerosRecord::ObtenerMorosos();

        $this->ADGTerceros->DataSource = $Terceros;
        $this->ADGTerceros->dataBind();
    }

    private function LlenarDDLTpId() {
        $TpId = new TipoIdentificacionRecord();
        $TpId = TipoIdentificacionRecord::finder()->FindAll();

        $this->CboTipoIdentificacion->DataSource = $TpId;
        $this->CboTipoIdentificacion->DataTextField = "TpIdentificacion";
        $this->CboTipoIdentificacion->DataValueField = "Id";
        $this->CboTipoIdentificacion->dataBind();
    }

    private function LlenarDDLCiudades() {
        $Ciudades = new CiudadesRecord();
        $Ciudades = CiudadesRecord::finder()->FindAll();

        $this->CboCiudades->DataSource = $Ciudades;
        $this->CboCiudades->DataTextField = "NmCiudad";
        $this->CboCiudades->DataValueField = "CodCiudad";

        $this->CboCiudades->dataBind();
    }

    public function OcultarModal($sender, $param) {
        //echo$sender->Parent->ID->Hide();
        $this->mpnlTerceros->Hide();
    }

    public function Crear($sender, $param) {
        $this->setViewState('Accion', '1');
        $this->mpnlTerceros->Show();
        $this->EnviarAccion();
    }

    public function EditarTercero($sender, $param) {

        $this->setViewState('Accion', '2');

        $Identificacion = $sender->Datakeys[$param->Item->ItemIndex];

        $this->setViewState('Tercero', $Identificacion);

        $this->mpnlTerceros->Show();

        $this->EnviarAccion();
//
        $Tercero = TercerosRecord::ObtenerTercero('1', $Identificacion);
//
        $this->TxtIdentificacion->Text = $Tercero->Identificacion;
        $this->TxtIdTerceroPertenece->Text = $Tercero->IdTerceroPertenece;
        $this->CboTipoIdentificacion->SelectedValue = $Tercero->TpIdentificacion;
        $this->TxtDigitoVerificacion->Text = $Tercero->DigitoVerificacion;
        $this->TxtNombreCorto->Text = $Tercero->NombreCorto;
        $this->TxtNombreExtendido->Text = $Tercero->NombreExtendido;
        $this->TxtNombre1->Text = $Tercero->Nombre;
        $this->TxtNombre2->Text = $Tercero->Nombre2;
        $this->TxtApellido1->Text = $Tercero->Apellido1;
        $this->TxtApellido2->Text = $Tercero->Apellido2;
        $this->TxtDireccion->Text = $Tercero->Direccion;
        $this->TxtTelefono->Text = $Tercero->Telefono;
        $this->TxtTelefono2->Text = $Tercero->Telefono2;
        $this->TxtFax->Text = $Tercero->Fax;
        $this->TxtEmail->Text = $Tercero->Email;
        $this->CboCiudades->SelectedValue = $Tercero->CodCiudad;
        $this->TxtContacto->Text = $Tercero->Contacto;
        $this->TxtCargoContacto->Text = $Tercero->CargoContacto;
        $this->TxtIdFormaPago->Text = $Tercero->IdFormaPago;
        $this->CboInactivo->SelectedValue = $Tercero->Inactivo;
        $this->TxtComentarios->Text = $Tercero->Comentarios;
//        $this->TxtSaldoCartera->Text = $Tercero->SaldoCartera;

        $this->TxtIdentificacion->Enabled = false;
    }

    /**
     * Buscamos los terceros,
     * el metodo se ejecuta cada que el text del TxtIdTercero cambia.
     * */
    public function BuscarTercero($sender, $param) {
        LibGeneral::BuscarTercero($sender, $param);
    }

    public function GuardarDatos() {
        $Tercero = new TercerosRecord();

        // Si se esta editando la informacion de una persona.
        if ($this->getViewState('Accion') == 2) {
            $Tercero = TercerosRecord::ObtenerTercero('1', $this->getViewState('Tercero'));
            $Tercero->Identificacion = $this->getViewState('Tercero');
        }// si se esta ingresando una nueva
        else {
            $Tercero->Identificacion = $this->TxtIdentificacion->Text;
        }

        $Tercero->FhCreacion = date('Y-m-d H:i:s');
//        $this->TxtIdentificacion->Text = $Tercero->Identificacion;
        if ($this->TxtIdTerceroPertenece->Text == '') {
            $Tercero->IdTerceroPertenece = NULL;
        } else {
            $Tercero->IdTerceroPertenece = $this->TxtIdTerceroPertenece->Text;
        }
        $Tercero->TpIdentificacion = $this->CboTipoIdentificacion->SelectedValue;
        $Tercero->DigitoVerificacion = $this->TxtDigitoVerificacion->Text;
        $Tercero->NombreCorto = $this->TxtNombreCorto->Text;
        $Tercero->NombreExtendido = $this->TxtNombreExtendido->Text;
        $Tercero->Nombre = $this->TxtNombre1->Text;
        $Tercero->Nombre2 = $this->TxtNombre2->Text;
        $Tercero->Apellido1 = $this->TxtApellido1->Text;
        $Tercero->Apellido2 = $this->TxtApellido2->Text;
        $Tercero->Direccion = $this->TxtDireccion->Text;
        $Tercero->Telefono = $this->TxtTelefono->Text;
        $Tercero->Telefono2 = $this->TxtTelefono2->Text;
        $Tercero->Fax = $this->TxtFax->Text;
        $Tercero->Email = $this->TxtEmail->Text;
        $Tercero->CodCiudad = $this->CboCiudades->SelectedValue;
        $Tercero->Contacto = $this->TxtContacto->Text;
        $Tercero->CargoContacto = $this->TxtCargoContacto->Text;
        $Tercero->IdFormaPago = $this->TxtIdFormaPago->Text;
        $Tercero->Inactivo = $this->CboInactivo->SelectedValue;
        $Tercero->Comentarios = $this->TxtComentarios->Text;
//            $Tercero->SaldoCartera = $this->TxtSaldoCartera->Text;
        $this->mpnlTerceros->Hide();

        try {
            $Tercero->save();
            $this->mpnlTerceros->Hide();
            LibGeneral::Completado($this->Page, 'Se han guardado los datos de <b>' . $this->TxtNombreCorto->Text . '</b>');
            $this->LlenarGrid();
        } catch (Exception $e) {
            LibGeneral::Error($this->Page, 'Se ha presentado un error durante la operacion, intentelo nuevamente.' . $e->getMessage());
        }
    }

    public function InactivarTercero($sender, $param) {
        $Tercero = new TercerosRecord();
        $Tercero = TercerosRecord::finder()->findByPk($param->Item->DataItem->Identificacion);

        if ($Tercero->Inactivo != 1) {
            $Tercero->Inactivo = 1;
            try {
                $Tercero->save();
                LibGeneral::Completado($this->Page, 'Se ha inactivado el usuario <b>' . $param->Item->DataItem->Usuario . "</b>.");
            } catch (Exception $e) {
                LibGeneral::Error($this->Page, "Se produjo un error en la operacion, vuelva a intentarlo.");
            }
        }
        else
            LibGeneral::Error($this->Page, "El usuario ya se encuentra inactivo.");

        $this->LlenarGrid();
    }

    private function EnviarAccion() {
        $this->LlenarDDLTpId();
        $this->LlenarDDLCiudades();

        if ($this->getViewState('Accion') == 2)
            $this->ALblAccion->Text = "EDITAR TERCERO";
        else {
            $this->ALblAccion->Text = "INGRESAR TERCERO";
            $this->TxtIdentificacion->Text = "";
            $this->TxtIdentificacion->Enabled = true;
            $this->TxtIdentificacion->focus();
        }
    }

    public function Buscar() {

        $Tercero = new TercerosRecord();

        if ($this->TxtIdentificacionBuscar->Text != "" && !$this->OptTodos->Checked) {
            $Tercero = TercerosRecord::finder()->findAllBy_Identificacion($this->TxtIdentificacionBuscar->Text);
        }

        if ($this->TxtNombres->Text != "" && !$this->OptTodos->Checked) {
            $Tercero = TercerosRecord::ObtenerTerceroXParametros($this->TxtNombres->Text);
        }

        if ($this->OptTodos->Checked) {
            $Tercero = TercerosRecord::finder()->findAll();
            $this->TxtNombres->Text = "";
            $this->TxtIdentificacionBuscar->Text = "";
        }

        if ($this->TxtNombres->Text != "" || $this->TxtIdentificacionBuscar->Text != "" || $this->OptTodos->Checked) {
            $LibGeneral = new LibGeneral();
            $LibGeneral->ResultadoBusqueda($this->Page, "ADGTerceros", $Tercero);
        }else
            LibGeneral::Error($this->Page, 'Debe ingresar un parametro de busqueda.');
    }

    public function changePage($sender, $param) {
        $this->ADGTerceros->CurrentPageIndex = $param->NewPageIndex; // recupera la página que ha sido seleccionada y que será mostrada.
        $this->LlenarGrid();
    }

    public function pagerCreated($sender, $param) {
        $param->Pager->Controls->insertAt(0, 'Page: ');
    }

    public function CamiarFiltro($sender, $param) {

        if ($this->ChkTerceros->Checked) {
            $this->LlenarGridTerceros();
        }

        if ($this->ChkMorosos->Checked) {
            $this->LlenarGridMorosos();
        }
//
        if ($this->ChkAmbos->Checked) {
            $this->LlenarGrid();
        }
    }

}

?>
