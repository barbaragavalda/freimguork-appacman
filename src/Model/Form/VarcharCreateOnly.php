<?php

namespace Appacman\Model\Form;

class VarcharCreateOnly extends Varchar
{

    protected function getInputHTML(?int $langID = null): string
    {
        if ($this->id) {
            return $this->getInputValue($langID) . $this->inputType('hidden', $langID);
        } else {
            return $this->inputType('text', $langID);
        }
    }

}