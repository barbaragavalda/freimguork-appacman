<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\PushForm;
use Appacman\Model\Utils\Permissions;
use Core\Utils\Session;

class PushNotifications extends Content {

    /**
     * @var \Appacman\Model\Item $item
     */
    protected $item = null;

    protected function run(){
        parent::run();

        $form = $this->getForm();
        /*
        $pushForm = new PushForm();
        foreach($form as &$input){
            $fieldName = $input->getFieldName();
            if( $fieldName == 'platform' ){
                $input = $pushForm->getInputPlatform($input->getName(), $fieldName);
            }
        }
        */
        r($form);
        exit;

        // form
        /*
        $success = false;
        if( isset($_POST['save']) ){
            $success = $this->item->save();
            $this->assign('formSuccess', $success);
            $this->assign('formSend', true);
        }else{
            $this->assign('formSend', false);
        }

        if( $success ){
            $session = Session::getInstance();
            $session->set('pendingMessage', gettext('Datos guardados correctamente.'));
            $this->redirect($this->domain . gettext('formulario') . '/' . $this->content->getID() . '/' . $this->item->getID());
        }else{
            $this->assign('prevNext', $this->content->getNextPrevItems($this->item->getID()));
            $this->assign('title', $this->getTitle());
            $this->assign('breadcrumb', $this->getBreadcrumb());
            $this->template('form.twig');
        }
        */
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();

        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canEdit = $this->user->hasPermission($contentID, Permissions::EDIT);
            $canCreate = $this->user->hasPermission($contentID, Permissions::CREATE);

            $itemID = $this->getParam('itemID');
            $this->item = new Item($itemID, $this->content->getTable());
            $hasPermission = false;
            if( $itemID == false && $canCreate){
                // has permission to create?
                $hasPermission = true;
            }else if( $itemID > 0 ){
                // has permission to edit or see?
                if( $this->item->exists() ){
                    if( $canEdit ){
                        $hasPermission = true;
                        $this->assign('itemID', $itemID);
                    }
                }
            }

            $this->assign('canEdit', $canEdit);
            $this->assign('canCreate', $canCreate);
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