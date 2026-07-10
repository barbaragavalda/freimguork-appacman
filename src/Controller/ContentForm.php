<?php

namespace Appacman\Controller;

use Core\Routing\Attribute\Route;

#[Route('/formulario/{contentID}')]
#[Route('/formulario/{contentID}/{itemID}')]
class ContentForm extends BaseContentForm
{

    protected function hasErrors(): bool
    {
        return $this->item->getError();
    }

}