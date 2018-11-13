<?php

namespace Appacman\Model\Form;

class ColorPicker extends Varchar {

    /**
     * remove tags on list
     * @param null $langID
     * @return string
     */
    public function getListValue($langID = null){
        $value = $this->getSeeValue($langID);
        return '<small class="list-colorpicker label" style="background-color: ' . $value . '">&nbsp;</small>';
    }

    /**
     * input type text
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return '
            <div class="input-group input-colorpicker">
                ' . $this->inputType('text', $langID) . '
                <div class="input-group-addon"><i></i></div>
            </div>
        ';
    }

}