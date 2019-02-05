<?php

namespace Appacman\Model\Form;

class EncryptedTwoWaySeeOnly extends EncryptedTwoWay {

    /**
     * input type text
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return $this->getSeeValue($langID);
    }

    /**
     * CANNOT save
     * @return bool
     */
    public function canSave($langID = null){
        return false;
    }

    public function hasError($langID = null){
        return false;
    }

    public function save($itemID, $langID = null){
        return false;
    }

}