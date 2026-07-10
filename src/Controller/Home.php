<?php
namespace Appacman\Controller;

use Core\Routing\Attribute\Route;

#[Route('/', methods: ['GET', 'POST'])]
class Home extends AppacmanController
{

    protected function run(): void
    {
        $this->template('home.twig');
    }

    protected function hasPermission(): bool
    {
        return $this->user->loggedIn();
    }

    protected function getTitle(): string
    {
        return _('Inicio');
    }

    protected function getBreadcrumb(): array
    {
        return array();
    }

}