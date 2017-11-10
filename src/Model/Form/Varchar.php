<?php

namespace Appacman\Model\Form;

class Varchar extends FormInput {

    public function getHTML(){
        return $this->inputType('text');
    }

}