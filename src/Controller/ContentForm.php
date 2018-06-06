<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\Utils\Permissions;
use Core\Model\Push\Base;
use Core\Utils\Session;

class ContentForm extends BaseForm {

    protected function hasErrors(){
        return false;
    }

}