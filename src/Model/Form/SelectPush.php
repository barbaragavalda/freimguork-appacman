<?php

namespace Appacman\Model\Form;

class SelectPush extends FormInput
{

    protected function getInputHTML(?int $langID = null): string
    {
        return '
            <select name="'
            . $this->fieldName
            . '[]"  class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="'
            . _('Selecciona')
            . ' '
            . $this->getPlaceholder()
            . '" style="width: 100%;" tabindex="-1" aria-hidden="true">
                '
            . $this->getOptionsHTML()
            . '
            </select>
        ';
    }

    protected function getOptionsHTML(): string
    {
        $optionsHTML = '';

        $name = 'DISTINCT(' . $this->fieldName . ')';
        if ($this->fieldName == 'os_version') {
            $name = 'DISTINCT(CONCAT(' . $this->fieldName . ', " (", platform, ")"))';
        }
        $sql     = '
            SELECT ' . $name . ' AS name, ' . $this->fieldName . ' AS value
            FROM appacman_push_device
            ORDER BY name ASC
        ';
        $options = $this->mysql->query($sql);
        $values  = $this->loadValues();
        foreach ($options as $option) {
            $selected    = (in_array($option['value'], $values) !== false) ? 'selected' : '';
            $optionsHTML .= '<option value="'
                . $option['value']
                . '" '
                . $selected
                . '>'
                . $option['name']
                . '</option>';
        }

        return $optionsHTML;
    }

    protected function loadValues(): array
    {
        return $_POST[ $this->getInputName() ] ?? explode(',', $this->value);
    }

    protected function getPostValue(?int $langID = null): string
    {
        if (isset($_POST[ $this->getInputName($langID) ])) {
            $values = $_POST[ $this->getInputName($langID) ];
            foreach ($values as &$value) {
                $value = '' . $value;
            }
            return implode(',', $values);
        }
        return '';
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