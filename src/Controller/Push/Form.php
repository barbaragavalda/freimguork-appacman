<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\BaseContentForm;
use Appacman\Model\Push\Statistic;

class Form extends BaseContentForm {

    protected function run(){
        parent::run();

        if( $this->item->getID() ){
            foreach($this->info['form'] as $input){
                if( $input->getFieldName() == 'is_sent' && $input->getValue() ){
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

        $this->template('Push/form.twig');
    }

    protected function hasPermission() {
        $this->params['contentID'] = $this->parts[1];
        return parent::hasPermission();
    }

    protected function hasErrors(){
        return false;
    }

}