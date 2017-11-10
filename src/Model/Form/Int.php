<?php

namespace Appacman\Model\Form;

class Int extends FormInput {

    public function getHTML(){
        return $this->inputType('text');
    }

}