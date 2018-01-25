<?php

namespace Appacman\Controller\Ajax;

use Appacman\Controller\Content;
use Appacman\Model\Item;
use Appacman\Model\Utils\Permissions;

class BlockItem extends Content {

    /**
     * @var \Appacman\Model\Item
     */
    protected $item = null;

    /**
     * @var int locked state
     */
    protected $state = 0;

    protected function run(){
        if( isset($_POST['state']) ){
            $this->state = $_POST['state'];
        }

        $this->removeInfo();
        $this->assign('error', !$this->item->block($this->state));
        $this->json();
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();

        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canLock = $this->user->hasPermission($contentID, Permissions::LOCK);

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

    protected function getTitle(){
        return '';
    }

    protected function getBreadcrumb(){
        return array();
    }

}