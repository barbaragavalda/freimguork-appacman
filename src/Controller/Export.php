<?php

namespace Appacman\Controller;

use Appacman\Model\Utils\Permissions;

class Export extends BaseExport {

    protected function addExtraInfo($list){
        return $list;
    }

}