<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\Utils\Language;
use Appacman\Model\Utils\Permissions;
use Core\Utils\Session;

class ContentForm extends Content {

    /**
     * @var \Appacman\Model\Item $item
     */
    protected $item = null;

    protected function run(){
        parent::run();

        // languages
        $languages = array();
        if( $this->item->hasLang() ){
            $lang = new Language();
            $languages = $lang->get();
        }

        // form
        $success = false;
        $this->assign('form', $this->item->get($languages));
        if( isset($_POST['save']) ){
            $success = $this->item->save();
            $this->assign('formSuccess', $success);
            $this->assign('formSend', true);
        }else{
            $this->assign('formSend', false);
        }

        $this->assign('prevNext', $this->content->getNextPrevItems($this->item->getID()));
        $this->assign('languages', $languages);
        $this->assign('title', $this->getTitle());
        $this->assign('breadcrumb', $this->getBreadcrumb());

        if( $success ){
            $session = Session::getInstance();
            $session->set('pendingMessage', gettext('Datos guardados correctamente.'));
            $this->redirect($this->domain . gettext('formulario') . '/' . $this->content->getID() . '/' . $this->item->getID());
        }else{
            $this->template('form.twig');
        }
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
                    $this->assign('itemID', $itemID);
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
        return $this->content->getName() . ' - ' . $this->item->getName();
    }

    protected function getBreadcrumb(){
        return array(
            array('name' => $this->content->getName(), 'link' => $this->domain . gettext('listado') . '/' . $this->content->getID() ),
            array('name' => $this->item->getName(), 'link' => null)
        );
    }

}