<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;

class EncryptedTwoWay extends FormInput {

    private $key = '';

    /**
     * Initialize input key for encryption
     * @param $info
     * @param $id
     * @param string|null $table
     */
    public function __construct($info, $id, $table){
        parent::__construct($info, $id, $table);

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
            $this->key = $row['id_'.$this->table] . '_' .$row['created'] . '_' . $this->fieldName;
        }
    }

    /**
     * decrypt value for display it on form
     * @param int|null $langID
     * @return string
     */
    public function getSeeValue($langID = null){
        if( $this->key ){
            return TwoWay::decrypy(parent::getSeeValue($langID), $this->key);
        }
        return '';
    }

    /**
     * input type text
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $postName = $this->getInputName($langID);
        $value = TwoWay::decrypy(parent::getSeeValue($langID), $this->key);
        return '<input type="text" class="form-control" id="'.$postName.'" name="'.$postName.'" placeholder="'.$this->getName().'" value="'.$value.'" />';
    }

    /**
     * encrypt value in order to save on database
     * @param null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        $postValue = parent::getPostValue($langID);
        return TwoWay::encrypt($postValue, $this->key);
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