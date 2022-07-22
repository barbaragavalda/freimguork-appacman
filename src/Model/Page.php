<?php

namespace Appacman\Model;

use Appacman\Model\Utils\Field;
use Core\Model\Model;

abstract class Page extends Model {

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

    public function getValues(){
        return $this->info;
    }

    protected function initFields($tableName, $contentID = null){
        $this->fields = new Field($tableName, $contentID);
    }

    public function getInputClass(&$field, $info = null){
        $info = ($info == null) ? $this->info : $info;
        // field value
        $fieldName = $field['field_name'];
        $field['value'] = '';
        if( array_key_exists($fieldName, $info) ){
            $field['value'] = $info[$fieldName];
        }

        // input view class
        if( strpos($field['type'], ' ') !== false ){
        	$explode = explode(' ', $field['type']);
        	if( count($explode) > 0 ){
        		$field['type'] = $explode[0];
        	}
        }
        $inputClass = 'Appacman\\Model\\Form\\' . ucfirst( $field['type'] );
        $id = null;
        if( count($info) ){
            if( is_a($this, 'Appacman\\Model\\Item') ){
                $id = $info['id_'.$this->table];
            }else if(array_key_exists('id', $info) ){
                $id = $info['id'];
            }
        }
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