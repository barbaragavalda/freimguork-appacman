<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\DateUtils;

class Date extends FormInput {

    /**
     * format date for user
     * @param int|null $langID
     * @return string
     */
    public function getSeeValue($langID = null){
        $value = parent::getSeeValue($langID);
        return DateUtils::userDate($value);
    }

    /**
     * TODO: datepicker
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return $this->inputType('text', $langID);
    }

    /**
     * format date for database
     * @param int|null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        $value = parent::getPostValue($langID);
        return DateUtils::databaseDate($value);
    }

}