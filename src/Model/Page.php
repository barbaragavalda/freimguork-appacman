<?php

namespace Appacman\Model;

use Appacman\Model\Utils\Field;
use Core\Model\Model;

abstract class Page extends Model {

    /**
     * @var int $id. Item id
     */
    protected $id = 0;

    /**
     * @var string $name. Title of this item
     */
    protected $name = 0;

    /**
     * @var array $info. Info of the content
     */
    protected $info = array();

    /**
     * @var \Appacman\Model\Utils\Field $fields. Fields info
     */
    protected $fields = array();

    /**
     * @var string $table. Table name
     */
    protected $table = '';

    public function __construct($id){
        parent::__construct();

        $this->id = $id;
    }

    public function getID(){
        return $this->id;
    }

    protected function initFields($tableName, $contentID = null){
        $this->fields = new Field($tableName, $contentID);
    }

    protected function getInputClass(&$field, $info = null){
        $info = ($info == null) ? $this->info : $info;
        // field value
        $fieldName = $field['field_name'];
        $field['value'] = '';
        if( array_key_exists($fieldName, $info) ){
            $field['value'] = $info[$fieldName];
        }

        // input view class
        $inputClass = 'Appacman\\Model\\Form\\' . ucfirst( $field['type'] );
        $id = count($info) ? $info['id_'.$this->table] : null;
        return new $inputClass($field, $id, $this->table);
    }

    abstract public function getName();

    /**
     * get the formulari for that item
     * @return array
     */
    abstract public function get();

    /**
     * check if this item exists
     * @return bool
     */
    abstract public function exists();

}