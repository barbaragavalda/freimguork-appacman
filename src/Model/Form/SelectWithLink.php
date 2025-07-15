<?php

namespace Appacman\Model\Form;

use Appacman\Model\ExtraUser;
use Core\Model\Encryptor\TwoWay;
use Core\Utils\Config;
use Core\Utils\Session;

class SelectWithLink extends Select
{

    public function getSeeValue($langID = null): string
    {
        $seeValue = parent::getSeeValue($langID);
        if (!empty($seeValue)) {
            return $this->getLink($seeValue);
        }
        return $seeValue;
    }

    protected function getInputHTML($langID = null): string
    {
        $input = parent::getInputHTML($langID);
        return $input . '<br>' . $this->getLink('(' . _('ver') . ' ' . strtolower($this->name) . ')');
    }

    private function getLink($value): string
    {
        if ($this->value) {
            $config    = Config::getInstance();
            $contentID = $this->getContentID();
            $link      = $config->getDomain() . 'formulario/' . $contentID . '/' . $this->value;
            return '<a href="' . $link . '" target="_blank">' . $value . '</a>';
        }
        return '';
    }

}