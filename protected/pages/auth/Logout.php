<?php
class Logout extends TPage
{
   /**
     * ButonLogout...
     * Finaliza la sesión del usuario actual.
     * El metodo responde al evento del boton "Logout".
     * @param 
     * @param 
     */
    public function BotonLogout($sender,$param)
    {
        $this->Application->getModule('auth')->logout();
        $url=$this->Service->constructUrl($this->Service->DefaultPage);
        $this->Response->redirect($url);
    }
    
    public function Cancelar($sender){
    	$this->Response->redirect('');
    }
}
?>