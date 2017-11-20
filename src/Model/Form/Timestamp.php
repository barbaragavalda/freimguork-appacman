<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\DateUtils;

class Timestamp extends FormInput {

    public function getValue($langID = null){
        $value = parent::getValue($langID);
        return DateUtils::userTimestamp($value);
    }

    public function getPostValue($langID = null){
        $value = parent::getPostValue($langID);
        return DateUtils::databaseTimestamp($value);
    }

    public function getInputHTML($langID = null){
        $value = self::getValue($langID);
        return $this->label($value) . $this->inputType('hidden', $langID);
    }

}