<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;

class EncryptedTwoWay extends FormInput {

    public function getValue(){
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
            return TwoWay::decrypy(parent::getValue(), $key);
        }
        return '';
    }

    public function getHTML(){
        return $this->inputType('text');
    }

}