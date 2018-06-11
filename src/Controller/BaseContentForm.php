<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\Utils\Admin;
use Appacman\Model\Utils\Language;
use Appacman\Model\Utils\Permissions;
use Core\Model\Utils\Mail;
use Core\Utils\Session;

abstract class BaseContentForm extends Content {

    /**
     * @var \Appacman\Model\Item $item
     */
    protected $item = null;

    protected function run(){
        parent::run();

        $this->prepareForm();

        // form
        $success = false;
        if( isset($_POST['save']) ){
            $this->item->preparePost();
            if( !$this->hasErrors() ){
                $success = $this->item->save();
                $this->assign('formSuccess', $success);
                $this->assign('formSend', true);

                $contentID = $this->content->getID();
                if( $success && $this->user->hasPermission($contentID, Permissions::SEND_CHANGES) ){
                    $this->sendEmail();
                }
            }
        }else{
            $this->assign('formSend', false);
        }

        $this->printForm($success);
    }

    abstract protected function hasErrors();

    protected function prepareForm(){
        $this->assign('form', $this->getForm());
    }

    protected function prepareLinks(){
        $this->assign('prevNext', $this->content->getNextPrevItems($this->item->getID()));
        $this->assign('title', $this->getTitle());
        $this->assign('breadcrumb', $this->getBreadcrumb());
    }

    protected function printForm($success){
        if( $success ){
            $session = Session::getInstance();
            $session->set('pendingMessage', gettext('Datos guardados correctamente.'));
            $this->redirect($this->domain . gettext('formulario') . '/' . $this->content->getID() . '/' . $this->item->getID());
        }else{
            $this->prepareLinks();
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
            $canOwn = $this->user->hasPermission($contentID, Permissions::OWN);

            // has permission to create?
            $itemID = $this->getParam('itemID');
            $this->item = new Item($itemID, $this->content->getTable());
            $hasPermission = false;
            if( $itemID == false && $canCreate){
                $hasPermission = true;
                // has permission to edit or see?
            }else if( $itemID > 0 ){
                if( $this->item->exists() ){
                    if( $canSee || $canEdit ){
                        $hasPermission = true;
                    }
                    if( $canOwn ){
                        $profileInfo = $this->user->getProfileInfo();
                        if( $profileInfo != null ){
                            $info = $this->item->getValues();
                            if( array_key_exists($profileInfo['field'], $info) && $info[ $profileInfo['field'] ] == $profileInfo['value'] ){
                                $hasPermission = true;
                            }
                        }else{
                            $hasPermission = true;
                        }
                    }
                    if( $hasPermission ) $this->assign('itemID', $itemID);
                }
            }

            $this->assign('canSee', $canSee);
            $this->assign('canEdit', $canEdit);
            $this->assign('canCreate', $canCreate);
            $this->assign('canDelete', $canDelete);
            $this->assign('canOwn', $canOwn);
            $this->assign('canSendChanges', $this->user->hasPermission($contentID, Permissions::SEND_CHANGES));
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

    private function sendEmail(){
        $languagesModel = new Language();
        $languages = $languagesModel->get();
        $message = '<p style="font-size: 1.5em;">' . sprintf(gettext('Este es un email automático para informarte que se han producido cambios en %s a través del gestor.'), '"' . $this->item->getName() . '"') . '</p>';
        foreach($this->info['form'] as $input){
            if( !is_a($input, 'Appacman\Model\Form\Uri') ){
                if( $input->isOnLangTable() ){
                    foreach($languages as $language){
                        $message .= '<p><b>' . $input->getName() . ' (' . $language['name'] . '):</b> ' . $input->getSeeValue($language['id']) . '</p>';
                    }
                }else{
                    $message .= '<p><b>' . $input->getName() . ':</b> ' . $input->getSeeValue() . '</p>';
                }
            }
        }
        $message .= '<p style="color: red;"><b>' . gettext('Fecha cambio') . ':</b> ' . date('d-m-Y H:i:s') . '</p>';

        $admins = new Admin();
        $email = new Mail();
        $subject = sprintf(gettext('Cambios en %s'), '"' . $this->item->getName() . '"');
        $email->send(null, $admins->getEmails(), $subject, $message);
    }

}