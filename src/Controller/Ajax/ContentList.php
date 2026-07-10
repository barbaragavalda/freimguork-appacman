<?php

namespace Appacman\Controller\Ajax;

use Core\Routing\Attribute\Route;

#[Route('/table/{contentID}')]
class ContentList extends BaseContentList
{

    protected function extraFields($list): array
    {
        return $list;
    }

}