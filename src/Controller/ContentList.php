<?php

namespace Appacman\Controller;

use Appacman\Model\Utils\Permissions;

class ContentList extends Content {

    protected function run(){
        parent::run();

        $this->assign('list_headers', $this->content->getTableHeaders());
        $this->assign('list_order', $this->content->getOrderBy());
        $this->assign('list', $this->content->get());

        $this->template('List/' . $this->content->getListType() . '.twig');
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();
        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canSee = $this->user->hasPermission($contentID, Permissions::SEE);
            $canEdit = $this->user->hasPermission($contentID, Permissions::EDIT);
            $canCreate = $this->user->hasPermission($contentID, Permissions::CREATE);
            $canDelete = $this->user->hasPermission($contentID, Permissions::DELETE);
            $canExport = $this->user->hasPermission($contentID, Permissions::EXPORT);
            $canLock = $this->user->hasPermission($contentID, Permissions::LOCK);

            // has permissions to see list?
            if( $canSee || $canEdit || $canCreate || $canDelete || $canExport || $canLock ){
                $this->assign('canSee', $canSee);
                $this->assign('canEdit', $canEdit);
                $this->assign('canCreate', $canCreate);
                $this->assign('canDelete', $canDelete);
                $this->assign('canExport', $canExport);
                $this->assign('canLock', $canLock);
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