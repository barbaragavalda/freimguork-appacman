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

    /**
     * @var array $languages. Available languages on database
     */
    protected $languages = 0;

    /**
     * @var int $type. PDO type of input
     */
    protected $type = \PDO::PARAM_STR;

    public function __construct($description, $id, $table = null){
        parent::__construct();
        $this->description = $description;
        $this->id = $id;
        $this->table = $table;
    }

    public function setLanguages($languages){
        $this->languages = $languages;
    }

    public function isVisible(){
        return $this->isVisible;
    }

    public function getName(){

        return $this->description['name'];
    }

    public function getValue($langID = null){
        $value = $this->description['value'];
        if( $langID == null && !is_array($value) ){
            return $value;
        }else{
            if( $langID == null ){
                $keys = array_keys($value);
                return $value[$keys[0]];
            }else{
                if( array_key_exists('lang_'.$langID, $value) ){
                    return $value['lang_'.$langID];
                }
            }
        }
        return '';
    }

    public function getFieldName(){
        return $this->description['field_name'];
    }

    public function getPostName($langID = null){
        $fieldName = $this->getFieldName();
        if( $langID == null ){
            return $fieldName;
        }else{
            return $fieldName . '_' .$langID;
        }
    }

    public function getPostValue($langID = null){
        return $_POST[ $this->getPostName($langID) ];
    }

    public function getSaveValue(){
        if( $this->onLangTable() ){
            $values = array();
            foreach($this->languages as $language) {
                $values['lang_'.$language['id']] = array(
                    $this->getFieldName() => array('value'=>$this->getPostValue($language['id']), 'type'=>$this->getTypeValue())
                );
            }
            return $values;
        }
        return array(
            $this->getFieldName() => array('value'=>$this->getPostValue(), 'type'=>$this->getTypeValue())
        );
    }

    public function getTypeValue(){
        return $this->type;
    }

    public function isRequired(){
        return $this->description['required'];
    }

    protected function inputType($type, $langID = null){
        $postName = $this->getPostName($langID);
        return '<input type="'.$type.'" class="form-control" id="'.$postName.'" name="'.$postName.'" placeholder="'.$this->getName().'" value="'.$this->getValue($langID).'" />';
    }

    public function label($value){
        return '<label class="form-label">' . $value . '</label>';
    }

    public function canSave(){
        return true;
    }

    public function onLangTable(){
        if( $this->mysql->fieldExists($this->table.'_lang', $this->getFieldName()) ){
            return true;
        }
        return false;
    }

    public function getFormHTML(){
        $html = '';
        if( $this->onLangTable() ){
            foreach($this->languages as $language) {
                $html .= $this->getFromRow($this->getInputHTML($language['id']), $language);
            }
        }else{
            $html .= $this->getFromRow($this->getInputHTML());
        }

        return $html;
    }

    public function getInfoHTML(){
        $html = '';
        if( $this->onLangTable() ){
            foreach($this->languages as $language) {
                $value = '<label class="form-label">'.$this->getValue($language['id']).'</label>';
                $html .= $this->getFromRow($value, $language);
            }
        }else{
            $value = '<label class="form-label">'.$this->getValue().'</label>';
            $html .= $this->getFromRow($value);
        }

        return $html;
    }

    private function getFromRow($input, $language = null){
        $classLang = '';
        $name = '';
        $html = '';
        if( $language != null ){
            $name = $language['name'];
            $classLang = ($language['id'] == null) ? '' : 'lang_'.$language['id'];
        }

        $html .= '<div class="form-horizontal '.$classLang.'">';
        $html .= '    <div class="form-group">';
        $html .= '        <label class="col-sm-2 control-label">'.$name.'</label>';
        $html .= '        <div class="col-sm-10">';
        $html .= '            ' . $input;
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '</div>';
        return $html;
    }

    abstract public function getInputHTML($langID = null);

}