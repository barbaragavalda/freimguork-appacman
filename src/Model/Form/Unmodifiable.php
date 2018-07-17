<?php

namespace Appacman\Model\Form;

class Unmodifiable extends FormInput {

    protected $isVisible = false;

    protected function getInputHTML($langID = null){
        return '';
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