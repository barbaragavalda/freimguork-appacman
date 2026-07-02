<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\StringUtils;

class Uri extends FormInput
{

    protected bool $isVisible = false;

    protected function getInputHTML(?int $langID = null): string
    {
        return $this->inputType('hidden', $langID);
    }

    protected function getPostValue(?int $langID = null, bool $isHidden = true): string
    {
        $postValue = ($langID == null) ? $_POST['name'] : $_POST[ 'name_' . $langID ];
        if (!$isHidden) {
            $postValue = parent::getPostValue($langID);
        }
        $noTags = strip_tags($postValue);
        return urlencode(StringUtils::removeSpecialCharacters($noTags, true, false));
    }

    public function hasError(?int $langID = null): bool|string
    {
        return false;
    }

    public function save(int $itemID, ?int $langID = null): bool
    {
        return false;
    }

}
