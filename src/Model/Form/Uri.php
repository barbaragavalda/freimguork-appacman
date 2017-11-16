<?php

namespace Appacman\Model\Form;

class Uri extends FormInput {

    public function __construct($description, $id, $table) {
        parent::__construct($description, $id, $table);

        $this->isVisible = false;
    }

    public function getInputHTML($langID = null){
        return $this->inputType('hidden');
    }

    public function getSaveValue(){
        $postValue = parent::getSaveValue();
        return urlencode( strip_tags($postValue) );
    }

}