<?php

namespace Appacman\Model\Form;

class SelectPush extends FormInput {

    /**
     * select simple (only one option)
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return '
            <select name="'.$this->fieldName.'[]"  class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="'.gettext('Selecciona').' '.$this->getPlaceholder().'" style="width: 100%;" tabindex="-1" aria-hidden="true">
                ' . $this->getOptionsHTML() . '
            </select>
        ';
    }

    /**
     * @return string
     */
    protected function getOptionsHTML(){
        $optionsHTML = '';

        $name = 'DISTINCT(' . $this->fieldName . ')';
        if( $this->fieldName == 'os_version' ) $name = 'DISTINCT(CONCAT(' . $this->fieldName . ', " (", platform, ")"))';
        $sql = '
            SELECT ' . $name . ' AS name, ' . $this->fieldName . ' AS value
            FROM appacman_push_device
            ORDER BY name ASC
        ';
        $options = $this->mysql->query($sql);
        $values = $this->loadValues();
        foreach($options as $option){
            $selected = ( in_array($option['value'], $values) !== false ) ? 'selected' : '';
            $optionsHTML .= '<option value="' . $option['value'] . '" '.$selected.'>' . $option['name'] . '</option>';
        }

        return $optionsHTML;
    }


    /**
     * get selected options
     * @return array
     */
    protected function loadValues(){
        $value = array();
        if( isset($_POST[ $this->getInputName() ]) ){
            $value = $_POST[ $this->getInputName() ];
        }else{
            $value = explode(',', $this->value);
        }
        return $value;
    }

    /**
     * post value
     * @param int|null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        if( isset($_POST[ $this->getInputName($langID) ]) ){
            $values = $_POST[ $this->getInputName($langID) ];
            foreach($values as &$value) $value = '' . $value . '';
            return implode(',', $values);
        }
        return '';
    }

    /**
     * Check if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $postValue = $this->getPostValue($langID);
        if( $postValue == null && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }
        return false;
    }

    public function save($itemID, $langID = null){
        return false;
    }

}