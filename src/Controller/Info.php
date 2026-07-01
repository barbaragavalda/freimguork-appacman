<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\Utils\Language;
use Appacman\Model\Utils\Permissions;
use Core\Utils\Session;

class Info extends AppacmanController
{

    protected function run(): void
    {
        $this->template('info.twig');
    }

    protected function hasPermission(): bool
    {
        return true;
    }

    protected function getTitle(): string
    {
        return _('Información');
    }

    protected function getBreadcrumb(): array
    {
        return array(
            array('name' => _('Información'), 'link' => null)
        );
    }

}