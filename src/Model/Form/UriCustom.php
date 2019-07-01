<?php

namespace Appacman\Model\Form;

class UriCustom extends Uri {

    protected $isVisible = true;

    /**
     * input hidden
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return $this->inputType('text', $langID);
    }

    /**
     * encode field name for url
     * @param int|null $langID
     * @param bool $isHidden
     * @return string
     */
    protected function getPostValue($langID = null, $isHidden = true){
        return parent::getPostValue($langID, false);
    }

    public function hasError($langID = null){
        $table = $this->table;
        if( $this->onLangTable ) $table = $this->table . '_lang';

        $params = array(
            'value' => array('value' => $this->getPostValue($langID, false), 'type' => \PDO::PARAM_STR)
        );
        $sql = '
            SELECT *
            FROM ' . $table . '
            WHERE ' . $this->fieldName . ' = :value
        ';
        if( $this->id ){
            $sql .= ' AND id_' . $this->table . ' <> :id';
            $params['id'] = array('value' => $this->id, 'type' => \PDO::PARAM_INT);
        }
        $exists = $this->mysql->query($sql, $params);
        if( count($exists) ){
            return gettext('Esta URL ya existe, prueba con otra.');
        }
        return false;
    }

}
