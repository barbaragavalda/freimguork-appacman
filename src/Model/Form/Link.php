<?php

namespace Appacman\Model\Form;

class Link extends FormInput {

    /**
     * show link
     * @param null $langID
     * @return string
     */
    public function getSeeValue($langID = null){
        $value = parent::getInputValue($langID);
        if( $value ){
            return '<a href="'.$value.'" target="_blank">'.$value.'</a>';
        }
        return '';
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
        if( !empty($postValue) && !filter_var($postValue, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED) ){
            return gettext('Comprueba el formato del link: que empieze por http:// o https://.');
        }
        if( $postValue == null && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }
        return false;
    }

}