<?php

/**
 * Description of VerTareas
 * @author daniel
 * */
class VerTareas extends TPage {

    public $Identificaciones = array();

    public function OnInit($param) {
        parent::OnInit($param);

        if (!$this->IsPostBack) {
            $this->CargarTareas(null, null);
        }
    }

    /**
     * Carga las tareas asignadas a un usuario
     * */
    public function CargarTareas($sender, $param) {
        if ($sender != null) {
            $this->setViewState('Identificaciones', $sender->Parent->Parent->ClmIdTercero->Text);
        }

        $sql = "SELECT tareas.CodTarea, obligaciones.NrObligacion,obligaciones.ValorFactura, obligaciones.ValorReporte, obligaciones.ValorCuota, obligaciones.IdTercero,
                        terceros.NombreCorto,   terceros.Telefono, terceros.Telefono2, obligaciones.FhObligacion, obligaciones.FhReporte,
                        obligaciones.FhUltimoPago, obligaciones.Saldo, obligaciones.FechaUltimaGestion, obligaciones.FechaUltimaGestion as Aleatoria, pertenece.NombreCorto as Cliente
                FROM tareas LEFT JOIN obligaciones ON obligaciones.CodObligacion = tareas.CodObligacion LEFT JOIN
                     terceros ON obligaciones.IdTercero = terceros.Identificacion
                     LEFT JOIN terceros AS pertenece ON pertenece.Identificacion = terceros.IdTerceroPertenece
                WHERE tareas.Usuario = '" . $this->User->Name . "' AND Cerrada = 0
                ORDER BY Aleatoria ASC";

        $Tareas = new TareasRecord();
        $Tareas = TareasRecord::finder('TareasExtRecord')->findAllBySql($sql);

        if (Count($Tareas) > 0) {
            $this->ADGObligaciones->DataSource = $Tareas;
            $this->ADGObligaciones->dataBind();
        }
    }

    public function OnItemDataBound($sender, $param) {
        $item = $param->Item;
        if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {

            if ($this->getViewState('Identificaciones') == '') {
                $arrayIdentificaciones = array('');
            } else {
                $arrayIdentificaciones[] = $this->getViewState('Identificaciones');
            }

            if (!in_array($this->ADGObligaciones->DataKeys[$item->ItemIndex], $arrayIdentificaciones)) {
                $tempArray = $this->Identificaciones;
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
        }
    }

    public function changePage($sender, $param) {
        $this->ADGObligaciones->CurrentPageIndex = $param->NewPageIndex; // recupera la página que ha sido seleccionada y que será mostrada.
        $this->CargarTareas(null,null);
    }

    public function pagerCreated($sender, $param) {
        $param->Pager->Controls->insertAt(0, 'Page: ');
    }

}
?>
