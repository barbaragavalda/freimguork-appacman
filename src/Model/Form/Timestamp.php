<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\DateUtils;

class Timestamp extends FormInput {

    /**
     * show like database in order to sort it correctly
     * @return mixed|string
     */
    public function getListValue(){
        return parent::getSeeValue();
    }

    /**
     * format timestamp for user
     * @param int|null $langID
     * @return string
     */
    public function getSeeValue($langID = null){
        $value = parent::getSeeValue($langID);
        return DateUtils::userTimestamp($value);
    }

    /**
     * TODO: datepicker
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $value = self::getSeeValue($langID);
        return $this->label($value) . $this->inputType('hidden', $langID);
    }

    /**
     * format timestamp for database
     * @param int|null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        $value = parent::getPostValue($langID);
        return DateUtils::databaseTimestamp($value);
    }

}