<?php

namespace Appacman\Model\Form;

use Core\Utils\Session;

class Own extends Select {

    protected function getInputHTML($langID = null){
        $session = Session::getInstance();
        $profile = $session->get('profile_info');

        if( $this->fieldName == $profile['field'] ){
            $this->value = $profile['value'];
            return $this->inputType('hidden', $langID) . $this->getSeeValue($langID);
        }else{
            return parent::getInputHTML($langID);
        }
    }

}