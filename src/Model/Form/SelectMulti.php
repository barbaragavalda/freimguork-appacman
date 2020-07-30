<?php

namespace Appacman\Model\Form;

class SelectMulti extends Select {

    protected $currentTable = '';

    protected $lateralTable = '';

    public function __construct($info, $id, $table = null){
        parent::__construct($info, $id, $table);

        $this->initTables();
    }

    public function getSeeValue($langID = null){
        $options = $this->getOptions();
        $values = $this->loadValues($langID);

        if( count($values) ){
            $value = array();
            foreach( $options as $option ){
                if( in_array($option['id'], $values) ){
                    $value[] = $option['name'];
                }
            }
            return implode(', ', $value);
        }
        return '-';
    }

    /**
     * select multiple (more than one option)
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $fieldName = $this->getInputName($langID);
        $selectCheck = $this->getInputName($langID, false) . '_selectAll';

        $field = '';
        if( $this->isMultiple === false ){
            $field = '
                <div class="select-all">
                    <input type="checkbox" class="custom-check select-all-checkbox" id="'.$selectCheck.'" name="'.$selectCheck.'" />
                    <label for="'.$selectCheck.'"> '.gettext('Seleccionar todos').'</label>
                </div>
            ';
        }
        $field .= '
            <select id="'.$fieldName.'" name="'.$fieldName.'"  class="form-control select2-multi select2-hidden-accessible" multiple="" data-placeholder="'.gettext('Selecciona').' '.$this->getPlaceholder().'" style="width: 100%;" tabindex="-1" aria-hidden="true" data-name="'.$this->fieldName.'">
                ' . $this->getOptionsHTML($langID) . '
            </select>
        ';
        return $field;
    }

    public function getInputName($langID = null, $withMultiple = true){
        $fieldName = $this->fieldName;
        if( $this->isMultiple !== false ) $fieldName .= $this->isMultiple;
        $multiple = $withMultiple ? '[]' : '';
        if( $langID == null ){
            return $fieldName . $multiple;
        }else{
            return $fieldName . '_' .$langID . $multiple;
        }
    }

    private function getLateralField(){
        if( $this->currentTable == $this->lateralTable ){
            return $this->lateralTable . '_related';
        }
        return $this->lateralTable;
    }

    /**
     * from witch table has to load options?
     * @return array
     */
    protected function getOptions($table = null, $extraFields = ''){
        return $this->loadOptions($this->lateralTable);
    }

    /**
     * get selected options
     * @param $langID
     * @return array
     */
    protected function loadValues($langID){
        $this->initTables();
        $sql = '
            SELECT id_'.$this->getLateralField().' AS id
            FROM '.$this->fieldName.'
            WHERE id_'.$this->currentTable.' = :id
        ';
        $params = array(
            'id' => array('value'=> $this->id, 'type' => \PDO::PARAM_INT)
        );
        $values = $this->mysql->query($sql, $params);
        return array_column($values, 'id');
    }

    protected function initTables(){
        $tables = explode('_', $this->fieldName);
        $this->currentTable = $tables[0];
        $this->lateralTable = substr(strstr($this->fieldName, '_'), 1);

        if( !$this->mysql->tableExists($this->lateralTable) ){
            $first = strpos($this->fieldName, '_');
            $pos = strpos($this->fieldName, '_', $first+1);
            $this->currentTable = substr($this->fieldName, 0, $pos);
            $this->lateralTable = substr($this->fieldName, $pos+1);
        }
    }

    /**
     * Check if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        return false;
    }

    public function canSave($langID = null){
        return false;
    }

    public function save($itemID, $langID = null){
        $postName = $this->getInputName($langID, false);
        if( isset($_POST[$postName]) ){
            return $this->insert($itemID, $_POST[$postName]);
        }
        return false;
    }

    protected function insert($itemID, $ids = array()){
        $this->initTables();

        // delete all
        $sql = '
                DELETE FROM '.$this->fieldName.'
                WHERE id_'.$this->currentTable.' = :id
            ';
        $params = array(
            'id' => array('value'=> $itemID, 'type' => \PDO::PARAM_INT)
        );
        $this->mysql->query($sql, $params);

        if( $this->mysql->getState() ){
            // insert again
            $values = array();
            $ids = array_unique($ids);
            foreach($ids as $index => $id){
                if( $id ){
                    $values[] = '(:id, :lateral_id_'.$index.')';
                    $params['lateral_id_'.$index] = array('value'=> $id, 'type' => \PDO::PARAM_INT);
                }
            }

            if( count($values) ){
                $sql = '
                        INSERT INTO '.$this->fieldName.' (id_'.$this->currentTable.', id_'.$this->getLateralField().') 
                        VALUES '.implode(',', $values).'
                    ';
                $this->mysql->query($sql, $params);
                if( $this->mysql->getState() ){
                    return false;
                }
            }else{
                return false;
            }
        }
    }

}