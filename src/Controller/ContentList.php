<?php

namespace Appacman\Controller;

use Appacman\Model\Content;
use Appacman\Model\Utils\Permissions;

class ContentList extends AppacmanController {

    /**
     * @var \Appacman\Model\Content $content
     */
    private $content = null;

    protected function run(){
        $this->assign('list_headers', $this->content->getHeaders());
        $this->assign('list_order', $this->content->getOrderBy());
        $this->assign('list', $this->content->get());
        $this->assign('contentID', $this->content->getID());
        $this->template('list.twig');
    }

    protected function hasPermission(){
        $hasPermission = false;
        $contentID = intval($this->getParam('contentID'));
        // has content id?
        if( $contentID > 0 ){
            // has permissions?
            $canSee = $this->user->hasPermission($contentID, Permissions::SEE);
            $canEdit = $this->user->hasPermission($contentID, Permissions::EDIT);
            $canCreate = $this->user->hasPermission($contentID, Permissions::CREATE);
            $canDelete = $this->user->hasPermission($contentID, Permissions::DELETE);
            if( $canSee || $canEdit || $canCreate || $canDelete ){
                // content exists?
                $this->content = new Content($contentID);
                if( $this->content->exists() ){
                    $hasPermission = true;
                    $this->assign('canSee', $canSee);
                    $this->assign('canEdit', $canEdit);
                    $this->assign('canCreate', $canCreate);
                    $this->assign('canDelete', $canDelete);
                }
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