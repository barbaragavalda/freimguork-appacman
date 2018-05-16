<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;

class DeleteItem extends Ajax {

    public function __construct(){
        parent::__construct();

        $this->permission = Permissions::DELETE;
    }

    protected function run(){
        $success = $this->item->delete();
        $this->setError( !$success );
    }

}