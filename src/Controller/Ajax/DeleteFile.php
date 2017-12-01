<?php

namespace Appacman\Controller\Ajax;

use Appacman\Controller\Content;
use Appacman\Model\Item;
use Appacman\Model\Utils\Permissions;
use Core\Model\File;

class DeleteFile extends Content {

    protected function run(){
        $itemID = $this->getParam('itemID');
        $fileID = $_POST['fieldID'];
        $fieldName = $_POST['fieldName'];

        $this->content->getTable();

        $file = new File($fileID);
        $error = !$file->delete($this->content->getTable(), $fieldName, $itemID, $fileID);

        $this->removeInfo();
        $this->assign('error', $error);
        $this->json();
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();

        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canEdit = $this->user->hasPermission($contentID, Permissions::EDIT);

            // has permission to edit?
            $itemID = $this->getParam('itemID');
            $item = new Item($itemID, $this->content->getTable());
            if( $itemID > 0 && $item->exists() && $canEdit ){
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