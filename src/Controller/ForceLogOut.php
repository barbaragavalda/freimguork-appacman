<?php

namespace Appacman\Controller;

use Core\Model\Utils\SessionLog;

class ForceLogOut extends Content {

    protected function run(){
        parent::run();

        $contentID = $this->getParam('contentID');
        $itemID = $this->getParam('itemID');

        $log = new SessionLog();
        $log->logOut($itemID);

        $this->redirect($this->domain . _('listado') . '/' . $contentID );
    }

    protected function getTitle(){
        return '';
    }

    protected function getBreadcrumb(){
        return array();
    }

}