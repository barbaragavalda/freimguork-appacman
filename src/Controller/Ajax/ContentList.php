<?php

namespace Appacman\Controller\Ajax;

use Core\Routing\Attribute\Route;

#[Route('/table/{contentID}', methods: ['GET', 'POST'])]
class ContentList extends BaseContentList
{

    protected function extraFields($list): array
    {
        return $list;
    }

}