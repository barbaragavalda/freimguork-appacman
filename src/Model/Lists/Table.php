<?php

namespace Appacman\Model\Lists;

use Core\Model\Model;

class Table extends Model {

    /**
     * @var \Appacman\Model\Content $content
     */
    protected $content = null;

    public function __construct($content, $forMenu = false){
        parent::__construct();

        $this->content = $content;
        $this->content->forMenu($forMenu);
    }

    public function get(){
        return $this->content->get();
    }

}