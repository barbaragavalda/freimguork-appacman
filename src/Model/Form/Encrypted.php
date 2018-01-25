<?php

namespace Appacman\Model\Form;

abstract class Encrypted extends FormInput {

    protected $key = '';

    /**
     * Initialize input key for encryption
     * @param $info
     * @param $id
     * @param string|null $table
     */
    public function __construct($info, $id, $table){
        parent::__construct($info, $id, $table);

        $keyID = 0;
        $keyCreated = null;
        if( $id ){
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
                $keyID = $row['id_'.$this->table];
                $keyCreated = $row['created'];
            }
        }else{
            $keyID = $this->mysql->getMaxId($table);
            if( isset($_POST['created']) ){
                $keyCreated = $_POST['created'];
            }
        }
        $this->key = $keyID . '_' . $keyCreated . '_' . $this->fieldName;
    }

    /**
     * Check if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $postValue = parent::getPostValue($langID);
        if( empty($postValue) && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }
        return false;
    }

}