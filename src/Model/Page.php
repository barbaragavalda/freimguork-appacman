<?php

namespace Appacman\Model;

use Appacman\Model\Utils\Field;
use Core\Model\Model;

abstract class Page extends Model
{

    protected string $name = '';

    protected array $info = array();

    protected ?Field $fields = null;

    protected string $table = '';

    public function __construct($id)
    {
        parent::__construct();

        $this->id = $id;
    }

    public function getValues(): array
    {
        return $this->info;
    }

    protected function initFields($tableName, $contentID = null): void
    {
        $this->fields = new Field($tableName, $contentID);
    }

    public function getInputClass(&$field, $info = null): object
    {
        $info = ($info == null) ? $this->info : $info;
        // field value
        $fieldName      = $field['field_name'];
        $field['value'] = '';
        if (array_key_exists($fieldName, $info)) {
            $field['value'] = $info[ $fieldName ];
        }

        // input view class
        if (str_contains($field['type'], ' ')) {
            $explode = explode(' ', $field['type']);
            if (count($explode) > 0) {
                $field['type'] = $explode[0];
            }
        }
        $inputClass = 'Appacman\\Model\\Form\\' . ucfirst($field['type']);
        $id         = null;
        if (count($info)) {
            if (is_a($this, 'Appacman\\Model\\Item')) {
                $id = $info[ 'id_' . $this->table ];
            } else {
                if (array_key_exists('id', $info)) {
                    $id = $info['id'];
                }
            }
        }
        return new $inputClass($field, $id, $this->table);
    }

    abstract public function getName(): string;

    /**
     * get the formulari for that item
     * @return array
     */
    abstract public function get(): array;

    /**
     * check if this item exists
     * @return bool
     */
    abstract public function exists(): bool;

}