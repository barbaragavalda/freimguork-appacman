<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;

class EncryptedTwoWay extends Encrypted {

    /**
     * decrypt value for display it on form
     * @param int|null $langID
     * @return string
     */
    public function getSeeValue($langID = null){
        if( $this->key ){
            return TwoWay::decrypt(parent::getSeeValue($langID), $this->key);
        }
        return '';
    }

    /**
     * input type text
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $postName = $this->getInputName($langID);
        $value = TwoWay::decrypt(parent::getSeeValue($langID), $this->key);
        return '<input type="text" class="form-control" id="'.$postName.'" name="'.$postName.'" placeholder="'.$this->getPlaceholder().'" value="'.$value.'" />';
    }

    /**
     * encrypt value in order to save on database
     * @param null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        $postValue = parent::getPostValue($langID);
        return TwoWay::encrypt($postValue, $this->key);
    }

}