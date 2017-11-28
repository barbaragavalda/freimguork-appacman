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
     * TODO: timepicker
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

    /**
     * Check timestamp format and if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $value = parent::getPostValue($langID);
        $postValue = $this->getPostValue($langID);
        if( !empty($value) && preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4} ([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])$/', $value) == false ){
            return str_replace('%format%', 'dd/mm/yyyy hh:mm:ss', gettext('Comprueba que sea una fecha correcta con el formato %format%.'));
        }
        if( $postValue == null && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }
        return false;
    }

}