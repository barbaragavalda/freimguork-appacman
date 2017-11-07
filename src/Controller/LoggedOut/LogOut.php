<?php
/**
 * Created by PhpStorm.
 * User: barbaragavaldabalada
 * Date: 6/11/17
 * Time: 15:34
 */

namespace Appacman\Controller\LoggedOut;


use Appacman\Controller\AppacmanController;

class LogOut extends AppacmanController {

    public function run(){
        $this->user->logout();
        $this->redirect($this->domain . gettext('iniciar-sesion'));
    }

}