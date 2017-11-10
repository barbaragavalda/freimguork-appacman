<?php

namespace Appacman\Model\Form;

class Uri extends FormInput {

    public function __construct($description, $id){
        parent::__construct($description, $id);

        $this->isVisible = false;
    }

    public function getHTML(){
        return $this->inputType('hidden');
    }

}