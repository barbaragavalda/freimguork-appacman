<?php

namespace Appacman\Model\Form;

class Varchar extends FormInput
{

    public function getListValue(?int $langID = null): string
    {
        return strip_tags(parent::getListValue($langID));
    }

    protected function getInputHTML(?int $langID = null): string
    {
        return $this->inputType('text', $langID);
    }

    public function hasError(?int $langID = null): bool
    {
        $postValue = $this->getPostValue($langID);
        if ($postValue == null && $this->isRequired) {
            return _('Campo obligatorio.');
        }
        return false;
    }

    public function save(int $itemID, ?int $langID = null): bool
    {
        return false;
    }

}