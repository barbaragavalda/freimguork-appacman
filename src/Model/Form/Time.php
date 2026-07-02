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
        $postName = $this->getInputName($langID);
        return '
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-clock-o"></i>
                </div>
                <input type="text" class="form-control timepicker" id="'
            . $postName
            . '" name="'
            . $postName
            . '" placeholder="'
            . $this->getPlaceholder()
            . '" value="'
            . $this->getSeeValue($langID)
            . '">
            </div>
        ';
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