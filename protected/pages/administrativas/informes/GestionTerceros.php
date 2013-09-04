<?php

/**
 * Description of Ingreso
 *
 * @author daniel
 */
class GestionTerceros extends TPage {

    public function OnInit($param) {
        parent::OnInit($param);
        if (!$this->IsPostBack) {
            
        }
    }

    public function CalcularSaldoCartera() {
        $LibGeneral = new LibGeneral();
    }

    /**
     *  Busca los pagos realizados por un cliente agrupadas por mes
     * */
    public function BuscarPagos() {

        $condicion = "";
        if ($this->TxtNroDocumento->Text != "" && is_numeric($this->TxtNroDocumento->Text)) {
            $condicion = " AND NrObligacion = " . $this->TxtNroDocumento->Text;
        }

        if ($this->TxtIdTercero->Text != "" && is_numeric($this->TxtIdTercero->Text)) {
            $strSql = "SELECT DATE_FORMAT(FhPago,'%M') AS FhPago, DATE_FORMAT(FhPago,'%m') AS IdRecibo, SUM(TotalPagado) as TotalPagado
                       FROM recibos
                       WHERE IdTercero = " . $this->TxtIdTercero->Text . "  $condicion AND FhPago > '2012-01-01' 
                       GROUP BY DATE_FORMAT(FhPago,_latin1'%M') ORDER BY IdRecibo ASC";
            $this->DGPagos->DataSource = RecibosRecord::finder()->findAllBySql($strSql);
            $this->DGPagos->dataBind();
        } else {
            $this->DGPagos->DataSource = "";
            $this->DGPagos->dataBind();
        }
    }

    /**
     * Define a cero el valor Visible="false"inicial de los totales por edad de cartera
     */
    public function Totales() {
        $this->setViewState('Total30', 0);
        $this->setViewState('Total60', 0);
        $this->setViewState('Total90', 0);
        $this->setViewState('Total120', 0);
        $this->setViewState('Total150', 0);
        $this->setViewState('Total180', 0);
        $this->setViewState('TotalMayor180', 0);
        $this->setViewState('TotalPorVencer', 0);
        $this->setViewState('TotalFacturas', 0);
        $this->setViewState('TotalAnticipos', 0);
    }

    /**
     * Busca las cuentas por cobrar segun parametros
     */
    public function Buscar() {
        $condicion = "";
        $terceros="";

        if ($this->TxtIdTercero->Text != "" && is_numeric($this->TxtIdTercero->Text)) {
            $terceros = TercerosRecord::ObtenerMorososTercero($this->TxtIdTercero->Text);
        }

        if (Count($terceros) != 0) {
            $this->DtGestionTerceros->DataSource = $terceros;
            $this->DtGestionTerceros->dataBind();
        }   
    }

    public function OcultarModal() {
        $this->mpnlGestion->Hide();
    }

    /**
     * Buscamos los terceros,
     * el metodo se ejecuta cada que el text del TxtIdTercero cambia.
     */
    public function BuscarTercero($sender, $param) {
        LibGeneral::BuscarTercero($sender, $param);
    }

    /**
     * Divide el texto ingresado cuando es seleccionado un suggestion del campo TxtIdTercero.
     * */
    public function CallBack() {
        LibGeneral::CallBack($this);

//        $this->CargarDatosTercero();
    }

    /**
     * Separa el nit(o identificacion) y el nombre de un tercero en el autocomplete
     * cuando el usuario selecciona una de las sugerencias que arroja el metodo "Buscar Tercero"
     * 
     * @param $object $objVista Es el objeto de la vista actual(pagina actual)
     * 
     * */
    public function CallBack2() {
        $Datos = explode("_", $this->TxtIdTercero2->Text);
        $this->TxtIdTercero2->Text = $Datos[1];
        $this->TxtNombre2->Text = $Datos[0];
    }

    /**
     * Consulta y carga en los labels la informacion del tercero consultado
     */
    public function CargarDatosTercero() {
        if ($this->TxtIdTercero->Text != "" && is_numeric($this->TxtIdTercero->Text)) {
            $arTercero = new TercerosRecord();
            $arTercero = TercerosRecord::finder()->findByPk($this->TxtIdTercero->Text);

            $this->LblContacto->Text = $arTercero->Contacto;
            $this->LblTelefono->Text = $arTercero->Telefono;
            $this->LblDireccion->Text = $arTercero->Direccion;

            $Saldo = TercerosRecord::CalcularSaldoTercero($this->TxtIdTercero->Text);
            $this->LblSaldoCartera->Text = number_format($Saldo, 2, ',', '.');
        }
    }

