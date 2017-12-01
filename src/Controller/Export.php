<?php

namespace Appacman\Controller;

use Appacman\Model\Utils\Permissions;

class Export extends Content {

    protected function run(){
        parent::run();

        $this->assign('csv', $this->content->get());
        $this->export($this->content->getTable());
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();
        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canExport = $this->user->hasPermission($contentID, Permissions::EXPORT);

            // has permissions to export?
            if( $canExport ){
                return true;
            }
        }

        return $hasPermission;
    }

    protected function getTitle(){
        return gettext('Exportar') . ' ' . $this->content->getName();
    }

    protected function getBreadcrumb(){
        return array();
    }

}