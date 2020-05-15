<?php

namespace Appacman\Model\Form;

class SelectMultiEncryptedTwoWay extends SelectMulti {

    protected function getOptions($table = null, $extraFields = ''){
        return $this->loadOptions($this->lateralTable, ', created');
    }

}