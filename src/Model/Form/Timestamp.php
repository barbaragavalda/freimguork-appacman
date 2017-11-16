<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\DateUtils;

class Timestamp extends FormInput {

    public function getValue($langID = null){
        $value = parent::getValue($langID);
        return DateUtils::hisDmyFormat($value);
    }

    public function getInputHTML($langID = null){
        return $this->label( $this->getValue($langID) );
    }

}