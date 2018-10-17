<?php

namespace Appacman\Model\Lists;

use Core\Model\Model;

class Table extends Model {

    /**
     * @var \Appacman\Model\Content $content
     */
    private $content = null;

    public function __construct($content){
        parent::__construct();

        $this->content = $content;
    }

    public function get(){
        return $this->content->get();
    }

}