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
        return $this->renderTemplate('colorpicker', array(
            'input' => $this->inputType('text', $langID),
        ));
    }

}