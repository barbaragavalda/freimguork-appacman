<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\Utils\Permissions;

class ContentForm extends Content {

    /**
     * @var \Appacman\Model\Item $item
     */
    private $item = null;

    protected function run(){
        parent::run();

        $this->assign('form', $this->item->get());
        $this->assign('breadcrumb', $this->getBreadcrumb());
        $this->template('form.twig');
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();

        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canSee = $this->user->hasPermission($contentID, Permissions::SEE);
            $canEdit = $this->user->hasPermission($contentID, Permissions::EDIT);
            $canCreate = $this->user->hasPermission($contentID, Permissions::CREATE);
            $canDelete = $this->user->hasPermission($contentID, Permissions::DELETE);

            // has permission to create?
            $itemID = $this->getParam('itemID');
            $this->item = new Item($itemID, $this->content->getTable());
            if( $itemID == false && $canCreate){
                $hasPermission = true;
                // has permission to edit or see?
            }else if( $itemID > 0 ){
                if( $this->item->exists() && ($canSee || $canEdit) ){
                    $hasPermission = true;
                }
            }

            $this->assign('canSee', $canSee);
            $this->assign('canEdit', $canEdit);
            $this->assign('canCreate', $canCreate);
            $this->assign('canDelete', $canDelete);
        }

        return $hasPermission;
    }

    protected function getTitle(){
        return gettext('Formulario') . ' ' . $this->content->getName();
    }

    protected function getBreadcrumb(){
        return array(
            array('name' => $this->content->getName(), 'link' => $this->domain . gettext('listado') . '/' . $this->content->getID() ),
            array('name' => $this->item->getName(), 'link' => null)
        );
    }

}