<?php

namespace Appacman\Model\Form;

class EncryptedOneWay extends FormInput {

    public function getHTML(){
        return $this->label( '<i class="fa fa-eye-slash"></i> ' . gettext('Valor oculto') );
    }

}