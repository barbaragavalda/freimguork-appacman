<?php

namespace Appacman\Model\Form;

use PDO;

class Check extends FormInput
{

    protected int $type = PDO::PARAM_BOOL;

    public function getSeeValue(?int $langID = null): string
    {
        $value = parent::getSeeValue($langID);
        if ($value) {
            return _('Sí');
        }
        return _('No');
    }

    protected function getInputHTML(?int $langID = null): string
    {
        $postName = $this->getInputName($langID);

        $disabled = $checked = '';
        if (parent::getInputValue($langID)) {
            $disabled = 'disabled';
            $checked  = 'checked';
        }
        return "
            <input type='hidden' id='$postName' name='$postName' value='0' $disabled/>
            <input type='checkbox' class='custom-check' id='$postName' name='$postName' placeholder='{$this->getPlaceholder()}' $checked value='1'/>
        ";
    }

    protected function getPostValue(?int $langID = null): string
    {
        $post = parent::getPostValue($langID);
        if (empty($post)) {
            return false;
        }
        return true;
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