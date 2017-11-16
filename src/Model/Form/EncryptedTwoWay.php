<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;

class EncryptedTwoWay extends FormInput {

    public function getValue($langID){
        $sql = '
            SELECT *
            FROM '.$this->table.'
            WHERE id_'.$this->table.' = :id
        ';
        $params = array(
            'id' => array('value' => $this->id, 'type' => \PDO::PARAM_INT)
        );

        $row = $this->mysql->query($sql, $params);
        if( count($row) ){
            $row = $row[0];
            $key = $row['id_'.$this->table] . '_' .$row['created'] . '_' . $this->getFieldName();
            return TwoWay::decrypy(parent::getValue($langID), $key);
        }
        return '';
    }

    public function getInputHTML($langID = null){
        return $this->inputType('text');
    }

}