<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\StringUtils;

class SeeOnly extends FormInput {

    protected function getInputHTML($langID = null){
        $this->selectSeeValue();

        return $this->getSeeValue($langID);
    }

    private function selectSeeValue(){
        if( StringUtils::startsWidth($this->fieldName, 'id_') ){
            $table = str_replace('id_', '', $this->fieldName);
            if( $this->mysql->tableExists($table) ){
                $tableName = '';
                if( $this->mysql->fieldExists($table, 'name') ){
                    $tableName = $table;
                }else if( $this->mysql->fieldExists($table . '_lang', 'name') ){
                    $tableName = $table . '_lang';
                }

                $sql = '
                    SELECT t.name 
                    FROM ' . $tableName . ' AS t 
                    WHERE t.id_' . $table . ' = :id
                ';
                $params = array(
                    'id' => array('value' => $this->value, 'type' => \PDO::PARAM_INT)
                );
                $value = $this->mysql->query($sql, $params);
                if( count($value) ){
                    $this->value = $value[0]['name'];
                }
            }
        }
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

    public function save($itemID, $langID = null){
        return false;
    }

}