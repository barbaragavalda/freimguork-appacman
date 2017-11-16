<?php

namespace Appacman\Model\Form;

class EncryptedOneWay extends FormInput {

    public function getInputHTML($langID = null){
        return $this->label( '<i class="fa fa-eye-slash"></i> ' . gettext('Valor oculto') );
    }

    public function canSave(){
        return false;
    }

}