    /**
     * Evalua y calcula el total adeudados por el cliente segun la edad de la cartera
     * @param type $sender
     * @param type $param
     */
    public function OnItemDataBound($sender, $param) {
        if ($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem") {
            $Dias = LibGeneral::restaFechas(LibGeneral::ForFecha($param->Item->DataItem->FhVencimientoObligacion), date('Y/m/d'));
            $DiasFacturado = LibGeneral::restaFechas(LibGeneral::ForFecha($param->Item->DataItem->FhObligacion), date('Y/m/d'));

            if ($param->Item->DataItem->Saldo < 1 && $param->Item->DataItem->Saldo > -1)
                $param->Item->Visible = false;

            $param->Item->ClmDiasFacturado->LblDiasFacturado->Text = $DiasFacturado;

            $param->Item->ClmDiasVencidos->LblVencidos->Text = $Dias;


            if ($Dias >= 0 && $Dias <= 30) {
                $param->Item->Clm30->Lbl30->Text = number_format($param->Item->Clm30->Lbl30->Text + $param->Item->DataItem->Saldo, '2', ',', '.');
                $dblTotal30 = $this->getViewState('Total30') + $param->Item->DataItem->Saldo;
                $this->setViewState('Total30', $dblTotal30);

                if ($param->Item->DataItem->Saldo < 0)
                    $param->Item->Clm30->Lbl30->ForeColor = "Red";
            }

            elseif ($Dias >= 31 && $Dias <= 60) {
                $param->Item->Clm60->Lbl60->Text = number_format($param->Item->Clm60->Lbl60->Text + $param->Item->DataItem->Saldo, '2', ',', '.');
                $dblTotal60 = $this->getViewState('Total60') + $param->Item->DataItem->Saldo;
                $this->setViewState('Total60', $dblTotal60);

                if ($param->Item->DataItem->Saldo < 0)
                    $param->Item->Clm60->Lbl60->ForeColor = "Red";
            }

            elseif ($Dias >= 61 && $Dias <= 90) {
                $param->Item->Clm90->Lbl90->Text = number_format($param->Item->Clm90->Lbl90->Text + $param->Item->DataItem->Saldo, '2', ',', '.');
                $dblTotal90 = $this->getViewState('Total90') + $param->Item->DataItem->Saldo;
                $this->setViewState('Total90', $dblTotal90);

                if ($param->Item->DataItem->Saldo < 0)
                    $param->Item->Clm90->Lbl90->ForeColor = "Red";
            }

            elseif ($Dias >= 91 && $Dias <= 120) {
                $param->Item->Clm120->Lbl120->Text = number_format($param->Item->Clm120->Lbl120->Text + $param->Item->DataItem->Saldo, '2', ',', '.');
                $dblTotal120 = $this->getViewState('Total120') + $param->Item->DataItem->Saldo;
                $this->setViewState('Total120', $dblTotal120);

                if ($param->Item->DataItem->Saldo < 0)
                    $param->Item->Clm120->Lbl120->ForeColor = "Red";
            }

            elseif ($Dias >= 121 && $Dias <= 150) {
                $param->Item->Clm150->Lbl150->Text = number_format($param->Item->Clm150->Lbl150->Text + $param->Item->DataItem->Saldo, '2', ',', '.');
                $dblTotal150 = $this->getViewState('Total150') + $param->Item->DataItem->Saldo;
                $this->setViewState('Total150', $dblTotal150);

                if ($param->Item->DataItem->Saldo < 0)
                    $param->Item->Clm150->Lbl150->ForeColor = "Red";
            }

            elseif ($Dias >= 151 && $Dias <= 180) {
                $param->Item->Clm180->Lbl180->Text = number_format($param->Item->Clm180->Lbl180->Text + $param->Item->DataItem->Saldo, '2', ',', '.');
                $dblTotal180 = $this->getViewState('Total180') + $param->Item->DataItem->Saldo;
                $this->setViewState('Total180', $dblTotal180);

                if ($param->Item->DataItem->Saldo < 0)
                    $param->Item->Clm180->Lbl180->ForeColor = "Red";
            }
            elseif ($Dias > 180) {
                $param->Item->ClmMayor180->LblMayor180->Text = number_format($param->Item->ClmMayor180->LblMayor180->Text + $param->Item->DataItem->Saldo, '2', ',', '.');
                $dblTotalMayor180 = $this->getViewState('TotalMayor180') + $param->Item->DataItem->Saldo;
                $this->setViewState('TotalMayor180', $dblTotalMayor180);

                if ($param->Item->DataItem->Saldo < 0)
                    $param->Item->ClmMayor180->LblMayor180->ForeColor = "Red";
            }
            else {
                $param->Item->ClmPorVencer->LblPorVencer->Text = number_format($param->Item->ClmPorVencer->LblPorVencer->Text + $param->Item->DataItem->Saldo, '2', ',', '.');
                $dblTotalPorVencer = $this->getViewState('TotalPorVencer') + $param->Item->DataItem->Saldo;
                $this->setViewState('TotalPorVencer', $dblTotalPorVencer);

                if ($param->Item->DataItem->Saldo < 0)
                    $param->Item->ClmPorVencer->LblPorVencer->ForeColor = "Red";
            }
            $dblTtal = $this->getViewState('TotalFacturas') + $param->Item->DataItem->Saldo;
            $this->setViewState('TotalFacturas', $dblTtal);
        }

        // Generador del pie del datagrid.
        if ($param->Item->ItemType === 'Footer') {
            $param->Item->ClmPorVencer->LblTotalPorVencer->Text = number_format($this->getViewState('TotalPorVencer'), '2', ',', '.');
            $param->Item->Clm30->LblTotal30->Text = number_format($this->getViewState('Total30'), '2', ',', '.');
            $param->Item->Clm60->LblTotal60->Text = number_format($this->getViewState('Total60'), '2', ',', '.');
            $param->Item->Clm90->LblTotal90->Text = number_format($this->getViewState('Total90'), '2', ',', '.');
            $param->Item->Clm120->LblTotal120->Text = number_format($this->getViewState('Total120'), '2', ',', '.');
            $param->Item->Clm150->LblTotal150->Text = number_format($this->getViewState('Total150'), '2', ',', '.');
            $param->Item->Clm180->LblTotal180->Text = number_format($this->getViewState('Total180'), '2', ',', '.');
            $param->Item->ClmMayor180->LblTotalMayor180->Text = number_format($this->getViewState('TotalMayor180'), '2', ',', '.');

//            $this->LblTotalFacturas->Text = number_format($this->getViewState('TotalFacturas'), '2', ',', '.');
        }
    }

//
//    public function OnItemDataBoundAnticipos($sender, $param) {
//        if ($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem") {
//            $dblTotalAnticipo = $this->getViewState('TotalAnticipos') + $param->Item->DataItem->Valor;
//            $this->setViewState('TotalAnticipos', $dblTotalAnticipo);
//        }
//
//        if ($param->Item->ItemType === 'Footer') {
//            $this->LblTotalAnticipos->Text = number_format($this->getViewState('TotalAnticipos'), '2', ',', '.');
//            $this->LblTotal->Text = number_format(($this->getViewState('TotalFacturas') - $this->getViewState('TotalAnticipos')), '2', ',', '.');
//        }
//    }

