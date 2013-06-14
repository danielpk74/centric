<?php

/**
 * Description of VerInformacion
 *
 * @author daniel
 * */
class VerInformacion extends TPage {

    public $_Tareas;

    public function OnInit($param) {
        parent::OnInit($param);

        if (!$this->IsPostBack) {
            $Tarea = new TareasRecord();
            $Tarea = TareasRecord::finder()->FindByPk($this->Request['Ct']);

            $this->setViewState('CodigoTarea',$this->Request['Ct']);

            $this->setViewState('CodigoObligacion', $Tarea->CodObligacion);

            $Obligacion = new ObligacionesRecord();
            $Obligacion = ObligacionesRecord::DevObligacionPk($Tarea->CodObligacion);

            $this->CargarInfoTercero($Obligacion->IdTercero);

            // Cargamos todas las tareas de este moroso
            $this->_Tareas = TareasRecord::TareasMoroso($Obligacion->IdTercero);

            $i = 0;

            foreach ($this->_Tareas as $Detalle) {
                $Datos[$i] = $Detalle->CodTarea;

                if ($Detalle->CodTarea == $Tarea->CodTarea)
                    $Indice = $i;

                $i++;
            }

            // El array con las tareas
            $this->setViewState('Datos', $Datos);
            $this->setViewState('RegMaximo', Count($this->_Tareas) - 1);

            // El numero perteneciente al indice del array: Ejm $Datos[8]
            $this->setViewState('i', $i);

            // El Indice dentro del array donde se encuentra el item actual
            $this->setViewState('Indice', $Indice);

            if ($this->getViewState('Indice') == 0)
                $this->LBtnAtras->Enabled = false;

            if ($this->getViewState('Indice') == $this->getViewState('RegMaximo'))
                $this->LBtnAdelante->Enabled = false;

            $this->LblNroTareas->Text = (Count($this->_Tareas) - 1) . " tareas mas por Gestionar";
            $this->LblNroTareas->ToolTip = "Otras Obligaciones de este mismo moroso por gestionar";

            $this->CargarAbonos($Obligacion->IdTercero);

            $this->CargarGestion();

            $this->CboConceptosGestion->DataSource = ConceptosCobranzaRecord::getConceptosGestion(1);
            $this->CboConceptosGestion->dataBind();

            $this->CboConceptosCompromisos->DataSource = SubConceptosCobranzaRecord::getSubConceptosGestion();
            $this->CboConceptosCompromisos->dataBind();
        }
    }

    /**
     * Almacena un nuevo registro de Gestion.
     * */
    public function GuardarGestion() {
        $LibGeneral = new LibGeneral();
        $Transaccion = GestionRecord::Transaccion();
        $Error = false;

        try {
            $Tarea = new TareasRecord();
            $Tarea = TareasRecord::finder()->FindByPk($this->getViewState('CodigoTarea'));

            $Gestion = new GestionRecord();
            $Gestion->CodConceptoGestion = $this->CboConceptosGestion->SelectedValue;
            $Gestion->FechaGestion = date('Y-m-d H:i:s');
            $Gestion->CodObligacion = $Tarea->CodObligacion;
            $Gestion->CodTarea = $this->getViewState('CodigoTarea');
            $Gestion->CodTercero = $this->LblIdTercero->Text;
            $Gestion->Observaciones = $this->TxtComentariosGestion->Text;
            $Gestion->Usuario = $this->User->Name;
            $Gestion->Telefono = $this->TxtTelefono->Text;
            $Gestion->save();

            ObligacionesRecord::ActualizarFechaUltimaGestion($Tarea->CodObligacion);

            if ($this->DtpFhPagoCompromiso->Text != "") {

                $Compromiso = new CompromisosPagoRecord();
                $Compromiso->NroObligacion = $this->LblCodObligacion->Text;
                $Compromiso->CodGestion = $Gestion->CodGestion;
                $Compromiso->CodTarea = $this->getViewState('CodigoTarea');

                $Compromiso->CodObligacion = $Tarea->CodObligacion;
                $Compromiso->FhCompromiso = date('Y-m-d');
                $Compromiso->FhPagoCompromiso = $this->DtpFhPagoCompromiso->Text;
                $Compromiso->CodConceptoCompromiso = $this->CboConceptosCompromisos->SelectedValue;
                $Compromiso->CodConceptoResultCobro = NULL;
                $Compromiso->IdTercero = $this->LblIdTercero->Text;
                $Compromiso->Valor = $this->TxtValorCompromiso->Text;
                $Compromiso->Cuotas = $this->TxtCuotasCompromiso->Text;
                $Compromiso->Usuario = $this->User->Name;
                $Compromiso->Observaciones = $this->TxtComentariosCompromisos->Text;
                if (!$Compromiso->save()) {
                    $Transaccion->rollback();
                    $Error = true;
                }
            }

            if (!$Error) {
                $Transaccion->commit();
//                TareasRecord::CerrarTarea($this->Request['Ct'], $this->User->Name);
            }
            LibGeneral::OcultarModal($this, $this->mpnlGestion->ID);

            LibGeneral::Completado($this, "Se ha guardado un registro de Gestión.");

            $this->CargarGestion();
        } catch (Exception $e) {
            LibGeneral::OcultarModal($this, $this->mpnlGestion->ID);
            LibGeneral::Error($this, $e->getMessage());
            $Transaccion->rollback();
        }
    }

    /**
     * Abre ModalPanel que permite ingresar un nuevo registro de un persona,
     * @param objeto $sender
     * @param objeto $param 
     * */
    public function NuevoCompromiso($sender, $param) {
        $this->mpnlCompromisos->Show();
    }

