<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\Utils\Language;
use Appacman\Model\Utils\Permissions;
use Core\Utils\Session;

class Info extends AppacmanController {

    protected function run(){
        $this->template('info.twig');
    }

    protected function hasPermission(){
        return true;
    }

    protected function getTitle(){
        return gettext('Información');
    }

    protected function getBreadcrumb(){
        return array(
            array('name' => gettext('Información'), 'link' => null)
        );
    }

}