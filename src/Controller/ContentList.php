<?php

namespace Appacman\Controller;

use Appacman\Model\Utils\Permissions;

class ContentList extends BaseContentList {

    protected function extraHeaders(){
        return array();
    }

    protected function extraFields($list){
        return $list;
    }

}