<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\DateUtils;

class Date extends FormInput {

    /**
     * show like database in order to sort it correctly
     * @return mixed|string
     */
    public function getListValue(){
        return parent::getSeeValue();
    }

    /**
     * format date for user
     * @param int|null $langID
     * @return string
     */
    public function getSeeValue($langID = null){
        $value = parent::getSeeValue($langID);
        return DateUtils::userDate($value);
    }

    /**
     * datepicker input
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $postName = $this->getInputName($langID);
        return '
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control datepicker" id="'.$postName.'" name="'.$postName.'" placeholder="'.$this->getName().'" value="'.$this->getSeeValue($langID).'">
            </div>
        ';
    }

    /**
     * format date for database
     * @param int|null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        $value = parent::getPostValue($langID);
        return DateUtils::databaseDate($value);
    }

    /**
     * Check date format and if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $value = parent::getPostValue($langID);
        $postValue = $this->getPostValue($langID);
        if( !empty($value) && preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/', $value) == false ){
            return str_replace('%format%', 'dd/mm/yyyy', gettext('Comprueba que sea una fecha correcta con el formato %format%.'));
        }
        if( $postValue == null && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }
        return false;
    }

}