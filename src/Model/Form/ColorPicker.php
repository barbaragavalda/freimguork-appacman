<?php

namespace Appacman\Model\Form;

class ColorPicker extends Varchar
{

    public function getListValue(?int $langID = null): string
    {
        $value = $this->getSeeValue($langID);
        return '<small class="list-colorpicker label" style="background-color: ' . $value . '">&nbsp;</small>';
    }

    protected function getInputHTML(?int $langID = null): string
    {
        return '
            <div class="input-group input-colorpicker">
                ' . $this->inputType('text', $langID) . '
                <div class="input-group-addon"><i></i></div>
            </div>
        ';
    }

}