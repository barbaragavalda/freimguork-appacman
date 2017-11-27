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
     * TODO: datepicker
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

        return $this->inputType('text', $langID);
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

}