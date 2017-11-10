<?php

namespace Appacman\Model\Form;

use Core\Model\Model;

abstract class FormInput extends Model {

    /**
     * @var array $description. Item description
     */
    protected $description = array();

    /**
     * @var bool $isVisible. Is the input visible?
     */
    protected $isVisible = true;

    /**
     * @var int $id. Item id
     */
    protected $id = 0;

    /**
     * @var string $table. Table of the item
     */
    protected $table = 0;

    public function __construct($description, $id, $table = null){
        parent::__construct();
        $this->description = $description;
        $this->id = $id;
        $this->table = $table;
    }

    public function isVisible(){
        return $this->isVisible;
    }

    public function getName(){

        return $this->description['name'];
    }

    public function getValue(){
        return $this->description['value'];
    }

    public function getFieldName(){
        return $this->description['field_name'];
    }

    public function isRequired(){
        return $this->description['required'];
    }

    protected function inputType($type){
        return '<input type="'.$type.'" class="form-control" id="'.$this->getFieldName().'" name="'.$this->getFieldName().'" placeholder="'.$this->getName().'" value="'.$this->getValue().'" />';
    }

    public function label($value){
        return '<label class="form-label">' . $value . '</label>';
    }

    abstract public function getHTML();

}