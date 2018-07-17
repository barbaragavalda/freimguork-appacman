<?php

namespace Appacman\Model\Form;

class Varchar extends FormInput {

    /**
     * remove tags on list
     * @param null $langID
     * @return string
     */
    public function getListValue($langID = null){
        return strip_tags(parent::getListValue($langID));
    }

    /**
     * input type text
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return $this->inputType('text', $langID);
    }

    /**
     * Check if its required
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