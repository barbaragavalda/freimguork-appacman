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
        return $this->renderTemplate('_input', array(
            'type'        => 'text',
            'postName'    => $this->getInputName($langID),
            'value'       => TwoWay::decrypt(parent::getSeeValue($langID), $this->key),
            'placeholder' => $this->getPlaceholder(),
            'extra'       => '',
        ));
    }

    protected function getPostValue(?int $langID = null): string
    {
        $postValue = parent::getPostValue($langID);
        return TwoWay::encrypt($postValue, $this->key);
    }

}