<?php

namespace Appacman\Model\Form;

class SelectMultiEncryptedTwoWay extends SelectMulti {

    protected function getOptions($table = null, $extraFields = ''){
        $lateralTable = str_replace('id_', '', $this->fieldName);
        return $this->loadOptions($lateralTable, ', created');
    }

}