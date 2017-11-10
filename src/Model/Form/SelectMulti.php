<?php

namespace Appacman\Model\Form;

class SelectMulti extends Select {

    public function getHTML(){
        return '
            <select class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="'.gettext('Selecciona').' '.$this->getName().'" style="width: 100%;" tabindex="-1" aria-hidden="true">
                ' . $this->getOptionsHTML() . '
            </select>
        ';
    }


    protected function getOptions(){
        $tables = explode('_', $this->getFieldName());
        $lateralTable = $tables[1];
        return $this->loadOptions($lateralTable);
    }

    /**
     * get selected options
     */
    protected function loadValues(){
        $table = $this->getFieldName();
        $tables = explode('_', $table);
        $currentTable = $tables[0];
        $lateralTable = $tables[1];

        $sql = '
            SELECT id_'.$lateralTable.' AS id
            FROM '.$table.'
            WHERE id_'.$currentTable.' = :id
        ';
        $params = array(
            'id' => array('value'=> $this->id, 'type' => \PDO::PARAM_INT)
        );
        $values = $this->mysql->query($sql, $params);
        return array_column($values, 'id');
    }

}