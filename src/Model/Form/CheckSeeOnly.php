<?php

namespace Appacman\Model\Form;

class CheckSeeOnly extends SeeOnly {

    protected function getInputHTML($langID = null){
        $value = parent::getSeeValue($langID);
        if( $value ){
            return gettext('Sí');
        }
        return gettext('No');
    }

}