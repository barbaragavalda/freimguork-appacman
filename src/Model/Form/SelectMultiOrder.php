<?php

namespace Appacman\Model\Form;

class SelectMultiOrder extends SelectMulti
{

    /**
     * from witch table has to load options?
     * @return array
     */
    protected function getOptions($table = null, $extraFields = '')
    {
        return $this->loadOptions($this->lateralTable, '', '`order`');
    }

}