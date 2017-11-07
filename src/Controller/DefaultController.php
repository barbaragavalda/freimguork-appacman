<?php

namespace Appacman\Controller;

class DefaultController extends AppacmanController {

	// 404 error
    protected function run(){
        $this->template('default_template.twig');
    }

}