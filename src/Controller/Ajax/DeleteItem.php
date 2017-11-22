<?php

namespace Appacman\Controller\Ajax;

use Appacman\Controller\Content;
use Appacman\Model\Item;
use Appacman\Model\Utils\Permissions;
use Core\Model\File;

class DeleteItem extends Content {

    private $item = array();

    protected function run(){
        $success = $this->item->delete();

        $this->removeInfo();
        $this->assign('error', !$success);
        $this->json();
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();

        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canEdit = $this->user->hasPermission($contentID, Permissions::EDIT);

            // has permission to edit?
            $itemID = $this->getParam('itemID');
            $this->item = new Item($itemID, $this->content->getTable());
            if( $itemID > 0 && $this->item->exists() && $canEdit ){
                $hasPermission = true;
            }
        }

        return $hasPermission;
    }

    protected function getTitle(){
        return '';
    }

    protected function getBreadcrumb(){
        return array();
    }

}