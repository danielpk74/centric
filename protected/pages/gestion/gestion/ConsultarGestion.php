<?php

/**
 * Description of Ingreso
 *
 * @author daniel
 */
class ConsultarGestion extends TPage {

    public function OnInit($param) {
        parent::OnInit($param);
        if (!$this->IsPostBack) {
            $this->TxtIdTercero2->Focus();

            $this->CargarUsuarios();
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

        // busqueda por el numero de factura
        if ($this->TxtNroDocumento->Text != "" && is_numeric($this->TxtNroDocumento->Text)) {
            $condicion .= " AND obligaciones.NrObligacion = " . $this->TxtNroDocumento->Text;
        }

        // Busqueda por la identificacion del moroso
        if ($this->TxtIdTercero2->Text != "" && is_numeric($this->TxtIdTercero2->Text)) {
            $condicion .= " AND terceros.Identificacion = " . $this->TxtIdTercero2->Text;
        }

        // Busqueda por la fecha de gestion
        if ($this->DtpFhDesde->Text != "" && $this->DtpFhHasta->Text != "") {
            $condicion .= " AND gestion.FechaGestion >= '" . $this->DtpFhDesde->Text . " 00:00:00' AND gestion.FechaGestion <= '" . $this->DtpFhHasta->Text . " 23:59:59'";
        }
        
        // Busqueda por la fecha de gestion
        if ($this->CboUsuarios->SelectedValue != "") {
            $condicion .= " AND gestion.Usuario = '" . $this->CboUsuarios->SelectedValue ."'";
        }

        $sql = "SELECT obligaciones.*, CONCAT(terceros.Nombre,'',terceros.Nombre2) as Nombres, CONCAT(terceros.Apellido1,'',terceros.Apellido2) as Apellidos, terceros.NombreCorto
                     FROM ((terceros INNER JOIN obligaciones ON terceros.Identificacion = obligaciones.IdTercero) LEFT JOIN gestion on obligaciones.CodObligacion = gestion.CodObligacion)                     
                     WHERE 1 = 1";

        $Obligaciones = new ObligacionesRecord();
        $Obligaciones = ObligacionesRecord::finder('ObligacionesExtRecord')->findAllBySql($sql . " " . $condicion);

        if (Count($Obligaciones) == 0) {
            $this->PnlError->Visible = true;
            $this->LblError->Text = "No se encontraron registros con el filtro especificado";
        } else {
            $this->ADGObligaciones->DataSource = $Obligaciones;
            $this->ADGObligaciones->dataBind();

            $this->PnlError->Visible = false;
            $this->LblError->Text = "";
            $this->NoRegistros->Text = "";
        }

//        $this->BuscarVentas();
//        $this->BuscarPagos();
//        $this->Totales();
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

        $this->CargarDatosTercero();
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

//            $this->LblContacto->Text = $arTercero->Contacto;
//            $this->LblTelefono->Text = $arTercero->Telefono;
//            $this->LblDireccion->Text = $arTercero->Direccion;
//
//            $Saldo = TercerosRecord::CalcularSaldoTercero($this->TxtIdTercero->Text);
//            $this->LblSaldoCartera->Text = number_format($Saldo, 2, ',', '.');
        }
    }

    /**
     * Evalua y calcula el total adeudados por el cliente segun la edad de la cartera
     * @param type $sender
     * @param type $param
     */
    public function OnItemDataBound($sender, $param) {
        if ($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem") {
            $Gestion = GestionRecord::DevGestionXMoroso($param->Item->DataItem->CodObligacion);
            $param->Item->ADGGestion->DataSource = $Gestion;
            $param->Item->ADGGestion->dataBind();
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
        $Gestion = GestionRecord::DevGestionXMoroso($sender->Parent->Parent->ClmCodObligacion->Text, $this->CboUsuarios->SelectedValue);

        $this->ADGGestion->DataSource = $Gestion;
        $this->ADGGestion->dataBind();

        $this->mpnlGestion->Show();
    }

    public function Imprimir() {
        ImprimirEstadoCuenta::general($this->TxtIdTercero->Text);
    }

    /**
     * Carga de gestores
     * */
    private function CargarUsuarios() {
        $Usuarios = UsuariosRecord::Usuarios();

        $this->CboUsuarios->DataSource = $Usuarios;
        $this->CboUsuarios->dataBind();
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
