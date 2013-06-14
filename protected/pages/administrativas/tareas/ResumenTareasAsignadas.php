<?php

/**
 * Permite asignar las tareas de gestion a un usuario especifico o aleatoriamente,
 * pudiendose elegir las tareas por montos, por empresas, acuerdos de pago, acuerdos incumplidos
 * */
class ResumenTareasAsignadas extends TPage {

    public function OnInit($param) {
        if (!$this->IsPostBack) {
            $this->CargarUsuarios();
        }

        $this->Gestores();

    }

    /**
     * Carga los gestores al TRepeater
     * */
    public function Usuarios() {
        $this->DRTareasUsuarios->DataSource = UsuariosRecord::ObtenerGestores();
        $this->DRTareasUsuarios->dataBind();
    }

    /**
     * 
     * */
    public function OnItemCreated($sender, $param) {
        if ($param->Item->ItemType === 'Item' || $param->Item->ItemType === 'AlternatingItem') {
//
            if ($param->Item->DataItem->Usuario) {
                $Tareas = new TareasRecord();
                $Tareas = TareasRecord::finder()->With_Obligacion()->findAllBy_Usuario($param->Item->DataItem->Usuario);

                if (Count($Tareas) > 0) {
//                  $this->getClientScript()->registerEndScript($page->getClientID(), "Completado('$Completado');");
                    $param->Item->ADGObligaciones->DataSource = $Tareas;
                    $param->Item->ADGObligaciones->dataBind();
                }
            }       
        }
    }

    public function changePage($sender, $param) {
        $this->ADGObligaciones->CurrentPageIndex = $param->NewPageIndex; // Recupera la página que ha sido seleccionada y que será mostrada.
        $this->CargarObligaciones();
    }

    public function pagerCreated($sender, $param) {
        $param->Pager->Controls->insertAt(0, 'page: ');
    }
}

?>
