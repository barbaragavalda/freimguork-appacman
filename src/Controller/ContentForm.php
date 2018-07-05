<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\Utils\Permissions;
use Core\Model\Push\Base;
use Core\Utils\Session;

class ContentForm extends BaseContentForm {

    protected function hasErrors(){
        return $this->item->getError();
    }

}