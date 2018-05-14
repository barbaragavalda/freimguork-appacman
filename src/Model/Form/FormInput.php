<?php

namespace Appacman\Model\Form;

use Core\Model\Model;

abstract class FormInput extends Model {

    /**
     * field name and placeholder
     * @var string
     */
    protected $name = '';

    /**
     * field name on database
     * @var string
     */
    protected $fieldName = '';

    /**
     * field current value on dastabase
     * @var string
     */
    protected $value = '';

    /**
     * is required?
     * @var bool
     */
    protected $isRequired = false;

    /**
     * visible on form?
     * @var bool
     */
    protected $isVisible = true;

    /**
     * @var int $id. Item id
     */
    protected $id = 0;

    /**
     * @var string $table. Table of the item
     */
    protected $table = '';

    /**
     * on lang table?
     * @var bool
     */
    protected $onLangTable = false;

    /**
     * @var array $languages. Available languages on database
     */
    protected $languages = array();

    /**
     * @var int $type. PDO type of input
     */
    protected $type = \PDO::PARAM_STR;

    /**
     * validation error
     * @var false|string $error
     */
    protected $error = false;

    public function __construct($info, $id, $table = null){
        parent::__construct();
        $this->id = $id;
        $this->table = $table;

        $this->name = $info['name'];
        $this->fieldName = $info['field_name'];
        $this->value = $info['value'];
        $this->isRequired = $info['required'];
        if( $this->mysql->fieldExists($this->table.'_lang', $this->fieldName) ){
            $this->onLangTable = true;
        }
    }

    /**
     * available languages
     * @param $languages
     */
    public function setLanguages($languages){
        $this->languages = $languages;
    }

    /**
     * is on lang table
     */
    public function setOnLangTable(){
        $this->onLangTable = true;
    }

    /**
     * inputs can override this function in order to not be displayed of the form
     * @return bool
     */
    public function isVisible(){
        return $this->isVisible;
    }

    /**
     * is field required?
     * @return bool
     */
    public function isRequired(){
        return $this->isRequired;
    }

    /**
     * Field name (description useful for the user)
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    public function getPlaceholder(){
        return strip_tags($this->name);
    }

    /**
     * Field name on database
     * @return string
     */
    public function getFieldName(){
        return $this->fieldName;
    }

    /**
     * Value on database
     * @return string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * Get validation error
     * @return string
     */
    public function getError(){
        return $this->error;
    }

    /**
     * is on lang table?
     * @return bool
     */
    public function isOnLangTable(){
        return $this->onLangTable;
    }

    //*******************************************//
    //***************** L I S T *****************//
    //*******************************************//
    /**
     * Value to show on list
     * @return string
     */
    public function getListValue(){
        return $this->getSeeValue();
    }

    //*******************************************//
    //*** F O R M    N O N    E D I T A B L E ***//
    //*******************************************//
    /**
     * Value to show on form when user CANNOT edit
     * @return string
     */
    public function getInfoHTML(){
        $html = '';
        if( $this->onLangTable ){
            foreach($this->languages as $language) {
                $value = $this->getSeeValue($language['id']);
                $html .= $this->getFromRow($this->label($value), $language);
            }
        }else{
            $value = $this->getSeeValue();
            $html .= $this->getFromRow( $this->label($value) );
        }

        return $html;
    }

    /**
     * @param null $langID
     * @return mixed|string
     */
    public function getSeeValue($langID = null){
        return $this->getInputValue($langID);
    }

    //********************************************************//
    //******** F O R M    E D I T A B L E    P R I N T *******//
    //********************************************************//
    /**
     * Value to show on form when user CAN edit
     * @return string
     */
    public function getFormHTML(){
        $html = '';
        if( $this->onLangTable ){
            foreach($this->languages as $language) {
                $html .= $this->getFromRow($this->getInputHTML($language['id']), $language);
            }
        }else{
            $html .= $this->getFromRow($this->getInputHTML());
        }

        return $html;
    }

    /**
     * Value to show on form when user CANNOT edit
     * @return string
     */
    public function getSeeHTML(){
        $html = '';
        if( $this->onLangTable ){
            foreach($this->languages as $language) {
                $html .= $this->getFromRow($this->getSeeValue($language['id']), $language);
            }
        }else{
            $html .= $this->getFromRow($this->getSeeValue());
        }

        return $html;
    }

