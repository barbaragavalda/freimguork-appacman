<?php

namespace Appacman\Model\Lists;

class Cart extends Table {

    public function get(){
        return $this->content->get(null, 'id_cart_state <> 1');
    }

}