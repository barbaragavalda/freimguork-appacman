<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\DateUtils;

class Timestamp extends FormInput {

    public function getValue(){
        return DateUtils::hisDmyFormat($this->description['value']);
    }

    public function getHTML(){
        return $this->label( $this->getValue() );
        return $this->label( $this->getValue() );
    }

}