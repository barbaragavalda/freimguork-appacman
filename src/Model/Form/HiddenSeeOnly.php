<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\StringUtils;

class HiddenSeeOnly extends SeeOnly
{

    protected function getInputHTML(?int $langID = null): string
    {
        $html = parent::getInputHTML($langID);
        $html .= $this->inputType('hidden');

        return $html;
    }

}