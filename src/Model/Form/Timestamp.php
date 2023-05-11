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
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $value = self::getSeeValue($langID);
        if( !$value && $this->isRequired ){
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
        if( !$value && $this->value ) $value = $this->value;
        if( !$value && $this->isRequired ){
            $date = new \DateTime( date(DateUtils::FORMAT_TIMESTAMP_DB) );
            $date->add(new \DateInterval('PT5M'));
            $value = $date->format('Y-m-d H:i:s');
        }
        $this->value = $value;
        return $value;
    }

    /**
     * Check timestamp format and if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $postValue = $this->getPostValue($langID);
        if( $postValue == null && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }
        return false;
    }

    public function save($itemID, $langID = null){
        return false;
    }

}