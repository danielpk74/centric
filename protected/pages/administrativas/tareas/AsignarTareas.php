<?php

/**
 * Permite asignar las tareas de gestion a un usuario especifico o aleatoriamente,
 * pudiendose elegir las tareas por montos, por empresas, acuerdos de pago, acuerdos incumplidos
 * */
class AsignarTareas extends TPage {

    public $Identificaciones = array();

    public function OnInit($param) {
        if (!$this->IsPostBack) {
            $this->CargarUsuarios();
            
            $this->ObtenerEmpresas();
            $this->CargarRangos();
            $this->CargarCampanias();
            $this->TareasUsuarios();
            $this->CargarObligaciones();
        }
    }

    public function CargarCampanias() {
        $this->CboCampanas->DataSource = CampanasRecord::ObtenerCampanas();
        $this->CboCampanas->dataBind();
    }

    /**
     * Obtiene todas las empresas clientes a quien se gestiona las carteras
     * */
    private function ObtenerEmpresas() {
        $this->CboEmpresas->DataSource = TercerosRecord::DistinTerceros();
        $this->CboEmpresas->dataBind();
    }

    public function FiltrarObligaciones() {
        $Obligaciones = ObligacionesRecord::FiltrosObligaciones($this->Page);

        if (Count($Obligaciones) != 0) {
            $this->ADGObligaciones->DataSource = $Obligaciones;
            $this->ADGObligaciones->dataBind();
        } else {
            $LibGeneral = new LibGeneral();
            $LibGeneral->Error($this->Page, "No se encontraron registros con los parametros de busqueda especificados");

            $this->ADGObligaciones->DataSource = "";
            $this->ADGObligaciones->dataBind();
        }
    }

    /**
     * Genera un array de rangos de busqueda y los carga a un 
     * */
    private function CargarRangos() {
        $Rangos = array('' => '(Con Saldos)', '<' => 'Menores que', '<=' => 'Menores Iguales que', '=' => 'Iguales que', '>' => 'Mayores que', '>=' => 'Mayores Iguales que');

        $this->CboRangos->DataSource = $Rangos;
        $this->CboRangos->dataBind();
    }

    /**
     * Carga los gestores al TRepeater
     * */
    public function TareasUsuarios() {
        $this->DRTareasUsuarios->DataSource = UsuariosRecord::ObtenerGestores();
        $this->DRTareasUsuarios->dataBind();
    }

    /**
     * Carga las tareas asignadas a cada uno de los usuarios.
     * @param type $sender
     * @param type $param 
     * */
    public function VerTareasAsignadas($sender, $param) {
        $this->TareasUsuarios();

        if ($sender->Text != "Ocultar Tareas")
            $sender->Text = "Ocultar Tareas";
        else
            $sender->Text = "Ver Tareas";
    }

    /**
     * 
     * */
    public function OnItemCreated($sender, $param) {
//        if ($param->Item->ItemType === 'Item' || $param->Item->ItemType === 'AlternatingItem') {
////
//            if ($param->Item->DataItem->Usuario) {
//                $Tareas = new TareasRecord();
//                $Tareas = TareasRecord::finder()->With_Obligacion()->findAllBy_Usuario($param->Item->DataItem->Usuario);
//
//                if (Count($Tareas) > 0) {
////                  $this->getClientScript()->registerEndScript($page->getClientID(), "Completado('$Completado');");
//                    $param->Item->ADGObligaciones->DataSource = $Tareas;
//                    $param->Item->ADGObligaciones->dataBind();
//                }
//            }
//        }
    }

    /**
     * Carga de usuarios
     * */
    private function CargarUsuarios() {
        $Usuarios = UsuariosRecord::Usuarios();

        $this->CboUsuarios->DataSource = $Usuarios;
        $this->CboUsuarios->dataBind();
    }

    /**
     * Carga el grid de obligaciones
     * */
    public function CargarObligaciones($sender=null, $param=null) {
        if ($sender != null) {
            $this->setViewState('Identificaciones', $sender->Parent->Parent->ClmIdTercero->Text);
        }

        $Obligaciones = new ObligacionesRecord();
        $Obligaciones = ObligacionesRecord::Obligaciones();
        
//        foreach ($Obligaciones as $Obligacion ) {
//            
//        }

        $this->ADGObligaciones->DataSource = $Obligaciones;
        $this->ADGObligaciones->dataBind();
    }

    public function changePage($sender, $param) {
        $this->ADGObligaciones->CurrentPageIndex = $param->NewPageIndex; // Recupera la página que ha sido seleccionada y que será mostrada.
        $this->CargarObligaciones();
    }

    public function pagerCreated($sender, $param) {
        $param->Pager->Controls->insertAt(0, 'page: ');
    }

    public function changePageDg($sender, $param) {
        $this->ADGObligaciones->CurrentPageIndex = $param->NewPageIndex; // Recupera la página que ha sido seleccionada y que será mostrada.
        $this->CargarObligaciones();
    }

    public function pagerCreatedDg($sender, $param) {
        $param->Pager->Controls->insertAt(0, 'page: ');
    }

