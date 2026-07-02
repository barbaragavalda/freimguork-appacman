<?php

namespace Appacman\Model\Form;

class CheckSeeOnly extends SeeOnly
{

    public function getListValue(): string
    {
        $value = parent::getSeeValue();
        if ($value) {
            return _('Sí');
        }
        return _('No');
    }

    public function getSeeValue(?int $langID = null): string
    {
        return $this->getListValue();
    }

    protected function getInputHTML(?int $langID = null): string
    {
        return $this->getListValue();
    }

}
