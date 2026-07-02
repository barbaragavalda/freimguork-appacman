<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;

class EncryptedTwoWay extends Encrypted
{

    public function getSeeValue(?int $langID = null): string
    {
        if ($this->key) {
            return TwoWay::decrypt(parent::getSeeValue($langID), $this->key);
        }
        return '';
    }

    protected function getInputHTML(?int $langID = null): string
    {
        $postName = $this->getInputName($langID);
        $value    = TwoWay::decrypt(parent::getSeeValue($langID), $this->key);
        return '<input type="text" class="form-control" id="'
            . $postName
            . '" name="'
            . $postName
            . '" placeholder="'
            . $this->getPlaceholder()
            . '" value="'
            . $value
            . '" />';
    }

    protected function getPostValue(?int $langID = null): string
    {
        $postValue = parent::getPostValue($langID);
        return TwoWay::encrypt($postValue, $this->key);
    }

}