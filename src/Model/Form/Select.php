<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;
use PDO;

class Select extends FormInput
{

    public function getSeeValue(?int $langID = null): string
    {
        if ($this->value) {
            $options = $this->getOptions();
            foreach ($options as $option) {
                if ($option['id'] == $this->value) {
                    return $option['name'];
                }
            }
        }
        return '-';
    }

    protected function getInputHTML(?int $langID = null): string
    {
        return '
            <select name="'
            . $this->getInputName($langID)
            . '" class="deepLink form-control select2 select2-hidden-accessible" data-placeholder="'
            . _('Selecciona')
            . ' '
            . $this->getPlaceholder()
            . '" style="width: 100%;" tabindex="-1" aria-hidden="true">
                '
            . $this->getOptionsHTML($langID)
            . '
            </select>
        ';
    }

    protected function getOptionsHTML(?int $langID): string|array
    {
        $optionsHTML = '';
        $options     = $this->getOptions();
        $values      = $this->loadValues($langID);

        $optionsHTML .= '<option></option>';
        foreach ($options as $option) {
            $selected = in_array($option['id'], $values) !== false ? 'selected' : '';
            $disabled = (array_key_exists('disabled', $option) && $option['disabled']) ? 'disabled' : '';
            $name     = $option['name'];
            if (array_key_exists('created', $option)) {
                $hash = $option['id'] . '_' . $option['created'] . '_name';
                $name = TwoWay::decrypt($option['name'], $hash);
            }
            $optionsHTML .= '<option value="'
                . $option['id']
                . '" '
                . $selected
                . ' '
                . $disabled
                . '>'
                . $name
                . '</option>';
        }

        return $optionsHTML;
    }

    protected function getOptions(?string $table = null, string $extraFields = ''): array
    {
        $lateralTable = $table;
        if ($lateralTable == null) {
            $lateralTable = str_replace('id_', '', $this->fieldName);
        }
        if (!$this->mysql->tableExists($lateralTable)) {
            $lateralTable = str_replace('_related', '', $lateralTable);
        }
        return $this->loadOptions($lateralTable, $extraFields);
    }

    protected function loadOptions(string $lateralTable, string $extraFields = '', string $orderBy = 'name'): array
    {
        $lateralTableLang = $lateralTable . '_lang';

        $params    = array();
        $where     = '';
        $innerJoin = '';

        if ($this->mysql->tableExists($lateralTableLang)) {
            $innerJoin      = "INNER JOIN $lateralTableLang ON $lateralTableLang.id_$lateralTable = $lateralTable.id_$lateralTable AND $lateralTableLang.id_appacman_lang = :lang";
            $params['lang'] = array('value' => $this->langID, 'type' => PDO::PARAM_INT);
        }
        $sql = '
            SELECT ' . $lateralTable . '.id_' . $lateralTable . ' AS id, name ' . $extraFields . '
            FROM ' . $lateralTable . '
            ' . $innerJoin . '
            ' . $where . '
            ORDER BY ' . $orderBy . ' ASC
        ';
        return $this->mysql->query($sql, $params);
    }

    protected function loadValues(?int $langID): array
    {
        $values    = array($this->value);
        $postValue = $this->getPostValue($langID);
        if ($postValue) {
            $values = array($postValue);
        }
        return $values;
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