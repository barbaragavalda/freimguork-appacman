<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\BaseContentForm;
use Appacman\Model\Push\Statistic;

class Form extends BaseContentForm {

    public function __construct(){
        parent::__construct();

        $this->listLink = _('notificaciones-push');
        $this->formLink = _('notificacion-push');
    }

    protected function run(){
        $this->template = 'Push/form.twig';
        parent::run();
    }

    protected function prepareForm()
    {
        parent::prepareForm();

        if( $this->item->getID() ){
            foreach($this->info['form'] as $input){
                if( $input->getFieldName() == 'is_sent' && filter_var($input->getValue(), FILTER_VALIDATE_BOOL) ){
                    // if is sent: cannot edit or delete
                    $this->assign('canEdit', false);
                    $this->assign('canDelete', false);

                    // and add some statistics
                    $statistics = new Statistic($this->item->getID());
                    $this->assign('statistics', $statistics->get());
                    break;
                }
            }
        }
    }

    protected function getBreadcrumb(){
        return array(
            array('name' => $this->content->getName(), 'link' => $this->domain . gettext('notificaciones-push') . '/' . $this->content->getID() ),
            array('name' => $this->item->getName(), 'link' => null)
        );
    }

    protected function hasErrors(){
        return false;
    }

}