    /**
     * Asigna tareas Por usuario seleccionado
     * */
    public function AsignarTareaUsuarios() {

        $LibGeneral = new LibGeneral();

        if ($this->CboUsuarios->SelectedValue == '') {
            $LibGeneral->Error($this->Page, 'Debe seleccionar un Usuario');
            return false;
        }

        $Errores = 0;
        $TareasAsignadas = 0;

        // Recorre el datagrid de tareas buscando las tareas seleccionadas, cuando esta seleccionada
        // Asigna la tarea al usuario en el cbo.
        for ($i = 0; $i < $this->ADGObligaciones->ItemCount; $i++) {
            if ($this->ADGObligaciones->Items[$i]->ClmAsignar->ChkAsignar->Checked) {
                try {
                    if (TareasRecord::AsignarTarea($this->ADGObligaciones->Items[$i]->ClmCodObligacion->Text, $this->ADGObligaciones->Items[$i]->ClmIdTercero->Text, $this->CboCampanas->SelectedValue, $this->CboUsuarios->SelectedValue, $this->User->Name, $this->ADGObligaciones->Items[$i]->ClmObservaciones->TxtObservaciones->Text, ''))
                        $TareasAsignadas++;
                } catch (Exception $e) {
                    $Errores++;

                    $LibGeneral->Error($this->Page, $e->getMessage());
                    return false;
                }
            }
        }

        if ($Errores == 0) {
            $LibGeneral->Completado($this->Page, $TareasAsignadas . ' tareas fueron asignadas correctamente.');
        }
        else
            $LibGeneral->Error($this->Page, 'Algunas tareas no pudieron ser asignadas.');

        $this->CargarObligaciones();
    }

    /**
     * Asigna tareas aleatoriamente.
     * */
    public function AsignarTareasAleatorias() {
        $LibGeneral = new LibGeneral();

        // Buscar obligaciones que no estan asignadas
        $Obligaciones = new ObligacionesRecord();
        $Obligaciones = ObligacionesRecord::Obligaciones();

        // si hay obligacines
        if (Count($Obligaciones) == 0) {
            $LibGeneral->Error($this->Page, 'No hay tareas pendientes para asignar.');
            return false;
        }

        $Gestores = new UsuariosRecord();
        $Gestores = UsuariosRecord::ObtenerGestores();

        $TotalObligaciones = Count($Obligaciones);
        $TotalGestores = Count($Gestores);

        try {
            $OblXUsuario = Round($TotalObligaciones / $TotalGestores);
        } catch (Exception $e) {
            $LibGeneral->Error($this->Page, 'Se ha presentado un error de tipo' . $e->getCode() . ', comuniquese con el administrador del sistema. ');
            return false;
        }

        $i = 0;
        $Errores = 0;
        foreach ($Gestores as $Gestor) {
            $criteria = new TActiveRecordCriteria;
            $criteria->Condition = 'Asignada = :Asignada';
            $criteria->Parameters[':Asignada'] = 0;
            $criteria->Limit = $OblXUsuario;

            $Obligaciones = new ObligacionesRecord();
            $Obligaciones = ObligacionesRecord::finder()->findAll($criteria);

            foreach ($Obligaciones as $Obligacion) {
                if ($this->AsignarTarea($Obligacion->CodObligacion, $Obligaciones->IdTercero, $Gestor->Usuario, 'Asignada Aleatoriamente', 1) == false)
                    $Errores++;

                $this->TareasUsuarios();
            }
        }

        if ($Errores == 0)
            $LibGeneral->Completado($this->Page, 'Las tareas fueron asignadas correctamente.');
    }

    public function OnItemDataBound($sender, $param) {
        $Dias = 0;
        $item = $param->Item;
        if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {

            if ($this->getViewState('Identificaciones') == '') {
                $arrayIdentificaciones = array('');
            } else {
                $arrayIdentificaciones[] = $this->getViewState('Identificaciones');
            }

            if (!in_array($this->ADGObligaciones->DataKeys[$item->ItemIndex], $arrayIdentificaciones)) {
                if (!in_array($this->ADGObligaciones->DataKeys[$item->ItemIndex], $this->Identificaciones)) {

                    $this->Identificaciones[] = $this->ADGObligaciones->DataKeys[$item->ItemIndex];

                    $arObligaciones = ObligacionesRecord::ObligacionesXMorosoXCliente($this->ADGObligaciones->DataKeys[$item->ItemIndex], TercerosRecord::DevIdTerceroPertenece($this->ADGObligaciones->DataKeys[$item->ItemIndex]));

                    if (Count($arObligaciones) > 1) {
                        $param->Item->FindControl('LnkbVerMas')->Visible = true;
                    }
                } else {//&& $this->getViewState('IdentificacionK') != $this->ADGObligaciones->DataKeys[$item->ItemIndex] ; $this->ADGObligaciones->DataKeys[$item->ItemIndex]
                    if (in_array($this->ADGObligaciones->DataKeys[$item->ItemIndex], $this->Identificaciones)) {
                        $item->Visible = false;
                    }
                }
            }
            $DiasVencido = LibGeneral::restaFechas(LibGeneral::ForFecha($param->Item->DataItem->FhVencimientoObligacion), date('Y/m/d'));
            $param->Item->ClmDiasVencidos->LblVencidos->Text = $DiasVencido;

            $DiasFacturado = LibGeneral::restaFechas(LibGeneral::ForFecha($param->Item->DataItem->FhObligacion), date('Y/m/d'));
            $param->Item->ClmDiasFacturado->LblDiasFacturado->Text = $DiasFacturado;

//            $Obligaciones = new ObligacionesRecord();
//            $Obligaciones = ObligacionesRecord::ObligacionesTercero($item->DataItem->IdTercero);
//
//            if (Count($Obligaciones) > 1) {
//                $param->Item->ClmNrObligacion->LnkbVerMas->Visible = true;
//            }
        }
    }

    public function AgregarItem($sender, $param) {
        if ($param->Item->ItemType == 'Item')
            $ItemType = 'AlternatingItem';
        else
            $ItemType = 'Item';

        $sender->Parent->CreateItem($param->Item->ItemIndex);
    }

}

?>
