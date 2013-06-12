<?php
prado::using("Application.librerias.LibGeneral");
class MainLayout extends TTemplateControl
{   
    /**
     * Muestra el usuario actual en el .tpl
     **/
    
    public $OpcionMenu;
	
    public function OnInit($param)
    {   
        $Usuario = new UsuariosRecord();
        $Usuario =  UsuariosRecord::finder()->findByPk($this->User->Name);
        if($Usuario->Tipo == '1')
            $this->OpcionMenu = "display";
        else
            $this->OpcionMenu = "none";
    }
    
    public function OpcionMenu()
    {        
            return $this->OpcionMenu ;
    }
    
    
    /**
     * ButonLogout...
     * Finaliza la sesión del usuario actual.
     * El metodo responde al evento del boton "Logout".
     * @param 
     * @param 
     **/
	
    public function BotonLogout($sender,$param)
    {
//        $this->Application->getModule('auth')->logout();
//        $url=$this->Service->constructUrl($this->Service->DefaultPage);
//        $this->Response->redirect($url);
    }
    

}
?>