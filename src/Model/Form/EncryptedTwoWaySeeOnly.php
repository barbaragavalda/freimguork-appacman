<?php

namespace Appacman\Model\Form;

class EncryptedTwoWaySeeOnly extends EncryptedTwoWay
{

    protected function getInputHTML(?int $langID = null): string
    {
        return $this->getSeeValue($langID);
    }

    public function canSave(?int $langID = null): bool
    {
        return false;
    }

    public function hasError(?int $langID = null): bool
    {
        return false;
    }

    public function save(int $itemID, ?int $langID = null): bool
    {
        return false;
    }

}