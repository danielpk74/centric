<?php

prado::using("Application.database.UsuariosRecord");

class LoginUsuario extends TPage {

    public function OnInit($param) {
        $this->usuario->focus();
    }

    /**
     * Valida si el nombre de usuario y la contraseña son correctos.
     * Este metodo responde a el TCustomValidator's OnServerValidate event.
     * @param diferentes valores enviados.
     * @param diferentes parametros enviados.
     */
    public function validateUser($sender, $param) {
        $authManager = $this->Application->getModule('auth');

        if (!$authManager->login($this->usuario->Text, $this->contrasena->Text)) {
            $param->IsValid = false; //Si no son correctos los datos, dice que falló.
        }
    }

    /**
     * Redirecciona el explorador del usuario a una URL especifica si los datos del login son corréctos.
     * Este metodo responde a el evento onClick del boton Login.
     * @param diferentes eventos enviados desde la plantilla. 
     * @param diferentes parametros enviados desde la plantilla.
     */
    public function BotonLogin($sender, $param) {
        
        if ($this->Page->IsValid) {     //si son correctos los datos de validación.

            //recupera la URL de la página una página predeterminada para usuarios.
            $url = $this->Application->getModule('auth')->ReturnUrl;
            $url = $this->Service->DefaultPageUrl;

            $this->Response->redirect($url);
        }
    }

}

?>