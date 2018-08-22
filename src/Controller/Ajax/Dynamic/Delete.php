<?php

namespace Appacman\Controller\Ajax\Dynamic;

use Appacman\Controller\Ajax\Ajax;
use Appacman\Model\Form\Dynamic;
use Appacman\Model\Utils\Language;
use Appacman\Model\Utils\Permissions;

class Delete extends Ajax {

    public function __construct(){
        parent::__construct();

        $this->permission = Permissions::DELETE;
    }

    protected function run(){
        $field = new Dynamic(
            array(
                'field_name' => $_POST['field'],
                'name' => '',
                'value' => '',
                'required' => false,
                'type' => 'dynamic'
            ),
            null,
            $_POST['table']
        );

        $success = $field->deleteWithID(array(array('id' => $_POST['id'])));
        $this->setError( !$success );

        $this->json();
    }

}