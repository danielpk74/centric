<?php

//incluye la clase TDbUserManager que define el metodo TDbUser.
Prado::using('System.Security.TDbUserManager');

/*
 * Valida la existencia de un usuario en la base de datos: nombre de usuario, contraseña. 
 * Redirecciona segun el resultado de la validacion. 
 *
 * */

class UsuariosAuth extends TDbUser {

    /**
     * crea un objeto de UsuariosAba basado en el nombre de usuario.
     * Este metodo es requerido por el TDbUser. que verificará la base de datos.
     * para ver si el nombre de usuario existe.
     * @param El nombre del usuario.
     * @return el objeto de UsuarioAba, null si el nombre del usuario es incorrecto.
     * */
    public function createUser($username) {
        $userRecord = UsuariosRecord::finder()->findByPk($username);
        if ($userRecord instanceof UsuariosRecord) {
            if ($userRecord->Inactivo != 1) {
                $user = new UsuariosAuth($this->Manager);
                $user->Name = strtolower($username);
                $user->Roles = ($userRecord->Tipo == 1 ? 'admin' : 'user');
                $user->IsGuest = false;
                return $user;
            } else {
                header('location:?page.LoginUsuario');
            }
        }
        else
            return null;
    }

    /**
     * Verifica si el usuario y la contraseña son válidos.
     * Este metodo es requerido por el TDbUser.
     * @param string usuario.
     * @param string password.
     * @return un boolean si el usuario y la contraseña son válidos.
     * */
    public function validateUser($username, $password) {
        return UsuariosRecord::finder()->findBy_Usuario_AND_Contrasena($username, md5($password))!==null;
    }

    /**
     * @return un booblean si el usuario es administrador.
     * */
    public function getIsAdmin() {
        return $this->isInRole('admin');
    }

}

?>