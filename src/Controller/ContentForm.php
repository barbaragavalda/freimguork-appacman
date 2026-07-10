<?php

namespace Appacman\Controller;

use Core\Routing\Attribute\Route;

#[Route('/formulario/{contentID}', methods: ['GET', 'POST'])]
#[Route('/formulario/{contentID}/{itemID}', methods: ['GET', 'POST'])]
class ContentForm extends BaseContentForm
{

    protected function hasErrors(): bool
    {
        return $this->item->getError();
    }

}