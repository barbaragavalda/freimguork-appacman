<?php

namespace Appacman\Model\Form;

class EncryptedOneWay extends FormInput {

    /**
     * all two-way encrypted values, cannot be displayed because it is impossible to decrypt them
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return $this->label( '<i class="fa fa-eye-slash"></i> ' . gettext('Valor oculto') );
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

}