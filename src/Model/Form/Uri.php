<?php

namespace Appacman\Model\Form;

class Uri extends FormInput {

    protected $isVisible = false;

    /**
     * input hidden
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return $this->inputType('hidden', $langID);
    }

    /**
     * encode field name for url
     * @param int|null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        $postValue = ($langID == null) ? $_POST['name'] : $_POST['name_'.$langID];
        return urlencode( strip_tags($postValue) );
    }

    public function hasError($langID = null){
        return false;
    }

}