    /**
     * How the inout is displayed to de user in order to edit it
     * @param int|null $langID
     * @return mixed
     */
    abstract protected function getInputHTML($langID = null);

    /**
     * Name of the input for post value
     * @param int|null $langID
     * @return string
     */
    protected function getInputName($langID = null){
        $fieldName = $this->fieldName;
        if( $langID == null ){
            return $fieldName;
        }else{
            return $fieldName . '_' .$langID;
        }
    }

    /**
     * Value of the input for post value
     * @param int|null $langID
     * @return string
     */
    protected function getInputValue($langID = null){
        $postName = $this->getInputName($langID);
        if( array_key_exists($postName, $_POST) ){
            return $_POST[$postName];
        }else{
            if( !empty($this->value) ){
                if( $langID == null && !is_array($this->value) ){
                    return $this->value;
                }else{
                    if( $langID == null ){
                        $keys = array_keys($this->value);
                        return $this->value[$keys[0]];
                    }else{
                        if( is_array($this->value) && array_key_exists('lang_'.$langID, $this->value) ){
                            return $this->value['lang_'.$langID];
                        }
                    }
                }
            }
        }
        return '';
    }

    //********************************************************//
    //********* F O R M    E D I T A B L E    P O S T ********//
    //********************************************************//
    /**
     * Can be saved on database?
     * @return bool
     */
    public function canSave($langID = null){
        return true;
    }

    /**
     * post value
     * @param int|null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        if( isset($_POST[ $this->getInputName($langID) ]) ){
            return $_POST[ $this->getInputName($langID) ];
        }
        return '';
    }

    /**
     * value to be saved on database
     * @return array
     */
    public function getSaveValue(){
        // multi language
        if( $this->onLangTable ){
            $values = array();
            foreach($this->languages as $language) {
                if( $this->canSave($language['id']) ){
                    $this->error = $this->hasError($language['id']);
                    if( !$this->error ){
                        $values['lang_'.$language['id']] = array(
                            $this->fieldName => array('value'=>$this->getPostValue($language['id']), 'type'=>$this->type)
                        );
                    }
                }
            }
            return $values;
        }

        // no language
        if( $this->canSave() ){
            $this->error = $this->hasError();
            if( !$this->error ) {
                return array(
                    $this->fieldName => array('value' => $this->getPostValue(), 'type' => $this->type)
                );
            }
        }

        return null;
    }

    /**
     * @param null $langID
     * @return false|string
     */
    abstract protected function hasError($langID = null);

    //*******************************************//
    //*********** F O R M    U T I L S **********//
    //*******************************************//
    /**
     * Row HTML on form
     * @param $input
     * @param int|null $language
     * @return string
     */
    private function getFromRow($input, $language = null){
        $name = '';
        $span = '';
        $extraClass = '';
        if( $language != null ){
            $name = $language['name'];
            $extraClass = ($language['id'] == null) ? '' : 'lang_'.$language['id'];
        }
        if( $this->error ){
            $extraClass = ' has-error';
            $span = '<span class="help-block"><i class="fa fa-times-circle-o"></i> '.$this->error.'</span>';
        }

        return '
            <div class="form-horizontal '.$extraClass.'">
                <div class="form-group">
                    <label class="col-sm-2 control-label">'.$name.'</label>
                    <div class="col-sm-10">
                        '.$input.'
                        '.$span.'
                    </div>
                </div>
            </div>
        ';
    }

    /**
     * Label HTML on form row
     * @param $value
     * @return string
     */
    protected function label($value){
        return '<label class="form-label">' . $value . '</label>';
    }

    /**
     * Default input HTML
     * @param $type
     * @param null $langID
     * @return string
     */
    protected function inputType($type = 'text', $langID = null){
        $postName = $this->getInputName($langID);
        return '<input type="'.$type.'" class="form-control" id="'.$postName.'" name="'.$postName.'" placeholder="'.$this->getPlaceholder().'" value="'.$this->getInputValue($langID).'" />';
    }

}