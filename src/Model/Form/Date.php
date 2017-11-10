<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\DateUtils;

class Date extends FormInput {

    public function getValue(){
        return DateUtils::dmyFormat($this->description['value']);
    }

    public function getHTML(){
        return $this->inputType('text');
    }

}