<?php

namespace Appacman\Controller\Ajax\Dynamic;

use Appacman\Controller\Ajax\Ajax;
use Appacman\Model\Form\Dynamic;
use Appacman\Model\Utils\Language;
use Appacman\Model\Utils\Permissions;

class Add extends Ajax {

    public function __construct(){
        parent::__construct();

        $this->permission = Permissions::EDIT;
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
            $_POST['id'],
            $_POST['table']
        );
        $position = null;
        if( $_POST['position'] ) $position = $_POST['position'];

        $languagesModel = new Language();
        $languages = $languagesModel->get();
        $field->setLanguages($languages);

        $this->removeInfo();
        $this->assign('html', $field->getItemHTML(null, true, $position));
        $this->json();
    }

}