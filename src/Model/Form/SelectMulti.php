<?php

namespace Appacman\Model\Form;

class SelectMulti extends Select {

    private $currentTable = '';

    private $lateralTable = '';

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
        $this->initTables();
        $sql = '
            SELECT id_'.$this->lateralTable.' AS id
            FROM '.$this->fieldName.'
            WHERE id_'.$this->currentTable.' = :id
        ';
        $params = array(
            'id' => array('value'=> $this->id, 'type' => \PDO::PARAM_INT)
        );
        $values = $this->mysql->query($sql, $params);
        return array_column($values, 'id');
    }

    protected function getPostValue($langID = null){
        $postName = $this->getFieldName($langID);
        if( isset($_POST[$postName]) ){
            $this->initTables();

            // delete all
            $sql = '
                DELETE FROM '.$this->fieldName.'
                WHERE id_'.$this->currentTable.' = :id
            ';
            $params = array(
                'id' => array('value'=> $this->id, 'type' => \PDO::PARAM_INT)
            );
            $this->mysql->query($sql, $params);

            // insert again
            $values = array();
            foreach($_POST[$postName] as $index => $id){
                $values[] = '(:id, :lateral_id_'.$index.')';
                $params['lateral_id_'.$index] = array('value'=> $id, 'type' => \PDO::PARAM_INT);
            }
            $sql = '
                INSERT INTO '.$this->fieldName.' (id_'.$this->currentTable.', id_'.$this->lateralTable.') 
                VALUES '.implode(',', $values).'
            ';
            $this->mysql->query($sql, $params);
        }

        return null;
    }

    private function initTables(){
        $table = $this->fieldName;
        $tables = explode('_', $table);
        $this->currentTable = $tables[0];
        $this->lateralTable = $tables[1];
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