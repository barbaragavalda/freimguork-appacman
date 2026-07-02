<?php

namespace Appacman\Model\Form;

class Unmodifiable extends FormInput
{

    protected bool $isVisible = false;

    protected function getInputHTML(?int $langID = null): string
    {
        return '';
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