<?php

namespace Appacman\Model\Form;

class Check extends FormInput {

    protected $type = \PDO::PARAM_BOOL;

    /**
     * See value is 'yes' or 'no'
     * @param null $langID
     * @return string
     */
    public function getSeeValue($langID = null){
        $value = parent::getSeeValue($langID);
        if( $value ){
            return gettext('Sí');
        }
        return gettext('No');
    }

    /**
     * input type chek with custom css
     * @param null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $postName = $this->getInputName($langID);
        $checked = parent::getInputValue($langID) ? 'checked' : '';
        return '
            <input type="checkbox" class="custom-check" id="'.$postName.'" name="'.$postName.'" placeholder="'.$this->getName().'" '.$checked.' value="1">
        ';
    }

    public function canSave($langID = null){
        $postName = $this->getInputName($langID);
        return array_key_exists($postName, $_POST);
    }

    /**
     * Post value is true or false
     * @param null $langID
     * @return bool
     */
    protected function getPostValue($langID = null){
        $post = parent::getPostValue($langID);
        if( empty($post) ){
            return false;
        }
        return true;
    }

}