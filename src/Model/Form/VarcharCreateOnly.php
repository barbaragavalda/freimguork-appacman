<?php

namespace Appacman\Model\Form;

class VarcharCreateOnly extends Varchar {

    /**
     * input type text
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        if( $this->id ){
            return $this->getInputValue($langID) . $this->inputType('hidden', $langID);
        }else{
            return $this->inputType('text', $langID);
        }
    }

}