    /**
     * Carga los recaudos aplicados en un recibo
     */
    public function CargarAnticipos() {
        $arAnticipos = RecibosRecaudosRecord::DevRecaudosTercero($this->TxtIdTercero->Text);

        if ($arAnticipos != false) {
            $this->DGAnticipos->DataSource = $arAnticipos;
            $this->DGAnticipos->dataBind();

            $this->FraMensaje->Visible = false;
        } else {
            if ($arAnticipos instanceof Exception)
                funciones::Mensaje("Se ha producido un error en la consulta.", 2, $this);
            else {
                $this->DGAnticipos->DataSource = "";
                $this->DGAnticipos->dataBind();
            }
        }
    }

    public function MostrarGestion($sender, $param) {
        $Gestion = GestionRecord::DevGestionXMoroso($sender->Parent->Parent->ClmCodObligacion->Text, $sender->Parent->Parent->ClmIdTercero->Text);

        $this->ADGGestion->DataSource = $Gestion;
        $this->ADGGestion->dataBind();

        $this->mpnlGestion->Show();
    }

    public function Imprimir() {
        ImprimirEstadoCuenta::general($this->TxtIdTercero->Text);
    }

    public function pagerCreated($sender, $param) {
        $param->Pager->Controls->insertAt(0, 'Page: ');
    }

    public function changePage($sender, $param) {
        $this->DGEstadosCuenta->CurrentPageIndex = $param->NewPageIndex; // recupera la página que ha sido seleccionada.

        $this->DGEstadosCuenta->DataSource = "";
        $this->DGEstadosCuenta->dataBind();
    }

}

?>
