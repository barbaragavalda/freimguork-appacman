<?php

namespace Appacman\Controller;

class DefaultController extends AppacmanController {

	// 404 error
    protected function run(){
        if( $this->user->loggedIn() ){
            $this->template('DefaultTemplate/loggedin.twig');
        }else{
            $this->template('DefaultTemplate/loggedout.twig');
        }
    }

    protected function hasPermission(){
        return true;
    }

    protected function getTitle(){
        return 'Appacman';
    }

    protected function getBreadcrumb(){
        return array();
    }

}