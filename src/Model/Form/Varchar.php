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

}