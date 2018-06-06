<?php

namespace Appacman\Model\Form;

class DateTime extends Timestamp {

    const EMPTY_DATETIME = '0000-00-00 00:00:00';

    public function getValue(){
        return $this->checkEmpty($this->value);
    }

    /**
     * datepicker input
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $postName = $this->getInputName($langID);
        $postValue = $this->checkEmpty( $this->getPostValue($langID) );
        return '
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control datetimepicker" id="'.$postName.'" name="'.$postName.'" placeholder="'.$this->getPlaceholder().'" value="'.$postValue.'">
            </div>
        ';
    }

    /**
     * Check timestamp format and if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $value = $this->checkEmpty( parent::getPostValue($langID) );
        if( !empty($value) && preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])$/', $value) == false ){
            return str_replace('%format%', 'yyyy-mm-dd hh:mm:ss', gettext('Comprueba que sea una fecha correcta con el formato %format%.'));
        }
    }

    private function checkEmpty($value){
        return ( $value == self::EMPTY_DATETIME ) ? '' : $value;
    }

}