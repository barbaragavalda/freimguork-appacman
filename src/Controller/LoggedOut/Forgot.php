<?php

namespace Appacman\Controller\LoggedOut;

use Appacman\Controller\AppacmanController;
use Appacman\Model\LoggedOut\UserForm;

class Forgot extends AppacmanController {

    protected function run(){
        $send = false;
        if( isset($_POST['remember']) ){
            $form = new UserForm();
            $form->remember();

            $extraUser = 'Appacman\\Model\\ExtraUser';
            if( !$send && class_exists($extraUser) ){
                $form = new $extraUser();
                if( method_exists($form, 'remember') ){
                    $form->remember();
                }
            }

            $send = $form->getSend();
            $this->assign('form', $form->getForm());
            $this->assign('form_error', $form->getError());
        }

        $this->assign('form_send', $send);
        $this->template('LoggedOut/forgot.twig');
    }

    protected function hasPermission(){
        return !$this->user->loggedIn();
    }

    protected function getTitle(){
        return gettext('Recordar contraseña');
    }

    protected function getBreadcrumb(){
        return array();
    }
}