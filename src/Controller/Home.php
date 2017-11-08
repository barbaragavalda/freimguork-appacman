<?php
/**
 * Created by PhpStorm.
 * User: barbaragavaldabalada
 * Date: 6/11/17
 * Time: 15:34
 */

namespace Appacman\Controller;

class Home extends AppacmanController {

    protected function run(){
        $this->template('home.twig');
    }

    protected function hasPermission(){
        return $this->user->loggedIn();
    }

    protected function getTitle(){
        return gettext('Inicio');
    }

    protected function getBreadcrumb(){
        return array();
    }

}