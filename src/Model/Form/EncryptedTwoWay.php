<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;

class EncryptedTwoWay extends FormInput {

    private $key = '';

    public function __construct($description, $id, $table){
        parent::__construct($description, $id, $table);

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
            $this->key = $row['id_'.$this->table] . '_' .$row['created'] . '_' . $this->getFieldName();
        }
    }

    public function getValue($langID = null){
        if( $this->key ){
            return TwoWay::decrypy(parent::getValue($langID), $this->key);
        }
        return '';
    }

    public function getInputHTML($langID = null){
        return $this->inputType('text');
    }

    public function getPostValue($langID = null){
        $postValue = parent::getPostValue($langID);
        return TwoWay::encrypt($postValue, $this->key);
    }

}