<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\OneWay;

class EncryptedOneWay extends Encrypted {

    public function getSeeValue($langID = null){
        return $this->label( '<i class="fa fa-eye-slash"></i> ' . gettext('Valor oculto') );
    }

    /**
     * all two-way encrypted values, cannot be displayed because it is impossible to decrypt them
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $postName = $this->getInputName($langID);
        return '<input type="text" class="form-control" id="'.$postName.'" name="'.$postName.'" placeholder="'.$this->getName().'" value="" />';
    }

    /**
     * encrypt value in order to save on database
     * @param null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        $postValue = parent::getPostValue($langID);
        return OneWay::encrypt($postValue, $this->key);
    }

}