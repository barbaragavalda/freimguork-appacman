<?php

namespace Appacman\Model\Utils;

use Core\Model\Model;

class Language extends Model {

    public function get(){
        $sql = '
            SELECT id_appacman_lang AS id, name
            FROM appacman_lang
            ORDER BY `order` ASC
        ';
        return $this->mysql->query($sql);
    }

}