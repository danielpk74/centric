<?php

/**
 * Description of LibGeneral
 * Contiene todos los metodos y script que se utilizaran desde cualquier modulo
 * de la aplicacion.
 * @author daniel
 * */
class LibGeneral extends TPage {

    /**
     * Muestra un cuadro de error describiendo parametros faltantes o errores.
     * @param objeto $page  La pagina que se esta ejecutando
     * @param string $Error El mensaje de error que se desea mostrar
     * */
    public static function Error($page, $Error) {
        $page->getClientScript()->registerEndScript($page->getClientID(), "Error('$Error');");
    }

    /**
     * Muestra un cuadro de mensaje describiendo una operacion que se cumplio
     * satisfactoriamente.
     * @param objeto $page La pagina que se esta ejecutando.
     * @param string $Completado El mensaje que describe la operacion realizada.
     * */
    public static function Completado($page, $Mensaje) {
        $page->getClientScript()->registerEndScript($page->getClientID(), "Completado('$Mensaje');");
    }

    /**
     * Oculta los modal panel
     * @param objeto $page
     * @param objeto $Modal 
     * */
    public static function OcultarModal($page, $Modal) {
        $page->$Modal->Hide();
    }

    /**
     * Muestra mensaje con la cantidad de registros leidos y cargados
     * en una importacion de datos.
     * @param objeto $page La pagina donde se desea mostrar el mensaje $this->Page
     * @param integer $Total El numero de registros cargados
     * */
    public static function MsCargaCompleta($Page, $Total) {
        self::Completado($Page, 'Se ha cargado Correctamente la información. Registros: ' . $Total);
    }

    /**
     * Muestra mensaje de error cuando este se presenta en una carga de datos
     * */
    public static function MsErrorCarga($Page, $e) {
        self::Error($Page, "Se produjo un error durante la carga de los datos, intentelo nuevamente." . $e->getMessage());
    }

    public function ResultadoBusqueda($Page, $Datagrid, $Records) {
        try {
            if (Count($Records) != 0) {
                $Page->$Datagrid->DataSource = $Records;
                $Page->$Datagrid->dataBind();
                self::Completado($Page, "Se encontraron <b>" . Count($Records) . "</b> registros,");
            } else {
                $Page->$Datagrid->DataSource = "";
                $Page->$Datagrid->dataBind();
                self::Error($Page, "No se encontraron registros.");
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    //metodo que consulta si el usuario es administrador
    public function IsAdmin() {
        $Usuario = new UsuariosRecord;
        $Usuario = UsuariosRecord::finder()->findByPk(strtolower($this->User->Name));

        if ($Usuario->Tipo == 1)
            $Admin = true;
        else
            $Admin = false;

        return $Admin;
    }

    // Metodo que consulta el permiso que recibe por argumento

    /**
     * Verifica si el usuario tiene permiso(especial) para realizar determinada operacion.
     * @param int $IdPermiso => Id del permiso de la tabla permisosespeciales.
     * @return boolean que determina si tiene o no permiso.
     * */
    public function PermisosConsultas($IdPermiso) {

// Si el usuario no es administrador verificamos los permisos que tiene.
        $userRecord = UsuariosRecord::finder()->findByPk($this->user->Name);
        if (LibGeneral::IsAdmin() == false) {
            $PermisoEspecial = PermisosCrudRecord::finder()->findByPk($userRecord->Tipo, $IdPermiso);
        } else {
            $PermisoEspecial->Ver = true;
            $PermisoEspecial->Editar = true;
            $PermisoEspecial->Crear = true;
        }

        return $PermisoEspecial;
    }

    public static function IrA($url) {
        $this->Response->redirect($url);
    }

    /**
     * dividimos la fecha inicial en cada una de sus partes y mediante la funcion
     * mktime() obtenemos la marca de tiempo el resultado obtenido es en segundos,
     * restando las fechas 7 dividiendo el resultado por los segundos que tiene
     * un dia obtenemos el total de dias entre las fechas.
     * formato = AAAA/mm/dd
     * @param date $dFecIni
     * @param date $dFecFin
     * */
    public static function restaFechas($dFecIni, $dFecFin) {
        $totalDays = "";
        try {
            $startDate = $dFecIni;
            $endDate = $dFecFin;
            list($year, $month, $day) = explode('/', $startDate);
            $startDate = mktime(0, 0, 0, $month, $day, $year);
            list($year, $month, $day) = explode('/', $endDate);
            $endDate = mktime(0, 0, 0, $month, $day, $year);
            $totalDays = ($endDate - $startDate) / (60 * 60 * 24);
            return $totalDays;
        } catch (Exception $e) {
            return $totalDays;
        }
    }

    /**
     * recibe y cambia el formato de una fecha.
     * @param date Fecha en formato YYYY-mm-dd
     * @return date Fecha en formato YYYY/mm/dd
     */
    public static function ForFecha($Fecha) {
        try {
            list($Anio, $Mes, $Dia) = explode("-", $Fecha);
            return $Anio . "/" . $Mes . "/" . substr($Dia, 0, 2);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Busca la informaicion perteneciente a un tercero buscado por 
     * identificacion o por nombre, y retorna los datos en forma de lista al autocomplete desde el que 
     * este metodo es llamado.
     * @param type $sender
     * @param type $param
     * @param type $Todos Variable que determina si muestra un listado de terceros incluyendo los inactivos 
     * o solo los activos, por defecto solo los que estan activos.
     * @example en caso de intentar crear un documento nuevo, solo muestra los activos, para consultas y estadisticas 
     * los muestra todos.
     * */
    public static function BuscarTercero($sender, $param, $Todos = '') {
        $sql = "SELECT NombreCorto,Identificacion 
                FROM terceros
                WHERE IdTerceroPertenece IS NULL AND (NombreCorto LIKE '%" . $sender->Data . "%' OR Identificacion LIKE '%" . $sender->Data . "%')";
        if ($Todos == '')
            $sql = $sql . " AND Inactivo=0 ";

        $sql = $sql . " LIMIT 5";
        $terceros = TercerosRecord::finder()->findAllBySql($sql);
        $list = array();

        foreach ($terceros as $row)
            $list[] = $row->NombreCorto . "_" . $row->Identificacion;

        $sender->setDataSource($list);
        $sender->dataBind();
    }
    
     /**
     * Separa el nit(o identificacion) y el nombre de un tercero en el autocomplete
     * cuando el usuario selecciona una de las sugerencias que arroja el metodo "Buscar Tercero"
     * 
     * @param $object $objVista Es el objeto de la vista actual(pagina actual)
     * 
     * */
    public static function CallBack($objVista) {
        $Datos = explode("_", $objVista->TxtIdTercero->Text);
        $objVista->TxtIdTercero->Text = $Datos[1];
        $objVista->TxtNombre->Text = $Datos[0];
    }

    /**
     * Asigna texto al label y al boton encabezado de los crud
     * @param string $TextoLabel Texto del titulo
     * @param string $TextBoton Texto del boton de nuevo
     */
    public static function EncabezadoCrud($TextoLabel, $TextBoton, $Vista) {
        $Vista->LblNuevo->Text = $TextoLabel;
        $Vista->BtnNuevo->Text = $TextBoton;
    }

}

?>
