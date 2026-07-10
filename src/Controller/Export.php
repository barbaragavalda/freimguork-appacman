<?php

namespace Appacman\Controller;

use Core\Routing\Attribute\Route;

#[Route('/exportar/{contentID}', methods: ['GET', 'POST'])]
class Export extends BaseExport
{

    protected function addExtraInfo($list): array
    {
        return $list;
    }

}