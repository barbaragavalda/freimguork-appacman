<?php

namespace Appacman\Model\Utils;

use Core\Model\Model;
use PDO;

class Language extends Model
{

    public function get($id = null): array
    {
        $where  = '';
        $params = array();
        if ($id != null) {
            $where  = 'WHERE id_appacman_lang = :id';
            $params = array('id' => array('value' => $id, 'type' => PDO::PARAM_INT));
        }

        $sql       = "
            SELECT id_appacman_lang AS id, name, culture
            FROM appacman_lang
            $where
            ORDER BY `order` ASC
        ";
        $languages = $this->mysql->query($sql, $params);

        if (count($languages)) {
            if ($id != null) {
                return $languages[0];
            }
            return $languages;
        }
        return array();
    }

}