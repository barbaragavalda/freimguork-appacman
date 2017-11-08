<?php
/**
 * Created by PhpStorm.
 * User: barbaragavaldabalada
 * Date: 6/11/17
 * Time: 15:34
 */

namespace Appacman\Controller\LoggedOut;


use Appacman\Controller\AppacmanController;
use Appacman\Model\LoggedOut\UserForm;

class Signin extends AppacmanController {

    protected function run(){
        $send = false;
        if( isset($_POST['enter']) ){
            $form = new UserForm();
            $form->signin();

            $send = $form->getSend();
            $this->assign('form', $form->getForm());
            $this->assign('form_error', $form->getError());
        }

        if( $send ){
            $this->redirect($this->domain . gettext(''));
        }else{
            $this->template('LoggedOut/signin.twig');
        }
    }

    protected function hasPermission(){
        return !$this->user->loggedIn();
    }

    protected function getTitle(){
        return gettext('Iniciar sesión');
    }

    protected function getBreadcrumb(){
        return array();
    }

}