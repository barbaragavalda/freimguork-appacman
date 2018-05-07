<?php

namespace Appacman\Controller\LoggedOut;

use Appacman\Controller\AppacmanController;

class LogOut extends AppacmanController {

    protected function run(){
        $this->user->logout();
        $this->redirect($this->domain . gettext('iniciar-sesion'));
    }

    protected function hasPermission(){
        return true;
    }

    protected function getTitle(){
        return gettext('Cerrar sesión');
    }

    protected function getBreadcrumb(){
        return array();
    }

}