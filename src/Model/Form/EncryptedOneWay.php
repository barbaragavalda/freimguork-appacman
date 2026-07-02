<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\OneWay;

class EncryptedOneWay extends Encrypted
{

    public function getSeeValue(?int $langID = null): string
    {
        return $this->label('<i class="fa fa-eye-slash"></i> ' . _('Valor oculto'));
    }

    protected function getInputHTML(?int $langID = null): string
    {
        $postName = $this->getInputName($langID);
        return '<input type="text" class="form-control" id="'
            . $postName
            . '" name="'
            . $postName
            . '" placeholder="'
            . $this->getPlaceholder()
            . '" value="" />';
    }

    public function canSave(?int $langID = null): bool
    {
        $postValue = parent::getPostValue($langID);
        if ($postValue) {
            return true;
        }
        return false;
    }

    protected function getPostValue(?int $langID = null): ?string
    {
        $postValue = parent::getPostValue($langID);
        return OneWay::encrypt($postValue, $this->key);
    }

    public function hasError(?int $langID = null): bool|string
    {
        if ($this->id) {
            return false;
        } else {
            return parent::hasError($langID);
        }
    }

}