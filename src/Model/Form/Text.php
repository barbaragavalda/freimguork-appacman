<?php

namespace Appacman\Model\Form;

class Text extends FormInput
{

    protected string $class = 'wysiwyg-textarea';

    public function getListValue(?int $langID = null): string
    {
        return strip_tags(parent::getListValue());
    }

    public function getSeeValue(?int $langID = null): string
    {
        return strip_tags(parent::getSeeValue($langID));
    }

    protected function getInputHTML(?int $langID = null): string
    {
        return '
            <div class="'
            . $this->class
            . '">
                <textarea id="'
            . $this->getInputName($langID)
            . '" name="'
            . $this->getInputName($langID)
            . '" placeholder="'
            . $this->getPlaceholder()
            . '">'
            . parent::getInputValue($langID)
            . '</textarea>
            </div>
        ';
    }

    public function hasError(?int $langID = null): bool|string
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