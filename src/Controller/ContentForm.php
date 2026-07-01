<?php

namespace Appacman\Controller;

class ContentForm extends BaseContentForm
{

    protected function hasErrors(): bool
    {
        return $this->item->getError();
    }

}