<?php

namespace Appacman\Model\Form;

class Time extends FormInput
{

    public function getListValue(): string
    {
        return parent::getSeeValue();
    }

    public function getSeeValue(?int $langID = null): string
    {
        if (isset($_POST['save'])) {
            return $this->getInputValue($langID);
        }
        return parent::getSeeValue($langID);
    }

    /**
     * datepicker input
     *
     * @param int|null $langID
     *
     * @return string
     */
    protected function getInputHTML(?int $langID = null): string
    {
        return $this->renderTemplate('_date-group', array(
            'icon'        => 'fa-clock-o',
            'inputClass'  => 'timepicker',
            'postName'    => $this->getInputName($langID),
            'placeholder' => $this->getPlaceholder(),
            'value'       => $this->getSeeValue($langID),
        ));
    }

    public function hasError(?int $langID = null): bool|string
    {
        $value     = parent::getPostValue($langID);
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