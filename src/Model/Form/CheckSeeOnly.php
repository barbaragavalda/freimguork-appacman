<?php
    
namespace Appacman\Model\Form;

class CheckSeeOnly extends SeeOnly {
    
    public function getListValue(){
        $value = parent::getSeeValue();
        if( $value ){
            return gettext('Sí');
        }
        return gettext('No');
    }
    
    public function getSeeValue($langID = null){
        return $this->getListValue();
    }
    
    protected function getInputHTML($langID = null){
        return $this->getListValue();
    }

}
