<?php

namespace Appacman\Controller\Ajax;

use Appacman\Controller\Content;
use Appacman\Model\Item;

class Ajax extends Content {

    protected $permission = null;

    /**
     * @var \Appacman\Model\Item
     */
    protected $item = null;

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();

        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canLock = $this->user->hasPermission($contentID, $this->permission);

            // has permission to block?
            $itemID = $this->getParam('itemID');
            $item = new Item($itemID, $this->content->getTable());
            if( $itemID > 0 && $item->exists() && $canLock ){
                $this->item = $item;
                $hasPermission = true;
            }
        }

        return $hasPermission;
    }

    protected function setError($error){
        $this->removeInfo();
        $this->assign('error', $error);
        $this->json();
    }

    protected function getTitle(){
        return '';
    }

    protected function getBreadcrumb(){
        return array();
    }

}