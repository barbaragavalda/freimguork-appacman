<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;

class SelectMultiEncryptedTwoWay extends SelectMulti
{

    protected function getOptions(?string $table = null, string $extraFields = ''): array
    {
        return $this->loadOptions($this->lateralTable, ', created');
    }

    public function getSeeValue(?int $langID = null): string
    {
        $options = $this->getOptions();
        $values  = $this->loadValues($langID);
        if (count($values)) {
            $value = array();
            foreach ($options as $option) {
                if (in_array($option['id'], $values)) {
                    $key     = $option['id'] . '_' . $option['created'] . '_';
                    $value[] = TwoWay::decrypt($option['name'], $key . 'name');
                }
            }
            return implode(', ', $value);
        }
        return '-';
    }

}