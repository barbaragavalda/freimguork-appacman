<?php

namespace Appacman\Model\Form;

class Link extends FormInput
{

    public function getSeeValue(?int $langID = null): string
    {
        $value = parent::getInputValue($langID);
        if ($value) {
            return '<a href="' . $value . '" target="_blank">' . $value . '</a>';
        }
        return '';
    }

    protected function getInputHTML(?int $langID = null): string
    {
        return $this->inputType('text', $langID);
    }

    public function hasError(?int $langID = null): bool|string
    {
        $postValue = $this->getPostValue($langID);
        if (!empty($postValue) && !filter_var($postValue, FILTER_VALIDATE_URL)) {
            return _('Comprueba el formato del link: que empieze por http:// o https://.');
        }
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