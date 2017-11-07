<?php

namespace Appacman\Controller;

class DefaultController extends AppacmanController {

	// 404 error
    protected function run(){
        $this->template('default_template.twig');
    }

    protected function getTitle(){
        return 'Appacman';
    }

    protected function getBreadcrumb(){
        return array();
    }

}