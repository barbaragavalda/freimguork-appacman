<?php

namespace Appacman\Model\Form;

use PDO;

class Number extends FormInput
{

    protected int $type = PDO::PARAM_STR;

    protected function getPostValue(?int $langID = null): mixed
    {
        $postName = $this->getInputName($langID, false);
        $post     = $this->getPost($postName);

        if ($post === '') {
            return null;
        }
        return str_replace(',', '.', $post);
    }

    protected function getInputHTML(?int $langID = null): string
    {
        return $this->inputType('text', $langID);
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