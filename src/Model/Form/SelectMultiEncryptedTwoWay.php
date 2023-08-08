<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;

class SelectMultiEncryptedTwoWay extends SelectMulti
{

    protected function getOptions($table = null, $extraFields = '')
    {
        return $this->loadOptions($this->lateralTable, ', created');
    }

    public function getSeeValue($langID = null)
    {
        $options = $this->getOptions();
        $values  = $this->loadValues($langID);
        if (count($values)) {
            $value = array();
            foreach ($options as $option) {
                if (in_array($option['id'], $values)) {
                    $key = $option['id'] . '_' . $option['created'] . '_';
                    $value[] = TwoWay::decrypt($option['name'], $key.'name');
                }
            }
            return implode(', ', $value);
        }
        return '-';
    }

}