<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\Ajax\Ajax;
use Appacman\Model\Push\Notifier;
use Appacman\Model\Utils\Permissions;

class Target extends Ajax {

    public function __construct(){
        parent::__construct();

        $this->permission = Permissions::EDIT;
    }

    protected function run(){
        $this->removeInfo();

        $notifier = new Notifier();
        $this->assign('target', $notifier->getTarget($_POST));
        $this->json();
    }

}