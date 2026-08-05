<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\DateUtils;

class Date extends FormInput
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
        $value = parent::getSeeValue($langID);
        return DateUtils::userDate($value) ?? '';
    }

    protected function getInputHTML(?int $langID = null): string
    {
        return $this->renderTemplate('_date-group', array(
            'icon'        => 'fa-calendar',
            'inputClass'  => 'datepicker',
            'postName'    => $this->getInputName($langID),
            'placeholder' => $this->getPlaceholder(),
            'value'       => $this->getSeeValue($langID),
        ));
    }

    protected function getPostValue(?int $langID = null): ?string
    {
        $value = parent::getPostValue($langID);
        if ($value === null) {
            return null;
        }
        return DateUtils::databaseDate($value);
    }

    public function hasError(?int $langID = null): bool|string
    {
        $value     = parent::getPostValue($langID);
        $postValue = $this->getPostValue($langID);
        if (!empty($value)
            && !preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[0-9]{4}$/', $value)) {
            return str_replace(
                '%format%',
                'dd/mm/yyyy',
                _('Comprueba que sea una fecha correcta con el formato %format%.')
            );
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