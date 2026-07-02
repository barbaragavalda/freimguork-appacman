<?php

namespace Appacman\Model\Lists;

class Cart extends Table
{

    public function initAll(): void
    {
        $list         = $this->content->get(null, 'id_cart_state <> 1');
        $this->items  = $list['rows'];
        $this->fields = $list['fields'];
    }

}