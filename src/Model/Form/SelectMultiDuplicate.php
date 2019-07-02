<?php

namespace Appacman\Model\Form;

class SelectMultiDuplicate extends SelectMulti {

    protected function initTables(){
        $tables = explode('_', $this->fieldName);
        $this->currentTable = $tables[0];
        $tables = explode('_', $tables[1]);
        $this->lateralTable = $tables[0];
    }

}