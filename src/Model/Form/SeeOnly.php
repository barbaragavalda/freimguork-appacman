<?php

namespace Appacman\Model\Form;

use PDO;

class SeeOnly extends FormInput
{

    protected function getInputHTML(?int $langID = null): string
    {
        $this->selectSeeValue();
        $this->multiSeeValue($langID);

        return $this->getSeeValue($langID);
    }

    public function getSeeValue(?int $langID = null): array|string
    {
        $this->selectSeeValue();
        return $this->getInputValue($langID);
    }

    protected function selectSeeValue(): void
    {
        if (str_starts_with($this->fieldName, 'id_')) {
            $table = str_replace('id_', '', $this->fieldName);
            if ($this->mysql->tableExists($table)) {
                $tableName = '';
                if ($this->mysql->fieldExists($table, 'name')) {
                    $tableName = $table;
                } else {
                    if ($this->mysql->fieldExists($table . '_lang', 'name')) {
                        $tableName = $table . '_lang';
                    }
                }

                $sql    = '
                    SELECT t.name 
                    FROM ' . $tableName . ' AS t 
                    WHERE t.id_' . $table . ' = :id
                ';
                $params = array(
                    'id' => array('value' => $this->value, 'type' => PDO::PARAM_INT)
                );
                $value  = $this->mysql->query($sql, $params);
                if (count($value)) {
                    $this->value = $value[0]['name'];
                }
            }
        }
    }

    private function multiSeeValue(?int $langID): void
    {
        if ($this->fieldType == 'selectMulti') {
            $multi       = new SelectMulti(
                array(
                    'field_name' => $this->fieldName,
                    'name'       => $this->fieldName,
                    'type'       => $this->fieldType,
                    'required'   => true,
                    'value'      => $this->value
                ), $this->id, 'product'
            );
            $this->value = $multi->getSeeValue($langID);
        }
    }

    public function canSave(?int $langID = null): bool
    {
        return false;
    }

    public function hasError(?int $langID = null): bool
    {
        return false;
    }

    public function save(int $itemID, ?int $langID = null): bool
    {
        return false;
    }

}