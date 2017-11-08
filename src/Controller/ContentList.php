<?php

namespace Appacman\Controller;

use Appacman\Model\Utils\Permissions;

class ContentList extends Content {

    protected function run(){
        parent::run();
        $this->assign('list_headers', $this->content->getHeaders());
        $this->assign('list_order', $this->content->getOrderBy());
        $this->assign('list', $this->content->get());
        $this->template('list.twig');
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();
        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canSee = $this->user->hasPermission($contentID, Permissions::SEE);
            $canEdit = $this->user->hasPermission($contentID, Permissions::EDIT);
            $canCreate = $this->user->hasPermission($contentID, Permissions::CREATE);
            $canDelete = $this->user->hasPermission($contentID, Permissions::DELETE);

            // has permissions to see list?
            if( $canSee || $canEdit || $canCreate || $canDelete ){
                $this->assign('canSee', $canSee);
                $this->assign('canEdit', $canEdit);
                $this->assign('canCreate', $canCreate);
                $this->assign('canDelete', $canDelete);
            }
        }

        return $hasPermission;
    }

    protected function getTitle(){
        return gettext('Listado') . ' ' . $this->content->getName();
    }

    protected function getBreadcrumb(){
        return array(
            array('name' => $this->content->getName(), 'link' => null)
        );
    }

}