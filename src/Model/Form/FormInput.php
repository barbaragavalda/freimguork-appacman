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
     * field hint
     * @var string
     */
    protected $hint = '';

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
     * post value on array
     * @var bool
     */
    protected $isMultiple = false;

    /**
     * @var array $languages. Available languages on database
     */
    protected $languages = array();

    /**
     * @var int $type. PDO type of input
     */
    protected $type = \PDO::PARAM_STR;

    /**
     * @var string $type
     */
    protected $fieldType = '';

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
        $this->fieldType = $info['type'];
        if( array_key_exists('hint', $info) ){
            $this->hint = $info['hint'];
        }
        if( array_key_exists('type', $info) && in_array($info['type'], array('dynamic', 'selectMulti')) ){
            $this->isRequired = false;
        }
        if( $this->mysql->fieldExists($this->table.'_lang', $this->fieldName) ){
            $this->onLangTable = true;
        }
    }

    public function isMultiple($position = true){
        $this->isMultiple = $position;
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
     * @param $onLangTable
     */
    public function setOnLangTable($onLangTable = true){
        $this->onLangTable = $onLangTable;
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
     * is field required?
     * @param bool
     */
    public function setIsRequired($isRequired){
        $this->isRequired = $isRequired;
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
     * @return string
     */
    public function getType(){
        return $this->fieldType;
    }

    public function setValue($value){
        $this->value = $value;
    }

    public function getHint(){
        return $this->hint;
    }

    /**
     * Get validation error
     * @return string
     */
    public function getError(){
        return $this->error;
    }

    /**
     * set validation error
     * @param string $error
     */
    public function setError($error){
        $this->error = $error;
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
     * hidden input
     * @return string
     */
    public function getHTML(){
        return '';
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
     * @param boolean $withMultiple
     * @return string
     */
    public function getInputName($langID = null, $withMultiple = true){
        $fieldName = $this->fieldName;
        $multiple = $this->isMultiple !== false && $withMultiple ? '[]' : '';
        if( $langID == null ){
            return $fieldName . $multiple;
        }else{
            return $fieldName . '_' .$langID . $multiple;
        }
    }

    /**
     * Value of the input for post value
     * @param int|null $langID
     * @return string
     */
    protected function getInputValue($langID = null){
        $postName = $this->getInputName($langID, false);
        if( array_key_exists($postName, $_POST) ){
            return $this->getPost($postName);
        }else{
            if( !empty($this->value) ){
                if( $langID == null && !is_array($this->value) ){
                    return $this->value;
                }else{
                    if( $langID == null ){
                        $keys = array_keys($this->value);
                        return $this->value[$keys[0]];
                    }else{
                        if( is_array($this->value) ){
                            if( array_key_exists('lang_'.$langID, $this->value) ){
                                return $this->value['lang_'.$langID];
                            }else{
                                return '';
                            }
                        }
                        return $this->value;
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
        $postName = $this->getInputName($langID, false);
        $value = $this->getPost($postName);

        if( $value ){
            return $value;
        }else{
            if( $this->isRequired() ){
                return '';
            }
        }
        return null;
    }

    protected function getPost($postName, $default = ''){
        if( isset($_POST[$postName]) ){
            if( $this->isMultiple === false ){
                return $_POST[$postName];
            }else{
                if( $_POST[$postName] && isset($_POST[$postName][ $this->isMultiple ]) ){
                    return $_POST[$postName][ $this->isMultiple ];
                }else{
                    return null;
                }
            }
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

    /**
     * save extra info
     * @param int $itemID
     * @param null $langID
     * @return boolean     error
     */
    abstract public function save($itemID, $langID = null);

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
        $name = $span = '';
        $extraClass = '';
        if( $language != null ){
            $name = $language['name'];
            $extraClass = ($language['id'] == null) ? '' : 'lang_'.$language['id'];
        }
        if( !empty($this->hint) ){
            $span .= '<span class="help-block">'.$this->hint.'</span>';
        }
        if( $this->error ){
            $extraClass = ' has-error';
            $span .= '<span class="help-block"><i class="fa fa-times-circle-o"></i> '.$this->error.'</span>';
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
     * @param string $extra
     * @return string
     */
    protected function inputType($type = 'text', $langID = null, $extra = ''){
        $postName = $this->getInputName($langID);
        $value = str_replace('"', '&quot;', $this->getInputValue($langID));
        return '<input type="'.$type.'" class="form-control" id="'.$postName.'" name="'.$postName.'" placeholder="'.$this->getPlaceholder().'" value="'.$value.'" ' . $extra . ' />';
    }

}