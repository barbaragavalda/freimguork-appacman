<?php

namespace Appacman\Model\Form;

class SelectPush extends FormInput
{

    protected function getInputHTML(?int $langID = null): string
    {
        return $this->renderTemplate('select-push', array(
            'fieldName'   => $this->fieldName,
            'placeholder' => _('Selecciona') . ' ' . $this->getPlaceholder(),
            'optionsHTML' => $this->getOptionsHTML(),
        ));
    }

    protected function getOptionsHTML(): string
    {
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

        $optionData = array();
        foreach ($options as $option) {
            $optionData[] = array(
                'id'       => $option['value'],
                'name'     => $option['name'],
                'selected' => in_array($option['value'], $values) !== false,
                'disabled' => false,
            );
        }

        return $this->renderTemplate('_select-options', array('options' => $optionData, 'includeBlank' => false));
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