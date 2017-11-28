<?php

namespace Appacman\Model\Form;

class SelectMulti extends Select {

    /**
     * select multiple (more than one option)
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return '
            <select name="'.$this->fieldName.'[]"  class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="'.gettext('Selecciona').' '.$this->getName().'" style="width: 100%;" tabindex="-1" aria-hidden="true">
                ' . $this->getOptionsHTML($langID) . '
            </select>
        ';
    }

    /**
     * from witch table has to load options?
     * @return array
     */
    protected function getOptions(){
        $tables = explode('_', $this->fieldName);
        $lateralTable = $tables[1];
        return $this->loadOptions($lateralTable);
    }

    /**
     * get selected options
     * @param $langID
     * @return array
     */
    protected function loadValues($langID){
        $table = $this->fieldName;
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

    /**
     * TODO: save select
     * @return bool
     */
    public function canSave($langID = null){
        return false;
    }

    /**
     * Check if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        return false;
    }

}