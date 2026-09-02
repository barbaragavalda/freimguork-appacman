<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;
use PDO;

class Select extends FormInput
{

    private static array $optionsCache = array();

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
        return $this->renderTemplate('select', array(
            'postName'    => $this->getInputName($langID),
            'placeholder' => _('Selecciona') . ' ' . $this->getPlaceholder(),
            'optionsHTML' => $this->getOptionsHTML($langID),
        ));
    }

    protected function getOptionsHTML(?int $langID): string|array
    {
        $options = $this->getOptions();
        $values  = $this->loadValues($langID);

        $optionData = array();
        foreach ($options as $option) {
            $name = $option['name'];
            if (array_key_exists('created', $option)) {
                $hash = $option['id'] . '_' . $option['created'] . '_name';
                $name = TwoWay::decrypt($option['name'], $hash);
            }
            $optionData[] = array(
                'id'       => $option['id'],
                'name'     => $name,
                'selected' => in_array($option['id'], $values) !== false,
                'disabled' => array_key_exists('disabled', $option) && $option['disabled'],
            );
        }

        return $this->renderTemplate('_select-options', array('options' => $optionData));
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
        // the result only depends on ($lateralTable, $extraFields, $orderBy, $this->langID),
        // all fixed for the whole request - a fresh Select instance per row/field otherwise
        // re-ran this identical query once per row (see Model\Lists\Table::prepare())
        $cacheKey = $lateralTable . '|' . $extraFields . '|' . $orderBy . '|' . $this->langID;
        if (array_key_exists($cacheKey, self::$optionsCache)) {
            return self::$optionsCache[ $cacheKey ];
        }

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

        self::$optionsCache[ $cacheKey ] = $this->mysql->query($sql, $params);
        return self::$optionsCache[ $cacheKey ];
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