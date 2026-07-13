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
        return $this->renderTemplate('_input', array(
            'type'        => 'text',
            'postName'    => $this->getInputName($langID),
            'value'       => '',
            'placeholder' => $this->getPlaceholder(),
            'extra'       => '',
        ));
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