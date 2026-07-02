<?php

namespace Appacman\Model\Form;

use PDO;

class UriCustom extends Uri
{

    protected bool $isVisible = true;

    protected function getInputHTML(?int $langID = null): string
    {
        return $this->inputType('text', $langID);
    }

    protected function getPostValue(?int $langID = null, bool $isHidden = true): string
    {
        return parent::getPostValue($langID, false);
    }

    public function hasError(?int $langID = null): bool|string
    {
        $table = $this->table;
        if ($this->onLangTable) {
            $table = $this->table . '_lang';
        }

        $params = array(
            'value' => array('value' => $this->getPostValue($langID, false), 'type' => PDO::PARAM_STR)
        );
        $sql    = '
            SELECT *
            FROM ' . $table . '
            WHERE ' . $this->fieldName . ' = :value
        ';
        if ($this->id) {
            $sql          .= ' AND id_' . $this->table . ' <> :id';
            $params['id'] = array('value' => $this->id, 'type' => PDO::PARAM_INT);
        }
        $exists = $this->mysql->query($sql, $params);
        if (count($exists)) {
            return _('Esta URL ya existe, prueba con otra.');
        }
        return false;
    }

}
