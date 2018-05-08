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
        return $value;
    }

    /**
     * TODO: timepicker
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $value = self::getSeeValue($langID);
        if( !$value ){
            $value = date(DateUtils::FORMAT_TIMESTAMP_DB);
            $this->value = $value;
        }
        return $this->label($value) . $this->inputType('hidden', $langID);
    }

    /**
     * format timestamp for database
     * @param int|null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        $value = parent::getPostValue($langID);
        if( !$value ){
            $value = date(DateUtils::FORMAT_TIMESTAMP_DB);
            $this->value = $value;
        }
        return $value;
    }

    /**
     * Check timestamp format and if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        return false;
    }

}