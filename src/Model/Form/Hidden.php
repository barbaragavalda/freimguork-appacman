<?php

namespace Appacman\Model\Form;

class Hidden extends FormInput {

    protected $isVisible = false;

    protected function getInputHTML($langID = null){
        return '';
    }

    public function getHTML(){
        return $this->inputType('hidden');
    }

    public function hasError($langID = null){
        return false;
    }

    public function save($itemID, $langID = null){
        return false;
    }

}