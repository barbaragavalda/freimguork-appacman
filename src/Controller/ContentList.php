<?php

namespace Appacman\Controller;

use Core\Routing\Attribute\Route;

#[Route('/listado/{contentID}')]
class ContentList extends BaseContentList
{

    protected function extraHeaders(): array
    {
        return array();
    }

}