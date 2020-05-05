<?php

namespace Appacman\Model\Lists;

class Cart extends Table {

    public function initAll(){
        $list = $this->content->get(null, 'id_cart_state <> 1');
        $this->items = $list['rows'];
        $this->fields = $list['fields'];
    }

}