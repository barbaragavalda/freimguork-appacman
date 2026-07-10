<?php

namespace Appacman\Controller\LoggedOut;

use Appacman\Controller\AppacmanController;
use Core\Routing\Attribute\Route;

#[Route('/cerrar-sesion', methods: ['GET', 'POST'])]
class LogOut extends AppacmanController
{

    protected function run(): void
    {
        $this->user->logout();
        $this->redirect($this->domain . _('iniciar-sesion'));
    }

    protected function hasPermission(): bool
    {
        return true;
    }

    protected function getTitle(): string
    {
        return _('Cerrar sesión');
    }

    protected function getBreadcrumb(): array
    {
        return array();
    }

}