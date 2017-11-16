<?php

namespace Appacman\Model\Form;

class Varchar extends FormInput {

    public function getInputHTML($langID = null){
        return $this->inputType('text', $langID);
    }

}