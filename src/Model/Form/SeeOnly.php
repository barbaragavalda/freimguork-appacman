<?php

namespace Appacman\Model\Form;

class SeeOnly extends FormInput {

    protected function getInputHTML($langID = null){
        return $this->getSeeValue($langID);
    }

    /**
     * CANNOT save
     * @return bool
     */
    public function canSave($langID = null){
        return false;
    }

    public function hasError($langID = null){
        return false;
    }

    public function save($itemID, $langID = null){
        return false;
    }

}