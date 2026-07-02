<?php

namespace Appacman\Model\Form;

class SelectMultiOrder extends SelectMulti
{

    protected function getOptions(?string $table = null, string $extraFields = ''): array
    {
        return $this->loadOptions($this->lateralTable, '', '`order`');
    }

}