<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;
use Core\Utils\Config;

class SelectEncryptedTwoWay extends Select
{

    public function getSeeValue(?int $langID = null): string
    {
        if ($this->getPostValue($langID)) {
            $this->value = $this->getPostValue($langID);
        }
        if ($this->value) {
            $options = $this->getOptions();
            foreach ($options as $option) {
                if ($option['id'] == $this->value) {
                    $hash  = $option['id'] . '_' . $option['created'] . '_name';
                    $value = TwoWay::decrypt($option['name'], $hash);

                    $contentID = $this->getContentID();
                    if ($contentID === false) {
                        return $value;
                    } else {
                        $config = Config::getInstance();
                        return '<a href="'
                            . $config->getDomain()
                            . _('formulario')
                            . '/'
                            . $contentID
                            . '/'
                            . $this->value
                            . '">'
                            . $value
                            . '</a>';
                    }
                }
            }
        }
        return '-';
    }

    protected function getOptions(?string $table = null, string $extraFields = ''): array
    {
        $lateralTable = str_replace('id_', '', $this->fieldName);
        return $this->loadOptions($lateralTable, ', created');
    }

}