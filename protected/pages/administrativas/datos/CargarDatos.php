<?php

/**
 * Carga los datos base de la aplicacion por medio de fupArchivos estructurados 
 * Excel o CSV
 * @author daniel
 * */
prado::using("Application.pages.administrativas.datos.CargaTerceros");
prado::using("Application.pages.administrativas.datos.CargaMorosos");
prado::using("Application.pages.administrativas.datos.CargaObligaciones");
prado::using("Application.pages.administrativas.datos.CargaTodos");

class CargarDatos extends TPage {

    public function OnInit($param) {
        parent::OnInit($param);
    }

    /**
     * Carga los datos especificados por el usuario, 
     * puede ser los terceros, pueden ser las obligaciones de estos o 
     * los abonos a las obligaciones registradas.
     * */
    public function Cargar() {
        $i = 0;

        $LibGeneral = new LibGeneral();

        if ($this->fupArchivo->HasFile) {

            if ($this->ChkTerceros->Checked) {
                $this->ImportarTerceros();
                $i++;
            }

            if ($this->ChkMorosos->Checked) {
                $this->ImportarMorosos();
                $i++;
            }
//
            if ($this->ChkObligaciones->Checked) {
                $this->ImportarObligaciones();
                $i++;
            }
//
            if ($this->ChkAbonos->Checked)
                $this->ImportarAbonos();

            if ($this->ChkTodos->Checked) {
                $this->ImportarTodos();
                $i++;
            }

            if ($i == 0) {
                $LibGeneral->Error($this->Page, "Debe seleccionar una opcion de carga.");
            }
        } else {
            $LibGeneral->Error($this->Page, "Debe seleccionar un Archivo de carga.");
        }
    }

    /**
     * Carga de obligaciones
     * */
    public function ImportarObligaciones() {

        $LibGeneral = new LibGeneral();
        $filename = explode(".", $this->fupArchivo->FileName);
        $filetemp = $this->fupArchivo->localName;
        $filetype = $this->fupArchivo->FileType;
        $hasfile = $this->fupArchivo->HasFile;

        $Carga = CargaObligaciones::CargarObligacion($filename, $filetemp, $filetype, $hasfile, $this->Page);

        if ($Carga == false)
            LibGeneral::Error($this, "Se presento un error durante el proceso, por favor vuelva a intentarlo.<br/>" .
                    CargaObligaciones::ObtenerMensaje());
        else
            LibGeneral::Completado($this, "Se han actualizado los registros.<br/>" . CargaObligaciones::ObtenerMensaje());
    }

    public function ImportarTodos() {

        $LibGeneral = new LibGeneral();
        $filename = explode(".", $this->fupArchivo->FileName);
        $filetemp = $this->fupArchivo->localName;
        $filetype = $this->fupArchivo->FileType;
        $hasfile = $this->fupArchivo->HasFile;

        $Carga = CargaTodos::ImportarTodos($filename, $filetemp, $filetype, $hasfile, $this->Page);

        if ($Carga == false)
            LibGeneral::Error($this, "Se presento un error durante el proceso, por favor vuelva a intentarlo.<br/>" .
                    CargaObligaciones::ObtenerMensaje());
        else
            LibGeneral::Completado($this, "Se han actualizado los registros.<br/>" . CargaObligaciones::ObtenerMensaje());
    }

    public function ImportarMorosos() {

        $LibGeneral = new LibGeneral();
        $filename = explode(".", $this->fupArchivo->FileName);
        $filetemp = $this->fupArchivo->localName;
        $filetype = $this->fupArchivo->FileType;
        $hasfile = $this->fupArchivo->HasFile;

        $Carga = CargaMorosos::ImportarMorosos($filename, $filetemp, $filetype, $hasfile, $this->Page);

        if ($Carga == false)
            LibGeneral::Error($this, "Se presento un error durante el proceso, por favor vuelva a intentarlo.<br/>" .
                    CargaMorosos::ObtenerMensaje());
        else
            LibGeneral::Completado($this, "Se han actualizado los registros.<br/>" . CargaMorosos::ObtenerMensaje());
    }

    /**
     * Carga de terceros
     * */
    public function ImportarTerceros() {
        $filename = explode(".", $this->fupArchivo->FileName);
        $filetemp = $this->fupArchivo->localName;
        $filetype = $this->fupArchivo->FileType;
        $hasfile = $this->fupArchivo->HasFile;

        $Carga = CargaTerceros::ImportarTerceros($filename, $filetemp, $filetype, $hasfile, $this->Page);

        if ($Carga == false)
            LibGeneral::Error($this, "Se presento un error durante el proceso, por favor vuelva a intentarlo.<br/>" .
                    CargaTerceros::ObtenerMensaje());
        else
            LibGeneral::Completado($this, "Se han actualizado los registros.<br/>" . CargaTerceros::ObtenerMensaje());
    }

    /**
     * Carga de terceros
     * */
    public function ImportarAbonos() {
        try {
            $filename = $this->fupArchivo->FileName;
            $filetemp = $this->fupArchivo->localName;
            $filetype = $this->fupArchivo->FileType;
            $hasfile = $this->fupArchivo->HasFile;

            CargarAbonos::CargarAbonos($filename, $filetemp, $filetype, $hasfile, $this->Page);

//            if ($Carga == false)
//                LibGeneral::Error($this, "Se presento un error durante el proceso, por favor vuelva a intentarlo.");
//            else
//                LibGeneral::Completado($this, "Se han actualizado " . $Carga - 1 . " registros.");
        } catch (Excetion $e) {
            LibGeneral::Error($this, "Se presento un error durante el proceso, por favor vuelva a intentarlo<br/>"); {
                
            }
        }
    }

}

?>
