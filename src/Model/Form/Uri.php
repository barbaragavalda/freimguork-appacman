<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\StringUtils;

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
     * @param bool $isHidden
     * @return string
     */
    protected function getPostValue($langID = null, $isHidden = true){
        $postValue = ($langID == null) ? $_POST['name'] : $_POST['name_'.$langID];
        if( !$isHidden ){
            $postValue = parent::getPostValue($langID);
        }
        $noTags = strip_tags($postValue);
        $lowerCase = mb_strtolower($noTags);
        return urlencode( StringUtils::removeSpecialCharacters($lowerCase) );
    }

    public function hasError($langID = null){
        return false;
    }

    public function save($itemID, $langID = null){
        return false;
    }

}
