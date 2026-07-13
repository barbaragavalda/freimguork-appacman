<?php

namespace Appacman\Model\Form;

use PDO;

class SelectMulti extends Select
{

    protected string $currentTable = '';

    protected string $lateralTable = '';

    public function __construct(array $info, ?int $id, ?string $table = null)
    {
        parent::__construct($info, $id, $table);

        $this->initTables();
    }

    public function getSeeValue(?int $langID = null): string
    {
        $options = $this->getOptions();
        $values  = $this->loadValues($langID);

        if (count($values)) {
            $value = array();
            foreach ($options as $option) {
                if (in_array($option['id'], $values)) {
                    $value[] = $option['name'];
                }
            }
            return implode(', ', $value);
        }
        return '-';
    }

    protected function getInputHTML(?int $langID = null): string
    {
        return $this->renderTemplate('select-multi', array(
            'showSelectAll'  => $this->isMultiple === false,
            'selectCheck'    => $this->getInputName($langID, false) . '_selectAll',
            'selectAllLabel' => _('Seleccionar todos'),
            'postName'       => $this->getInputName($langID),
            'placeholder'    => _('Selecciona') . ' ' . $this->getPlaceholder(),
            'fieldName'      => $this->fieldName,
            'optionsHTML'    => $this->getOptionsHTML($langID),
        ));
    }

    public function getInputName(?int $langID = null, bool $withMultiple = true): string
    {
        $fieldName = $this->fieldName;
        if ($this->isMultiple !== false) {
            $fieldName .= $this->isMultiple;
        }
        $multiple = $withMultiple ? '[]' : '';
        if ($langID == null) {
            return $fieldName . $multiple;
        } else {
            return $fieldName . '_' . $langID . $multiple;
        }
    }

    private function getLateralField(): string
    {
        if ($this->currentTable == $this->lateralTable) {
            return $this->lateralTable . '_related';
        }
        return $this->lateralTable;
    }

    protected function getOptions(?string $table = null, string $extraFields = ''): array
    {
        return $this->loadOptions($this->lateralTable);
    }

    protected function loadValues(?int $langID): array
    {
        $this->initTables();
        $sql    = '
            SELECT id_' . $this->getLateralField() . ' AS id
            FROM ' . $this->fieldName . '
            WHERE id_' . $this->currentTable . ' = :id
        ';
        $params = array(
            'id' => array('value' => $this->id, 'type' => PDO::PARAM_INT)
        );
        $values = $this->mysql->query($sql, $params);
        return array_column($values, 'id');
    }

    protected function initTables(): void
    {
        $tables             = explode('_', $this->fieldName);
        $this->currentTable = $tables[0];
        $this->lateralTable = substr(strstr($this->fieldName, '_'), 1);

        if (!$this->mysql->tableExists($this->lateralTable)) {
            $first              = strpos($this->fieldName, '_');
            $pos                = strpos($this->fieldName, '_', $first + 1);
            $this->currentTable = substr($this->fieldName, 0, $pos);
            $this->lateralTable = substr($this->fieldName, $pos + 1);
        }
    }

    public function hasError(?int $langID = null): bool
    {
        return false;
    }

    public function canSave(?int $langID = null): bool
    {
        return false;
    }

    public function save(int $itemID, ?int $langID = null): bool
    {
        $postName = $this->getInputName($langID, false);
        if (isset($_POST[ $postName ])) {
            return $this->insert($itemID, $_POST[ $postName ]);
        }
        return false;
    }

    protected function insert(int $itemID, array $ids = array(), bool $deleteOld = true): bool
    {
        $this->initTables();
        $field = $this->getLateralField();

        $old = $this->exists($field, $itemID);

        // insert again
        $values = array();
        $params = array(
            'id' => array('value' => $itemID, 'type' => PDO::PARAM_INT)
        );
        $ids    = array_unique($ids);
        foreach ($ids as $index => $id) {
            $exists = count($this->exists($field, $itemID, $id));
            if ($id && !$exists) {
                $values[]                         = '(:id, :lateral_id_' . $index . ')';
                $params[ 'lateral_id_' . $index ] = array('value' => $id, 'type' => PDO::PARAM_INT);
            }
        }

        $delete = array_diff($old, $ids);
        if (count($delete) && $deleteOld) {
            $sql          = '
                DELETE FROM ' . $this->fieldName . '
                WHERE id_' . $this->currentTable . ' = :item AND id_' . $field . ' IN(' . implode(', ', $delete) . ')
            ';
            $paramsDelete = array(
                'item' => array('value' => $itemID, 'type' => PDO::PARAM_INT)
            );
            $this->mysql->query($sql, $paramsDelete);
        }

        if (count($values)) {
            $sql = '
                INSERT INTO ' . $this->fieldName . ' (id_' . $this->currentTable . ', id_' . $field . ') 
                VALUES ' . implode(',', $values) . '
            ';
            $this->mysql->query($sql, $params);
            if ($this->mysql->getState()) {
                return false;
            }
        }
        return false;
    }

    private function exists(string $field, int $itemID, ?int $id = null): array
    {
        $where  = '';
        $params = array(
            'item' => array('value' => $itemID, 'type' => PDO::PARAM_INT)
        );
        if ($id != null) {
            $where        = ' AND id_' . $field . ' = :id';
            $params['id'] = array('value' => $id, 'type' => PDO::PARAM_INT);
        }

        $sql = '
            SELECT id_' . $field . ' AS id
            FROM ' . $this->fieldName . '
            WHERE id_' . $this->currentTable . ' = :item ' . $where . '
        ';
        $ids = $this->mysql->query($sql, $params);

        if (count($ids)) {
            if ($id == null) {
                return array_column($ids, 'id');
            } else {
                return $ids[0];
            }
        }
        return array();
    }

}