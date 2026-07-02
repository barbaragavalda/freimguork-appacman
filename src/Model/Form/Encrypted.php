<?php

namespace Appacman\Model\Form;

abstract class Encrypted extends FormInput
{

    protected string $key = '';

    public function __construct(array $info, ?int $id, ?string $table)
    {
        parent::__construct($info, $id, $table);

        $keyID      = 0;
        $keyCreated = null;
        if ($id) {
            $sql    = '
                SELECT *
                FROM ' . $this->table . '
                WHERE id_' . $this->table . ' = :id
            ';
            $params = array(
                'id' => array('value' => $this->id, 'type' => \PDO::PARAM_INT)
            );
            $row    = $this->mysql->query($sql, $params);
            if (count($row)) {
                $row        = $row[0];
                $keyID      = $row[ 'id_' . $this->table ];
                $keyCreated = $row['created'];
            }
        } else {
            $keyID = $this->mysql->getMaxId($table);
            if (isset($_POST['created'])) {
                $keyCreated = $_POST['created'];
            }
        }
        $this->key = $keyID . '_' . $keyCreated . '_' . $this->fieldName;
    }

    public function hasError(?int $langID = null): bool|string
    {
        $postValue = parent::getPostValue($langID);
        if (empty($postValue) && $this->isRequired) {
            return _('Campo obligatorio.');
        }
        return false;
    }

    public function save(int $itemID, ?int $langID = null): bool
    {
        return false;
    }

}