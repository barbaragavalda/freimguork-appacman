<?php

namespace Appacman\Model\Form;

class Int extends FormInput {

    public function getInputHTML($langID = null){
        return $this->inputType('text');
    }

    public function getTypeValue(){
        return \PDO::PARAM_INT;
    }

}