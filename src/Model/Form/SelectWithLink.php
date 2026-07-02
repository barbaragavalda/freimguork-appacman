<?php

namespace Appacman\Model\Form;

use Core\Utils\Config;

class SelectWithLink extends Select
{

    public function getSeeValue(?int $langID = null): string
    {
        $seeValue = parent::getSeeValue($langID);
        if (!empty($seeValue)) {
            return $this->getLink($this->getLinkName($seeValue));
        }
        return $seeValue;
    }

    protected function getInputHTML(?int $langID = null): string
    {
        $input = parent::getInputHTML($langID);
        $name  = $this->getLinkName('(' . _('ver') . ' ' . strtolower($this->name) . ')');
        return $input . '<br>' . $this->getLink($name);
    }

    protected function getLinkName(string $name): string
    {
        return $name;
    }

    private function getLink(string $value): string
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