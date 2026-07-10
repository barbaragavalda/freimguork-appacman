<?php

namespace Appacman\Controller;

use Core\Model\Utils\SessionLog;
use Core\Routing\Attribute\Route;

#[Route('/log-out/{contentID}/{itemID}')]
class ForceLogOut extends Content
{

    protected function run(): void
    {
        parent::run();

        $contentID = $this->getParam('contentID');
        $itemID    = $this->getParam('itemID');

        $log = new SessionLog();
        $log->logOut($itemID);

        $this->redirect($this->domain . _('listado') . '/' . $contentID);
    }

    protected function getTitle(): string
    {
        return '';
    }

    protected function getBreadcrumb(): array
    {
        return array();
    }

}