    /**
     * Abre ModalPanel que permite ingresar  un concepto y una observacion 
     * de un cierre de un compromiso de pago
     * @param objeto $sender
     * @param objeto $param 
     * */
    public function CerrarCompromiso($sender, $param) {
        $this->mpnlCerrarCompromiso->Show();

        $Conceptos = ConceptosCobranzaRecord::getConceptosCierreCompromiso();
        $this->CboConceptosCierreCompromisos->DataSource = $Conceptos;
        $this->CboConceptosCierreCompromisos->dataBind();

        $this->LblCodCompromiso->Text = $sender->Parent->Parent->ClmCodCompromiso->Text;
    }

    /**
     * 
     * */
    public function GuardarCierreCompromiso($sender, $param) {
        CompromisosPagoRecord::CerrarCompromiso($this->LblCodCompromiso->Text, $this->CboConceptosCierreCompromisos->SelectedValue, $this->TxtComentariosCierreCompromiso->Text, date('Y-m-d H:i:s'), $this->User->Name, $this);
    }

    /**
     * Oculta los modal panel
     * @param type $sender
     * @param type $param 
     * */
    public function OcultarModal($sender, $param) {
        LibGeneral::OcultarModal($this, $sender->CustomData);
    }

    /**
     * Carga la informacion del tercero.
     * @param type $IdTercero 
     * */
    private function CargarInfoTercero($IdTercero) {
        $Tercero = TercerosRecord::ObtenerTercero(1, $IdTercero);

        $this->LblIdTercero->Text = $Tercero->Identificacion;
        $this->LblIdTerceroPertenece->Text = $Tercero->IdTerceroPertenece . " - "; //. $Tercero->TerceroPertenece->NombreCorto;
        $this->LblNombreCorto->Text = $Tercero->NombreCorto;
        $this->LblTelefono->Text = $Tercero->Telefono;
        $this->LblTelefono2->Text = $Tercero->Telefono2;
        $this->LblDireccion->Text = $Tercero->Direccion;
        $this->LblEmail->Text = $Tercero->Email;
        $this->LblFormaPago->Text = $Tercero->IdFormaPago;
//        $this->LblSaldo->Text = $Tercero->SaldoCartera;
    }

    /**
     * Carga el historial de pago de un tercero
     * */
    private function CargarAbonos($IdTercero) {
//        $Abonos = AbonosRecord::AbonosTercero($IdTercero, $this->Request['Co']);
        $Abonos = AbonosRecord::AbonosTercero($IdTercero, $this->getViewState('CodigoObligacion'));

        $this->ADGAbonos->DataSource = $Abonos;
        $this->ADGAbonos->dataBind();
    }

//    /**
//     * Carga los acuerdos de pago que ha tenido el tercero
//     * @param type $IdTercero 
//     * */
//    private function CargarCompromisos($IdTercero) {
//        $Acuerdos = CompromisosPagoRecord::CompromisosTercero($IdTerero);
//        $this->ADGCompromisos->DataSource = $Acuerdos;
//    }

    /**
     * Carga al datagrid los registros de la gestion 
     * realizada a una obligacion. 
     * */
    public function CargarGestion() {

        if ($this->getViewState('CodigoTarea') == "")
            $CodigoTarea = $this->Request['Ct'];
        else
            $CodigoTarea = $this->getViewState('CodigoTarea');

        $Tarea = new TareasRecord();
        $Tarea = TareasRecord::finder()->FindByPk($CodigoTarea);

        $Gestion = GestionRecord::DevGestionXMoroso($Tarea->CodObligacion, $this->LblIdTercero->Text);

        $Obligacion = new ObligacionesRecord();
        $Obligacion = ObligacionesRecord::finder()->findByPk($Tarea->CodObligacion);

        $this->LblFechaUltimaGestion->Text = "Ultima Gestión: " . $Obligacion->FechaUltimaGestion;
        $this->LblCodObligacion->Text = $Obligacion->NrObligacion;

        $this->ADGGestion->DataSource = $Gestion;
        $this->ADGGestion->dataBind();
    }

    public function OnItemDataBound($sender, $param) {
        if ($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem") {
            $Compromiso = new CompromisosPagoRecord();
            $Compromiso = CompromisosPagoRecord::DevCompromisos($param->Item->Data->CodGestion);

            $param->Item->ClmGridCompromisos->ADGCompromisos->DataSource = $Compromiso;
            $param->Item->ClmGridCompromisos->ADGCompromisos->dataBind();
        }
    }

    /**
     * Carga los datos del detalle siguiente al actual, segun la ubicacion 
     * dentro de este(el detalle actual) en el array de datos
     * */
    public function Adelante() {
        $Indice = $this->getViewState('Indice') + 1;

        $Datos = $this->getViewState('Datos');

        $this->setViewState('Indice', $Indice);
        $this->setViewState('CodigoTarea', $Datos[$Indice]);

        $this->CargarGestion();

        $this->LBtnAtras->Enabled = true;

        if ($Indice == $this->getViewState('RegMaximo'))
            $this->LBtnAdelante->Enabled = false;
    }

    /**
     * Carga los datos del detalle anterior al actual, segun la ubicacion 
     * dentro de este(el detalle actual) en el array de datos
     * */
    public function Atras() {
        $Indice = $this->getViewState('Indice') - 1;

        $Datos = $this->getViewState('Datos');

        $this->setViewState('Indice', $Indice);
        $this->setViewState('CodigoTarea', $Datos[$Indice]);
        $this->CargarGestion($Datos[$Indice]);

        if ($this->getViewState('Indice') == 0)
            $this->LBtnAtras->Enabled = false;

        if ($this->LBtnAdelante->Enabled == false)
            $this->LBtnAdelante->Enabled = true;
    }

}

?>
