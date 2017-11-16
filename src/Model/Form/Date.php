<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\DateUtils;

class Date extends FormInput {

    public function getValue($langID = null){
        $value = parent::getValue($langID);
        return DateUtils::dmyFormat($value);
    }

    public function getInputHTML($langID = null){
        return $this->inputType('text', $langID);
    }

}